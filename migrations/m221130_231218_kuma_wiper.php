<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m221130_231218_kuma_wiper extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%salmon_weapon3}}', [
            'key' => 'kuma_wiper',
            'name' => 'Grizzco Splatana',
            'weapon_id' => null,
        ]);

        $id = $this->key2id('{{%salmon_weapon3}}', 'kuma_wiper');
        $this->batchInsert('{{%salmon_weapon3_alias}}', ['weapon_id', 'key'], [
            [$id, '28900'],
            [$id, self::name2key3('Grizzco Splatana')],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->key2id('{{%salmon_weapon3}}', 'kuma_wiper');
        $this->delete('{{%salmon_weapon3_alias}}', ['weapon_id' => $id]);
        $this->delete('{{%salmon_weapon3}}', ['id' => $id]);

        return true;
    }

    protected function vacuumTables(): array
    {
        return [
            '{{%salmon_weapon3}}',
            '{{%salmon_weapon3_alias}}',
        ];
    }
}
