<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m240222_091509_salmon_uniform3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%salmon_uniform3}}', ['key', 'name', 'rank'], [
            ['peach_gloopsuit', 'Peach Gloopsuit', 400],
            ['lime_gloopsuit', 'Lime Gloopsuit', 410],
            ['berry_gloopsuit', 'Berry Gloopsuit', 420],
        ]);

        $this->batchInsert('{{%salmon_uniform3_alias}}', ['uniform_id', 'key'], [
            [$this->key2id('{{%salmon_uniform3}}', 'peach_gloopsuit'), '15'],
            [$this->key2id('{{%salmon_uniform3}}', 'lime_gloopsuit'), '16'],
            [$this->key2id('{{%salmon_uniform3}}', 'berry_gloopsuit'), '17'],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $ids = [
            $this->key2id('{{%salmon_uniform3}}', 'peach_gloopsuit'),
            $this->key2id('{{%salmon_uniform3}}', 'lime_gloopsuit'),
            $this->key2id('{{%salmon_uniform3}}', 'berry_gloopsuit'),
        ];

        $this->delete('{{%salmon_uniform3_alias}}', ['uniform_id' => $ids]);
        $this->delete('{{%salmon_uniform3}}', ['id' => $ids]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_uniform3}}',
            '{{%salmon_uniform3_alias}}',
        ];
    }
}
