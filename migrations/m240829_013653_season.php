<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Expression;

final class m240829_013653_season extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;

        $this->update(
            '{{%season3}}',
            [
                'term' => new Expression(
                    vsprintf('tstzrange(%s, %s, %s)', [
                        $db->quoteValue('2024-06-01T00:00:00+00:00'),
                        $db->quoteValue('2024-09-01T00:00:00+00:00'),
                        $db->quoteValue('[)'),
                    ]),
                ),
            ],
            ['key' => 'season202406'],
        );

        $this->insert('{{%season3}}', [
            'key' => 'season202409',
            'name' => 'Drizzle Season 2024',
            'start_at' => '2024-09-01T00:00:00+00:00',
            'end_at' => '2024-12-01T00:00:00+00:00',
            'term' => new Expression(
                vsprintf('tstzrange(%s, %s, %s)', [
                    $db->quoteValue('2024-09-01T00:00:00+00:00'),
                    $db->quoteValue('2024-12-01T00:00:00+00:00'),
                    $db->quoteValue('[)'),
                ]),
            ),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%season3}}', ['key' => 'season202409']);

        $this->update(
            '{{%season3}}',
            [
                'term' => new Expression(
                    vsprintf('tstzrange(%s, %s, %s)', [
                        $db->quoteValue('2024-06-01T00:00:00+00:00'),
                        $db->quoteValue('2024-09-01T00:00:00+00:00'),
                        $db->quoteValue('[]'),
                    ]),
                ),
            ],
            ['key' => 'season202406'],
        );

        return true;
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
