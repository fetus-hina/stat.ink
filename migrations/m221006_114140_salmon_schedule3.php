<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;
use yii\db\Query;

final class m221006_114140_salmon_schedule3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->createTable('{{%salmon_schedule3}}', [
            'id' => $this->primaryKey(),
            'map_id' => $this->pkRef('{{%salmon_map3}}')->notNull(),
            'start_at' => $this->timestampTZ(0)->notNull()->unique(),
            'end_at' => $this->timestampTZ(0)->notNull(),
        ]);

        $this->createTable('{{%salmon_schedule_weapon3}}', [
            'id' => $this->primaryKey(),
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'weapon_id' => $this->pkRef('{{%salmon_weapon3}}')->null(),
            'random_id' => $this->pkRef('{{%salmon_random3}}')->null(),

            vsprintf('CHECK (%s)', [
                // weapon_id か random_id のどちらか一方が NULL であるかのチェック
                // 両方 NULL だったり、両方 NOT NULL だったりは許さない
                preg_replace(
                    '/^.+?\bWHERE\s+(.+)$/',
                    '\1',
                    (new Query())
                        ->where(['or',
                            ['and',
                                ['not', ['[[weapon_id]]' => null]],
                                ['[[random_id]]' => null],
                            ],
                            ['and',
                                ['[[weapon_id]]' => null],
                                ['not', ['[[random_id]]' => null]],
                            ],
                        ])
                        ->createCommand($db)
                        ->getRawSql()
                ),
            ]),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%salmon_schedule_weapon3}}',
            '{{%salmon_schedule3}}',
        ]);

        return true;
    }
}
