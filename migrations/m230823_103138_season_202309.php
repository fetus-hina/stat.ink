<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230823_103138_season_202309 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $startAt = '2023-09-01T00:00:00+00:00';
        $endAt = '2023-12-01T00:00:00+00:00';

        $db = \app\components\helpers\TypeHelper::instanceOf($this->db, \yii\db\Connection::class);
        $this->insert('{{%season3}}', [
            'key' => 'season202309',
            'name' => 'Drizzle Season 2023',
            'start_at' => $startAt,
            'end_at' => $endAt,
            'term' => new \yii\db\Expression(
                vsprintf('tstzrange(%s, %s, %s)', [
                    $db->quoteValue($startAt),
                    $db->quoteValue($endAt),
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
        $this->delete(
            '{{%season3}}',
            ['key' => 'season202309'],
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
