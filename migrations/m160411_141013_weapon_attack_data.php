<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\SplatoonVersion;
use app\models\Weapon;
use yii\db\Migration;

class m160411_141013_weapon_attack_data extends Migration
{
    public function safeUp()
    {
        $w = $this->getWeapons();
        // 1.0.0
        $this->batchInsert('weapon_attack', ['main_weapon_id', 'damage'], [
            // shooter
            [ $w['52gal'],           52.0 ],
            [ $w['96gal'],           62.0 ],
            [ $w['bold'],            38.0 ],
            [ $w['dualsweeper'],     28.0 ],
            [ $w['h3reelgun'],       41.0 ],
            [ $w['hotblaster'],     125.0 ],
            [ $w['jetsweeper'],      31.0 ],
            [ $w['l3reelgun'],       29.0 ],
            [ $w['longblaster'],    125.0 ],
            [ $w['nova'],           125.0 ],
            [ $w['nzap85'],          28.0 ],
            [ $w['prime'],           42.0 ],
            [ $w['promodeler_mg'],   24.5 ],
            [ $w['rapid'],           80.0 ],
            [ $w['rapid_elite'],     80.0 ],
            [ $w['sharp'],           26.0 ],
            [ $w['sshooter'],        36.0 ],
            [ $w['wakaba'],          28.0 ],

            // roller
            [ $w['carbon'],         125.0 ],
            [ $w['dynamo'],         125.0 ],
            [ $w['hokusai'],         37.0 ],
            [ $w['pablo'],           28.0 ],
            [ $w['splatroller'],    125.0 ],

            // charger
            [ $w['bamboo14mk1'],     80.0 ],
            [ $w['liter3k'],        180.0 ],
            [ $w['splatcharger'],   160.0 ],
            [ $w['squiclean_a'],    140.0 ],

            // slosher
            [ $w['bucketslosher'],   70.0 ],
            [ $w['hissen'],          62.0 ],
            [ $w['screwslosher'],    76.0 ],

            // spinner
            [ $w['barrelspinner'],   28.0 ],
            [ $w['hydra'],           28.0 ],
            [ $w['splatspinner'],    28.0 ],
        ]);

        // 2.2.0
        $version = SplatoonVersion::findOne(['tag' => '2.2.0'])->id;
        $this->batchInsert('weapon_attack', ['version_id', 'main_weapon_id', 'damage'], [
            [ $version, $w['sharp'],    28.0 ],
            [ $version, $w['sshooter'], 35.0 ],
        ]);

        // 2.7.0
        $version = SplatoonVersion::findOne(['tag' => '2.7.0'])->id;
        $this->batchInsert('weapon_attack', ['version_id', 'main_weapon_id', 'damage'], [
            [ $version, $w['96gal'],    52.0 ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('weapon_attack');
    }

    public function getWeapons()
    {
        $list = Weapon::find()
            ->andWhere('{{weapon}}.[[id]] = {{weapon}}.[[main_group_id]]')
            ->asArray()
            ->all();
        $ret = [];
        foreach ($list as $weapon) {
            $ret[$weapon['key']] = $weapon['id'];
        }
        return $ret;
    }
}
