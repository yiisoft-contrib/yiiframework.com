<?php

namespace app\components;

use DateTimeImmutable;
use RuntimeException;

/** Maintainer-side aggregation and presentation of the approved sponsor snapshot. */
class Sponsors
{
    /**
     * Read the private export locally. Names and transaction details are never returned.
     * The Open Collective export uses empty reversal fields for original, unreversed rows.
     */
    public static function readContributions(string $path): array
    {
        $file = fopen($path, 'r');
        if ($file === false) {
            throw new RuntimeException('Cannot open the contribution export.');
        }
        $accounts = [];
        $snapshot = '';
        $seen = [];
        $eligible = 0;
        try {
            $headers = fgetcsv($file, 0, ',', '"', '');
            $required = ['Effective Date & Time', 'Transaction ID', 'Credit/Debit', 'Kind',
                'Amount Single Column', 'Currency', 'Is Reverse', 'Is Reversed', 'Opposite Account Handle'];
            if (!is_array($headers) || array_diff($required, $headers)) {
                throw new RuntimeException('Unexpected contribution export columns.');
            }
            while (($values = fgetcsv($file, 0, ',', '"', '')) !== false) {
                if ($values === [null]) {
                    continue;
                }
                if (count($values) !== count($headers)) {
                    throw new RuntimeException('Malformed contribution export row.');
                }
                $row = array_combine($headers, $values);
                if (isset($seen[$row['Transaction ID']])) {
                    throw new RuntimeException('Duplicate transaction in the export.');
                }
                $seen[$row['Transaction ID']] = true;
                $date = substr($row['Effective Date & Time'], 0, 10);
                $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
                if (!$parsed || $parsed->format('Y-m-d') !== $date) {
                    throw new RuntimeException('Invalid export date.');
                }
                $snapshot = max($snapshot, $date);
                if ($row['Kind'] !== 'CONTRIBUTION' || $row['Credit/Debit'] !== 'CREDIT'
                    || $row['Is Reverse'] !== '' || $row['Is Reversed'] !== '') {
                    continue;
                }
                if ($row['Currency'] !== 'USD'
                    || !preg_match('/^(\d+)(?:\.(\d{1,2}))?$/D', $row['Amount Single Column'], $amount)) {
                    throw new RuntimeException('Expected a nonnegative USD contribution amount.');
                }
                // Integer cents avoid floating-point rounding in lifetime rankings.
                $cents = (int) $amount[1] * 100 + (int) str_pad($amount[2] ?? '', 2, '0');
                if ($cents === 0) {
                    continue;
                }
                $handle = $row['Opposite Account Handle'];
                if (!isset($accounts[$handle])) {
                    $accounts[$handle] = ['cents' => 0, 'lastContribution' => $date];
                }
                $accounts[$handle]['cents'] += $cents;
                $accounts[$handle]['lastContribution'] = max($accounts[$handle]['lastContribution'], $date);
                ++$eligible;
            }
        } finally {
            fclose($file);
        }
        if ($eligible === 0) {
            throw new RuntimeException('No eligible contributions found in the export.');
        }
        uksort($accounts, static function ($a, $b) use ($accounts) {
            return ($accounts[$b]['cents'] <=> $accounts[$a]['cents']) ?: strcmp($a, $b);
        });

        return ['snapshot' => $snapshot, 'accounts' => $accounts, 'eligible' => $eligible];
    }

    /** Input order is lifetime contribution rank, established during import. */
    public static function group(array $data): array
    {
        $snapshot = new DateTimeImmutable($data['snapshot']);
        $month = $snapshot->modify('first day of this month')->modify('-' . (int) $data['currentMonths'] . ' months');
        // Clamp before adding the inclusive day: February 29 minus a year ends on February 28.
        $day = min((int) $snapshot->format('j'), (int) $month->format('t'));
        $from = $month->setDate((int) $month->format('Y'), (int) $month->format('n'), $day)
            ->modify('+1 day')->format('Y-m-d');
        $groups = ['current' => [], 'past' => []];
        foreach ($data['sponsors'] as $sponsor) {
            $group = $sponsor['lastContribution'] >= $from ? 'current' : 'past';
            $groups[$group][] = $sponsor;
        }

        return ['from' => $from, 'groups' => $groups];
    }
}
