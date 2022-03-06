<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Expression;
use yii\db\Query;

class m170720_103120_weapon2 extends Migration
{
    public function safeUp()
    {
        $this->upWeapon('wakaba', 'Splattershot Jr.', 'shooter', 'splashbomb', 'armor');
        $this->upWeapon('sputtery', 'Dapple Dualies', 'shooter', 'jumpbeacon', 'pitcher');
        $this->upWeapon('clashblaster', 'Clash Blaster', 'blaster', 'splashbomb', 'presser');
        $this->upWeapon('soytuber', 'Goo Tuber', 'charger', 'kyubanbomb', 'chakuchi');
    }

    public function safeDown()
    {
        $keys = [
            'wakaba',
            'sputtery',
            'clashblaster',
            'soytuber',
        ];
        $this->delete('death_reason2', ['key' => $keys]);
        $this->delete('weapon2', ['key' => $keys]);
    }

    private function upWeapon(
        string $key,
        string $name,
        string $type,
        string $sub,
        string $special,
        ?string $main = null,
        ?string $canonical = null
    ) {
        $this->insert('weapon2', [
            'key'           => $key,
            'name'          => $name,
            'type_id'       => $this->findId('weapon_type2', $type),
            'subweapon_id'  => $this->findId('subweapon2', $sub),
            'special_id'    => $this->findId('special2', $special),
            'main_group_id' => $main !== null
                ? $this->findId('weapon2', $main)
                : new Expression("currval('weapon2_id_seq'::regclass)"),
            'canonical_id'  => $canonical !== null
                ? $this->findId('weapon2', $canonical)
                : new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);

        $this->insert('death_reason2', [
            'key'           => $key,
            'name'          => $name,
            'type_id'       => $this->findId('death_reason_type2', 'main'),
            'weapon_id'     => $this->findId('weapon2', $key),
        ]);
    }

    private function findId(string $table, string $key): int
    {
        return (new Query())
            ->select('id')
            ->from($table)
            ->where(['key' => $key])
            ->limit(1)
            ->scalar();
    }
}
