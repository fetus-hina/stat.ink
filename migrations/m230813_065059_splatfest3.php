<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230813_065059_splatfest3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%splatfest3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(127)->notNull(),
            'start_at' => $this->timestampTZ(0)->notNull(),
            'end_at' => $this->timestampTZ(0)->notNull(),
        ]);

        $this->createTable('{{%splatfest_camp3}}', [
            'id' => $this->integer()->notNull(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'name' => $this->string(127)->notNull(),
            'PRIMARY KEY ([[id]])',
        ]);

        $this->batchInsert('{{%splatfest_camp3}}', ['id', 'key', 'name'], [
            [1, 'alpha', 'Shiver'],
            [2, 'bravo', 'Frye'],
            [3, 'charlie', 'Big Man'],
        ]);

        $this->createTable('{{%splatfest_team3}}', [
            'id' => $this->primaryKey(),
            'fest_id' => $this->pkRef('{{%splatfest3}}')->notNull(),
            'camp_id' => $this->pkRef('{{%splatfest_camp3}}')->notNull(),
            'name' => $this->string(32)->notNull(),
            'color' => $this->char(8)->notNull(),

            'UNIQUE ([[fest_id]], [[camp_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%splatfest_team3}}',
            '{{%splatfest_camp3}}',
            '{{%splatfest3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%splatfest3}}',
            '{{%splatfest_camp3}}',
            '{{%splatfest_team3}}',
        ];
    }
}
