<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221008_120634_dragon_match extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%dragon_match3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull(),
            'name' => $this->string(63)->notNull(),
        ]);

        $this->batchInsert('{{%dragon_match3}}', ['key', 'name'], [
            ['10x', '10x Battle'],
            ['100x', '100x Battle'],
            ['333x', '333x Battle'],
        ]);

        $this->createTable('{{%dragon_match3_alias}}', [
            'id' => $this->primaryKey(),
            'dragon_id' => $this->pkRef('{{%dragon_match3}}')->notNull(),
            'key' => $this->apiKey3()->notNull(),
        ]);

        $this->batchInsert('{{%dragon_match3_alias}}', ['dragon_id', 'key'], [
            [ $this->key2id('{{%dragon_match3}}', '10x'), 'decuple' ],
            [ $this->key2id('{{%dragon_match3}}', '100x'), 'dragon' ],
            [ $this->key2id('{{%dragon_match3}}', '333x'), 'double_dragon' ],
        ]);

        $this->addColumn(
            '{{%battle3}}',
            'fest_dragon_id',
            (string)$this->pkRef('{{%dragon_match3}}')->null()
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%battle3}}', 'fest_dragon_id');
        $this->dropTables(['{{%dragon_match3_alias}}', '{{%dragon_match3}}']);

        return true;
    }
}
