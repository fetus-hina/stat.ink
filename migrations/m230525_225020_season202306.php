<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Expression;

final class m230525_225020_season202306 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->insert('{{%season3}}', [
            'key' => 'season202306',
            'name' => 'Sizzle Season 2023',
            'start_at' => '2023-06-01T00:00:00+00:00',
            'end_at' => '2023-09-01T00:00:00+00:00',
            'term' => new Expression(
                vsprintf('tstzrange(%s, %s, %s)', [
                    $db->quoteValue('2023-06-01T00:00:00+00:00'),
                    $db->quoteValue('2023-09-01T00:00:00+00:00'),
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
        $this->delete('{{%season3}}', ['key' => 'season202306']);

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
