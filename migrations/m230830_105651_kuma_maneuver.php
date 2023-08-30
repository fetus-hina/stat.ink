<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m230830_105651_kuma_maneuver extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%salmon_weapon3}}', [
            'key' => 'kuma_maneuver',
            'name' => 'Grizzco Dualies',
        ]);

        $this->batchInsert('{{%salmon_weapon3_alias}}', ['weapon_id', 'key'], [
            [
                $this->key2id('{{%salmon_weapon3}}', 'kuma_maneuver'),
                self::name2key3('Grizzco Dualies'),
            ],
            [
                $this->key2id('{{%salmon_weapon3}}', 'kuma_maneuver'),
                '25900',
            ],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%salmon_weapon3}}', 'kuma_maneuver');
        $this->delete('{{%salmon_weapon3_alias}}', ['weapon_id' => $id]);
        $this->delete('{{%salmon_weapon3}}', ['id' => $id]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_weapon3}}',
            '{{%salmon_weapon3_alias}}',
        ];
    }
}
