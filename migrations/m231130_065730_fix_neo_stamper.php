<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m231130_065730_fix_neo_stamper extends Migration
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
            ['name' => 'Neo Splatana Stamper'],
            ['id' => $id],
        );

        $this->update(
            '{{%weapon3_alias}}',
            ['key' => self::name2key3('Neo Splatana Stamper')],
            [
                'weapon_id' => $id,
                'key' => self::name2key3('Splatana Stamper Nouveau'),
            ],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%weapon3}}', 'jimuwiper_hue');

        $this->update(
            '{{%weapon3}}',
            ['name' => 'Splatana Stamper Nouveau'],
            ['id' => $id],
        );

        $this->update(
            '{{%weapon3_alias}}',
            ['key' => self::name2key3('Splatana Stamper Nouveau')],
            [
                'weapon_id' => $id,
                'key' => self::name2key3('Neo Splatana Stamper'),
            ],
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
