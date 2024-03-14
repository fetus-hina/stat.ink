<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

// https://github.com/fetus-hina/stat.ink/issues/1264
final class m240314_014407_rename_stamper extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $id = $this->key2id('{{%weapon3}}', 'jimuwiper_hue');

        $this->update(
            '{{%weapon3}}',
            ['name' => 'Splatana Stamper Nouveau'],
            ['id' => $id],
        );

        $this->insert('{{%weapon3_alias}}', [
            'key' => self::name2key3('Splatana Stamper Nouveau'),
            'weapon_id' => $id,
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->update(
            '{{%weapon3}}',
            ['name' => 'Neo Splatana Stamper'],
            ['key' => 'jimuwiper_hue'],
        );

        $this->delete(
            '{{%weapon3_alias}}',
            ['key' => self::name2key3('Splatana Stamper Nouveau')],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%weapon3}}',
            '{{%weapon3_alias}}',
        ];
    }
}
