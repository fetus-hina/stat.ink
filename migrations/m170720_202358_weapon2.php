<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Expression;
use yii\db\Query;

class m170720_202358_weapon2 extends Migration
{
    public function safeUp()
    {
        $this->update('subweapon2', ['key' => 'robotbomb'], ['key' => 'rocketbomb']);
        $this->update('death_reason2', ['key' => 'robotbomb'], ['key' => 'rocketbomb']);

        $this->upWeapon('jetsweeper', 'Jet Squelcher', 'shooter', 'poisonmist', 'jetpack');
        $this->upWeapon('nzap85', 'N-ZAP \'85', 'shooter', 'kyubanbomb', 'armor');
        $this->upWeapon('promodeler_rg', 'Aerospray RG', 'shooter', 'sprinkler', 'sphere');
        $this->upWeapon('promodeler_mg', 'Aerospray MG', 'shooter', 'kyubanbomb', 'pitcher', 'promodeler_rg');
        $this->upWeapon('52gal', '.52 Gal', 'shooter', 'pointsensor', 'sphere');
        $this->upWeapon('96gal', '.96 Gal', 'shooter', 'sprinkler', 'armor');
        $this->upWeapon('l3reelgun', 'L-3 Nozzlenose', 'shooter', 'curlingbomb', 'sphere');
        $this->upWeapon('h3reelgun', 'H-3 Nozzlenose', 'shooter', 'pointsensor', 'missile');
        $this->upWeapon('hotblaster_custom', 'Custom Blaster', 'blaster', 'robotbomb', 'jetpack', 'hotblaster');
        $this->upWeapon('nova', 'Luna Blaster', 'blaster', 'splashbomb', 'sphere');
        $this->upWeapon('rapid', 'Rapid Blaster', 'blaster', 'trap', 'pitcher');
        $this->upWeapon('manueuver_collabo', 'スプラマニューバーコラボ', 'shooter', 'curlingbomb', 'jetpack', 'manueuver');
        $this->upWeapon('splatroller_collabo', 'Krak-On Splat Roller', 'roller', 'jumpbeacon', 'sphere', 'splatroller');
        $this->upWeapon('variableroller', 'Flingza Roller', 'roller', 'splashshield', 'pitcher');
        $this->upWeapon('carbon', 'Carbon Roller', 'roller', 'robotbomb', 'amefurashi');
        $this->upWeapon(
            'splatcharger_collabo',
            'Firefin Splat Charger',
            'charger',
            'splashshield',
            'pitcher',
            'splatcharger',
        );
        $this->upWeapon(
            'splatscope_collabo',
            'Firefin Splatterscope',
            'charger',
            'splashshield',
            'pitcher',
            'splatcharger',
        );
        $this->upWeapon('liter4k', 'E-liter 4K', 'charger', 'trap', 'amefurashi');
        $this->upWeapon('liter4k_scope', 'E-liter 4K Scope', 'charger', 'trap', 'amefurashi', 'liter4k');
        $this->upWeapon('splatspinner', 'Mini Splatling', 'splatling', 'quickbomb', 'missile');
        $this->upWeapon('hissen', 'Tri-Slosher', 'slosher', 'quickbomb', 'armor');
    }

    public function safeDown()
    {
        $keys = [
            'jetsweeper',
            'nzap85',
            'promodeler_rg',
            'promodeler_mg',
            '52gal',
            '96gal',
            'l3reelgun',
            'h3reelgun',
            'hotblaster_custom',
            'nova',
            'rapid',
            'manueuver_collabo',
            'splatroller_collabo',
            'variableroller',
            'carbon',
            'splatcharger_collabo',
            'splatscope_collabo',
            'liter4k',
            'liter4k_scope',
            'splatspinner',
            'hissen',
        ];
        $this->delete('death_reason2', ['key' => $keys]);
        $this->delete('weapon2', ['key' => $keys]);
        $this->update('subweapon2', ['key' => 'rocketbomb'], ['key' => 'robotbomb']);
        $this->update('death_reason2', ['key' => 'rocketbomb'], ['key' => 'robotbomb']);
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
            'key' => $key,
            'name' => $name,
            'type_id' => $this->findId('weapon_type2', $type),
            'subweapon_id' => $this->findId('subweapon2', $sub),
            'special_id' => $this->findId('special2', $special),
            'main_group_id' => $main !== null
                ? $this->findId('weapon2', $main)
                : new Expression("currval('weapon2_id_seq'::regclass)"),
            'canonical_id' => $canonical !== null
                ? $this->findId('weapon2', $canonical)
                : new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);

        $this->insert('death_reason2', [
            'key' => $key,
            'name' => $name,
            'type_id' => $this->findId('death_reason_type2', 'main'),
            'weapon_id' => $this->findId('weapon2', $key),
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
