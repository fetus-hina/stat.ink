<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221002_102833_fest3_lobby_only extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%lobby3}}', ['key', 'name', 'rank'], [
            ['splatfest_challenge', 'Splatfest (Pro)', 120],
            ['splatfest_open', 'Splatfest (Open)', 130],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%lobby3}}', [
            'key' => [
                'splatfest_challenge',
                'splatfest_open',
            ],
        ]);

        return false;
    }
}
