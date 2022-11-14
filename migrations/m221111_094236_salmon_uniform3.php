<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221111_094236_salmon_uniform3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%salmon_uniform3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey3()->notNull()->unique(),
            'name' => $this->string(64)->notNull(),
            'rank' => $this->integer()->notNull()->unique(),
        ]);

        $this->batchInsert('{{%salmon_uniform3}}', ['key', 'name', 'rank'], [
            ['orange', 'Orange Slopsuit', 100],
            ['green', 'Green Slopsuit', 110],
            ['yellow', 'Yellow Slopsuit', 120],
            ['pink', 'Pink Slopsuit', 130],
            ['blue', 'Blue Slopsuit', 140],
            ['black', 'Black Slopsuit', 150],
            ['white', 'White Slopsuit', 160],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%salmon_uniform3}}');

        return true;
    }
}
