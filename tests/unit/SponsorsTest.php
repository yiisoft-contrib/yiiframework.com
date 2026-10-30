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
        ];
        foreach ($rows as $row) {
            fputcsv($stream, $row);
        }
        fclose($stream);
        try {
            $data = Sponsors::readContributions($file);
            self::assertSame('2026-10-26', $data['snapshot']);
            self::assertSame(3, $data['eligible']);
            self::assertSame(['second', 'first'], array_keys($data['accounts']));
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
}
