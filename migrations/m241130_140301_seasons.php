<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Expression;

final class m241130_140301_seasons extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $data = [];
        foreach (range(2024, 2030) as $year) {
            foreach ([3, 6, 9, 12] as $month) {
                if ($record = $this->makeRecord($year, $month)) {
                    $data[] = $record;
                }
            }
        }

        $this->batchInsert(
            '{{%season3}}',
            ['key', 'name', 'start_at', 'end_at', 'term'],
            $data,
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete(
            '{{%season3}}',
            ['and',
                ['>=', 'start_at', '2024-12-01T00:00:00+00:00'],
            ],
        );

        return true;
    }

    /**
     * @return array{string, string, string, string, Expression}|null
     */
    private function makeRecord(int $year, int $month): ?array
    {
        if ($year === 2024 && $month < 12) {
            return null;
        }

        $start = (new DateTimeImmutable('now', new DateTimeZone('Etc/UTC')))
            ->setDate($year, $month, 1)
            ->setTime(0, 0, 0);
        $end = $start->add(new DateInterval('P3M'));

        return [
            sprintf('season%s', $start->format('Ym')),
            vsprintf('%s Season %04d', [
                match ((int)$start->format('n')) {
                    3 => 'Fresh',
                    6 => 'Sizzle',
                    9 => 'Drizzle',
                    12 => 'Chill',
                },
                (int)$start->format('Y'),
            ]),
            $start->format(DateTime::ATOM),
            $end->format(DateTime::ATOM),
            new Expression(
                vsprintf('tstzrange(%s, %s, %s)', [
                    $this->db->quoteValue($start->format(DateTime::ATOM)),
                    $this->db->quoteValue($end->format(DateTime::ATOM)),
                    $this->db->quoteValue('[)'),
                ]),
            ),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%season3}}',
        ];
    }
}
