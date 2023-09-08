<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230907_233647_crown extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%crown3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'name' => $this->string(32)->notNull(),
        ]);

        $this->batchInsert('{{%crown3}}', ['key', 'name'], [
            ['x', 'X'],
            ['100x', 'Splatfest 100x Winner'],
            ['333x', 'Splatfest 333x Winner'],
        ]);

        $tables = [
            '{{%battle_player3}}',
            '{{%battle_tricolor_player3}}',
        ];
        foreach ($tables as $table) {
            $this->addColumn(
                $table,
                'crown_id',
                (string)$this->pkRef('{{%crown3}}')->null(),
            );
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $tables = [
            '{{%battle_player3}}',
            '{{%battle_tricolor_player3}}',
        ];
        foreach ($tables as $table) {
            $this->dropColumn($table, 'crown_id');
        }

        $this->dropTables([
            '{{%crown3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%crown3}}',
        ];
    }
}
