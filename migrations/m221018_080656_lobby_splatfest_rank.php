<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221018_080656_lobby_splatfest_rank extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update('{{%lobby3}}', ['rank' => 710], ['key' => 'splatfest_challenge']);
        $this->update('{{%lobby3}}', ['rank' => 720], ['key' => 'splatfest_open']);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('{{%lobby3}}', ['rank' => 120], ['key' => 'splatfest_challenge']);
        $this->update('{{%lobby3}}', ['rank' => 130], ['key' => 'splatfest_open']);

        return true;
    }
}
