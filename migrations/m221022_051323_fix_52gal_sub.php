<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221022_051323_fix_52gal_sub extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update(
            '{{%weapon3}}',
            ['subweapon_id' => $this->key2id('{{%subweapon3}}', 'splashshield')],
            ['key' => '52gal'],
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
            ['subweapon_id' => $this->key2id('{{%subweapon3}}', 'linemarker')],
            ['key' => '52gal'],
        );

        return true;
    }
}
