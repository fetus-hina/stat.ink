<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Expression;

final class m221207_152341_season3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%season3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apikey3()->notNull(),
            'name' => $this->string(64)->notNull(),
            'start_at' => $this->timestampTZ(0)->notNull(),
            'end_at' => $this->timestampTZ(0)->notNull(),
            'term' => 'tstzrange NOT NULL',

            'EXCLUDE USING GIST ([[term]] WITH &&)',
            'CHECK ([[start_at]] = LOWER([[term]]))',
            'CHECK ([[end_at]] = UPPER([[term]]))',
            'CHECK ([[start_at]] < [[end_at]])',
        ]);

        $this->batchInsert(
            '{{%season3}}',
            ['key', 'name', 'start_at', 'end_at', 'term'],
            array_map(
                fn (array $row): array => [
                    $row[0],
                    $row[1],
                    $row[2],
                    $row[3],
                    new Expression(
                        vsprintf('tstzrange(%s, %s, %s)', [
                            $this->db->quoteValue($row[2]),
                            $this->db->quoteValue($row[3]),
                            $this->db->quoteValue('[)'),
                        ]),
                    ),
                ],
                $this->getData(),
            ),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%season3}}');

        return true;
    }

    private function getData(): array
    {
        return [
            ['season202209', 'Drizzle Season 2022', '2022-09-01T00:00:00+00:00', '2022-12-01T00:00:00+00:00'],
            ['season202212', 'Chill Season 2022', '2022-12-01T00:00:00+00:00', '2023-03-01T00:00:00+00:00'],
        ];
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%season3}}',
        ];
    }
}
