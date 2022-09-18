<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m220918_082347_heroshot extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%weapon3}}', [
            'key' => 'heroshooter_replica',
            'mainweapon_id' => $this->key2id('{{%mainweapon3}}', 'sshooter'),
            'subweapon_id' => $this->key2id('{{%subweapon3}}', 'kyubanbomb'),
            'special_id' => $this->key2id('{{%special3}}', 'ultrashot'),
            'canonical_id' => $this->key2id('{{%weapon3}}', 'sshooter'),
            'name' => 'Hero Shot Replica',
        ]);

        $this->batchInsert('{{%weapon3_alias}}', ['weapon_id', 'key'], [
            [$this->key2id('{{%weapon3}}', 'heroshooter_replica'), self::name2key3('Hero Shot Replica')],
            [$this->key2id('{{%weapon3}}', 'heroshooter_replica'), '45'],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $weaponId = $this->key2id('{{%weapon3}}', 'heroshooter_replica');
        $this->delete('{{%weapon3_alias}}', ['weapon_id' => $weaponId]);
        $this->delete('{{%weapon3}}', ['id' => $weaponId]);

        return true;
    }
}
