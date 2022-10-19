<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221019_042702_rule_group extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%rule_group3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull(),
            'name' => $this->string(32)->notNull(),
            'rank' => $this->integer()->notNull()->unique(),
        ]);

        $this->batchInsert('{{%rule_group3}}', ['key', 'name', 'rank'], [
            ['nawabari', 'Turf War', 100],
            ['gachi', 'Ranked Modes', 200],
        ]);

        $this->addColumn(
            '{{%rule3}}',
            'group_id',
            (string)$this->pkRef('{{%rule_group3}}')->null(),
        );

        $this->update(
            '{{%rule3}}',
            ['group_id' => self::key2id('{{%rule_group3}}', 'nawabari')],
            ['key' => 'nawabari'],
        );

        $this->update(
            '{{%rule3}}',
            ['group_id' => self::key2id('{{%rule_group3}}', 'gachi')],
            ['not', ['key' => 'nawabari']],
        );

        $this->alterColumn(
            '{{%rule3}}',
            'group_id',
            (string)$this->integer()->notNull(),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%rule3}}', 'group_id');
        $this->dropTable('{{%rule_group3}}');

        return true;
    }

    public function vacuumTables(): array
    {
        return [
            '{{%rule_group3}}',
            '{{%rule}}',
        ];
    }
}
