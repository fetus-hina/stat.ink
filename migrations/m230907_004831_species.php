<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230907_004831_species extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%species3}}', [
            'id' => $this->primaryKey()->notNull(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'name' => $this->string(64)->notNull(),
        ]);

        $this->batchInsert('{{%species3}}', ['key', 'name'], [
            [ 'inkling', 'Inkling' ],
            [ 'octoling', 'Octoling' ],
        ]);

        $tables = [
            '{{%battle_player3}}',
            '{{%battle_tricolor_player3}}',
            '{{%salmon_player3}}',
        ];
        foreach ($tables as $table) {
            $this->addColumn(
                $table,
                'species_id',
                (string)$this->pkRef('{{%species3}}')->null(),
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
            '{{%salmon_player3}}',
        ];
        foreach ($tables as $table) {
            $this->dropColumn($table, 'species_id');
        }

        $this->dropTables([
            '{{%species3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%species3}}',
        ];
    }
}
