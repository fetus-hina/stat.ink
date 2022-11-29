<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221129_135900_weapon_variant_subsp extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update(
            '{{%weapon3}}',
            ['special_id' => $this->key2id('{{%special3}}', 'tripletornado')],
            ['key' => 'sshooter_collabo'],
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
            ['special_id' => null],
            ['key' => 'sshooter_collabo'],
        );

        return true;
    }

    public function vacuumTables(): array
    {
        return [
            '{{%weapon3}}',
        ];
    }
}
