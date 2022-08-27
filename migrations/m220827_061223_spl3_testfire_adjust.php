<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\AutoKey;
use app\components\db\Migration;

final class m220827_061223_spl3_testfire_adjust extends Migration
{
    use AutoKey;

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->batchInsert('{{%subweapon3}}', ['key', 'name'], [
            ['jumpbeacon', 'Squid Beakon'],
            ['robotbomb', 'Autobomb'],
            ['tansanbomb', 'Fizzy Bomb'],
            ['trap', 'Ink Mine'],
        ]);

        $this->batchInsert('{{%subweapon3_alias}}', ['subweapon_id', 'key'], [
            [$this->key2id('{{%subweapon3}}', 'jumpbeacon'), self::name2key3('Squid Beakon')],
            [$this->key2id('{{%subweapon3}}', 'robotbomb'), self::name2key3('Autobomb')],
            [$this->key2id('{{%subweapon3}}', 'tansanbomb'), self::name2key3('Fizzy Bomb')],
            [$this->key2id('{{%subweapon3}}', 'trap'), self::name2key3('Ink Mine')],
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->delete('{{%subweapon3_alias}}', [
            'subweapon_id' => [
                $this->key2id('{{%subweapon3}}', 'jumpbeacon'),
                $this->key2id('{{%subweapon3}}', 'robotbomb'),
                $this->key2id('{{%subweapon3}}', 'tansanbomb'),
                $this->key2id('{{%subweapon3}}', 'trap'),
            ],
        ]);

        $this->delete('{{%subweapon3}}', [
            'key' => [
                'jumpbeacon',
                'robotbomb',
                'tansanbomb',
                'trap',
            ],
        ]);

        return true;
    }

    public function vacuumTables(): array
    {
        return [
            '{{%subweapon3}}',
            '{{%subweapon3_alias}}',
        ];
    }
}
