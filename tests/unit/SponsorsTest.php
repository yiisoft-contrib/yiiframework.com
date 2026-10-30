<?php

use app\components\Sponsors;

class SponsorsTest extends \PHPUnit\Framework\TestCase
{
    public function testImportExcludesReversalsFeesAndPayoutsAndKeepsHandlesSeparate()
    {
        $file = tempnam(sys_get_temp_dir(), 'sponsor-test');
        $stream = fopen($file, 'w');
        fputcsv($stream, ['Effective Date & Time', 'Transaction ID', 'Credit/Debit', 'Kind',
            'Amount Single Column', 'Currency', 'Is Reverse', 'Is Reversed', 'Opposite Account Handle']);
        $rows = [
            ['2025-01-01', '1', 'CREDIT', 'CONTRIBUTION', '0.10', 'USD', '', '', 'first'],
            ['2026-10-26', '2', 'CREDIT', 'CONTRIBUTION', '0.20', 'USD', '', '', 'first'],
            ['2026-10-01', '3', 'CREDIT', 'CONTRIBUTION', '2.51', 'USD', '', '', 'second'],
            ['2026-10-26', '4', 'CREDIT', 'CONTRIBUTION', '99', 'USD', '', 'REVERSED', 'first'],
            ['2026-10-26', '5', 'CREDIT', 'CONTRIBUTION', '99', 'USD', 'REVERSE', '', 'first'],
            ['2026-10-26', '6', 'CREDIT', 'HOST_FEE', '99', 'USD', 'REVERSE', '', 'first'],
            ['2026-10-26', '7', 'CREDIT', 'PAYMENT_PROCESSOR_COVER', '99', 'USD', 'REVERSE', '', 'first'],
            ['2026-10-26', '8', 'CREDIT', 'ADDED_FUNDS', '99', 'USD', '', '', 'first'],
            ['2026-10-26', '9', 'DEBIT', 'CONTRIBUTION', '99', 'USD', '', '', 'first'],
            ['2026-10-26', '10', 'CREDIT', 'CONTRIBUTION', '0', 'USD', '', '', 'first'],
            ['2026-10-26', '11', 'CREDIT', 'HOST_FEE', '99', 'USD', '', '', 'first'],
            ['2026-10-26', '12', 'CREDIT', 'PAYMENT_PROCESSOR_COVER', '99', 'USD', '', '', 'first'],
            ['2026-10-26', '13', 'CREDIT', 'CONTRIBUTION', '1', 'USD', '', '', 'zulu'],
            ['2026-10-26', '14', 'CREDIT', 'CONTRIBUTION', '1', 'USD', '', '', 'alpha'],
        ];
        foreach ($rows as $row) {
            fputcsv($stream, $row);
        }
        fclose($stream);
        try {
            $data = Sponsors::readContributions($file);
            self::assertSame('2026-10-26', $data['snapshot']);
            self::assertSame(5, $data['eligible']);
            self::assertSame(['second', 'alpha', 'zulu', 'first'], array_keys($data['accounts']));
            self::assertSame(['cents' => 30, 'lastContribution' => '2026-10-26'], $data['accounts']['first']);
            self::assertSame(251, $data['accounts']['second']['cents']);
        } finally {
            unlink($file);
        }
    }

    public function testWindowBoundariesAndLifetimeOrder()
    {
        $data = [
            'snapshot' => '2026-10-26',
            'currentMonths' => 12,
            'sponsors' => [
                ['handle' => 'past-leader', 'lastContribution' => '2025-10-26'],
                ['handle' => 'current-leader', 'lastContribution' => '2026-10-26'],
                ['handle' => 'current-next', 'lastContribution' => '2025-10-27'],
            ],
        ];
        $grouped = Sponsors::group($data);
        self::assertSame('2025-10-27', $grouped['from']);
        self::assertSame(['current-leader', 'current-next'], array_column($grouped['groups']['current'], 'handle'));
        self::assertSame(['past-leader'], array_column($grouped['groups']['past'], 'handle'));
        $data['currentMonths'] = 6;
        self::assertSame('2026-04-27', Sponsors::group($data)['from']);
        self::assertCount(1, Sponsors::group($data)['groups']['current']);
    }

    /** @dataProvider calendarWindows */
    public function testCalendarWindowClampsBeforeAddingInclusiveDay($snapshot, $months, $from)
    {
        $before = (new DateTimeImmutable($from))->modify('-1 day')->format('Y-m-d');
        $grouped = Sponsors::group([
            'snapshot' => $snapshot,
            'currentMonths' => $months,
            'sponsors' => [
                ['handle' => 'boundary', 'lastContribution' => $from],
                ['handle' => 'before', 'lastContribution' => $before],
            ],
        ]);
        self::assertSame($from, $grouped['from']);
        self::assertSame(['boundary'], array_column($grouped['groups']['current'], 'handle'));
        self::assertSame(['before'], array_column($grouped['groups']['past'], 'handle'));
    }

    public function calendarWindows()
    {
        return [
            'leap day to common year' => ['2024-02-29', 12, '2023-03-01'],
            'month end to common February' => ['2025-03-31', 1, '2025-03-01'],
            'month end to leap February' => ['2024-03-31', 1, '2024-03-01'],
            'month end to thirty-day month' => ['2026-05-31', 1, '2026-05-01'],
            'unchanged snapshot window' => ['2026-10-26', 12, '2025-10-27'],
        ];
    }

    /** @dataProvider importHistory */
    public function testImporterRequiresEveryApprovedHandleBeforeWritingOutput($partial)
    {
        $config = require dirname(__DIR__, 2) . '/config/sponsors.php';
        $file = tempnam(sys_get_temp_dir(), 'sponsor-history');
        $stream = fopen($file, 'w');
        fputcsv($stream, ['Effective Date & Time', 'Transaction ID', 'Credit/Debit', 'Kind',
            'Amount Single Column', 'Currency', 'Is Reverse', 'Is Reversed', 'Opposite Account Handle']);
        foreach ($config['sponsors'] as $index => $sponsor) {
            if ($partial && $index === 0) {
                continue;
            }
            fputcsv($stream, ['2026-10-26', (string) $index, 'CREDIT', 'CONTRIBUTION',
                '1', 'USD', '', '', $sponsor['handle']]);
        }
        fputcsv($stream, ['2026-10-26', 'unapproved', 'CREDIT', 'CONTRIBUTION',
            '1', 'USD', '', '', 'not-in-public-allowlist']);
        fclose($stream);
        try {
            $process = proc_open([PHP_BINARY, dirname(__DIR__, 2) . '/scripts/import-sponsors.php', $file],
                [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
            self::assertIsResource($process);
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            $error = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $status = proc_close($process);
            if ($partial) {
                self::assertSame(1, $status);
                self::assertSame('', $output);
                self::assertContains('full contribution history', $error);
            } else {
                self::assertSame(0, $status, $error);
                self::assertSame(count($config['sponsors']), substr_count($output, "'handle' =>"));
                self::assertNotContains('not-in-public-allowlist', $output);
            }
        } finally {
            unlink($file);
        }
    }

    public function importHistory()
    {
        return ['complete' => [false], 'partial' => [true]];
    }
}
