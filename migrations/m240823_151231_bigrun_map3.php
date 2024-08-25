<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240823_151231_bigrun_map3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // fix forgotten migration
        $this->update('{{%map3}}', ['bigrun' => true], ['key' => 'gonzui']);

        $this->createTable('{{%bigrun_map3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'name' => $this->string(48)->notNull(),
            'short_name' => $this->string(16)->notNull(),
            'area' => $this->integer()->null(),
            'release_at' => $this->timestampTZ(0)->null(),
            'bigrun' => $this->boolean()->notNull()->defaultValue(true),
        ]);

        $this->execute(
            'INSERT INTO {{%bigrun_map3}} ' .
            'SELECT * FROM {{%map3}} WHERE [[bigrun]] = TRUE',
        );

        $this->createTable('{{%bigrun_map3_alias}}', [
            'id' => $this->primaryKey(),
            'map_id' => $this->pkRef('{{%bigrun_map3}}')->notNull(),
            'key' => $this->apiKey3()->notNull()->unique(),
        ]);

        $this->execute(
            'INSERT INTO {{%bigrun_map3_alias}} ' .
            'SELECT {{%map3_alias}}.* ' .
            'FROM {{%map3_alias}} ' .
            'INNER JOIN {{%map3}} ON {{%map3}}.[[id]] = {{%map3_alias}}.[[map_id]] ' .
            'WHERE {{%map3}}.[[bigrun]] = TRUE',
        );

        $this->update('{{%map3}}', ['bigrun' => false], ['key' => 'gonzui']);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%bigrun_map3_alias}}',
            '{{%bigrun_map3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%map3}}',
            '{{%map3_alias}}',
        ];
    }
}
