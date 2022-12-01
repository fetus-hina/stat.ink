<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221201_020349_202212_new_weapon_subsp extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->update(
            '{{%weapon3}}',
            [
                'subweapon_id' => $this->key2id('{{%subweapon3}}', 'splashshield'),
                'special_id' => $this->key2id('{{%special3}}', 'kyuinki'),
            ],
            ['key' => 'wideroller'],
        );
        $this->update(
            '{{%weapon3}}',
            ['subweapon_id' => $this->key2id('{{%subweapon3}}', 'sprinkler')],
            ['key' => 'rpen_5h'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update('{{%weapon3}}', ['subweapon_id' => null], ['key' => 'wideroller']);
        $this->update('{{%weapon3}}', ['special_id' => null], ['key' => ['wideroller', 'rpen_5h']]);

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%weapon3}}',
        ];
    }
}
