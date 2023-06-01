<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230601_080311_lobby_event extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%lobby_group3}}', [
            'key' => 'event',
            'name' => 'Challenge',
            'rank' => 400,
            'importance' => 200,
        ]);

        $this->insert('{{%lobby3}}', [
            'key' => 'event',
            'name' => 'Challenge',
            'rank' => 400,
            'group_id' => $this->key2id('{{%lobby_group3}}', 'event'),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%lobby3}}', ['key' => 'event']);
        $this->delete('{{%lobby_group3}}', ['key' => 'event']);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%lobby3}}',
            '{{%lobby_group3}}',
        ];
    }
}
