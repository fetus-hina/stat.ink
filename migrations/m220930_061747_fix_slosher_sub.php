<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220930_061747_fix_slosher_sub extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update(
            '{{%weapon3}}',
            ['subweapon_id' => $this->key2id('{{%subweapon3}}', 'splashbomb')],
            ['key' => 'bucketslosher'],
        );
        $this->update(
            '{{%weapon3}}',
            ['subweapon_id' => $this->key2id('{{%subweapon3}}', 'poisonmist')],
            ['key' => 'hissen'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update(
            '{{%weapon3}}',
            ['subweapon_id' => $this->key2id('{{%subweapon3}}', 'poisonmist')],
            ['key' => 'bucketslosher'],
        );
        $this->update(
            '{{%weapon3}}',
            ['subweapon_id' => $this->key2id('{{%subweapon3}}', 'splashbomb')],
            ['key' => 'hissen'],
        );

        return true;
    }
}
