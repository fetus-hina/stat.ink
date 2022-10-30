<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m221030_191308_kuma_blaster extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%salmon_weapon3}}', [
            'key' => 'kuma_blaster',
            'name' => 'Grizzco Blaster',
        ]);

        $this->insert('{{%salmon_weapon3_alias}}', [
            'weapon_id' => $this->key2id('{{%salmon_weapon3}}', 'kuma_blaster'),
            'key' => self::name2key3('Grizzco Blaster'),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = self::name2key3('Grizzco Blaster');
        $this->delete('{{%salmon_weapon3_alias}}', ['weapon_id' => $id]);
        $this->delete('{{%salmon_weapon3}}', ['id' => $id]);

        return true;
    }
}
