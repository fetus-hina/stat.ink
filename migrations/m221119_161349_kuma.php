<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m221119_161349_kuma extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%salmon_weapon3}}', ['key', 'name'], [
            ['kuma_shelter', 'Grizzco Brella'],
            ['kuma_charger', 'Grizzco Charger'],
            ['kuma_slosher', 'Grizzco Slosher'],
        ]);

        $this->batchInsert('{{%salmon_weapon3_alias}}', ['weapon_id', 'key'], [
            [$this->key2id('{{%salmon_weapon3}}', 'kuma_shelter'), self::name2key3('Grizzco Brella')],
            [$this->key2id('{{%salmon_weapon3}}', 'kuma_charger'), self::name2key3('Grizzco Charger')],
            [$this->key2id('{{%salmon_weapon3}}', 'kuma_slosher'), self::name2key3('Grizzco Slosher')],
            [$this->key2id('{{%salmon_weapon3}}', 'kuma_shelter'), '26900'],
            [$this->key2id('{{%salmon_weapon3}}', 'kuma_charger'), '22900'],
            [$this->key2id('{{%salmon_weapon3}}', 'kuma_slosher'), '23900'],
            [$this->key2id('{{%salmon_weapon3}}', 'kuma_blaster'), '20900'],
            [$this->key2id('{{%salmon_weapon3}}', 'kuma_stringer'), '27900'],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $newIds = [
            $this->key2id('{{%salmon_weapon3}}', 'kuma_shelter'),
            $this->key2id('{{%salmon_weapon3}}', 'kuma_charger'),
            $this->key2id('{{%salmon_weapon3}}', 'kuma_slosher'),
        ];
        $this->delete('{{%salmon_weapon3_alias}}', ['weapon_id' => $newIds]);
        $this->delete('{{%salmon_weapon3}}', ['id' => $newIds]);

        $kumaIds = [
            $this->key2id('{{%salmon_weapon3}}', 'kuma_blaster'),
            $this->key2id('{{%salmon_weapon3}}', 'kuma_stringer'),
        ];

        $this->delete('{{%salmon_weapon3_alias}}', ['and',
            ['weapon_id' => $kumaIds],
            ['~', 'key', '^\d+$'],
        ]);

        return true;
    }

    public function vacuumTables(): array
    {
        return [
            '{{%salmon_weapon3}}',
            '{{%salmon_weapon3_alias}}',
        ];
    }
}
