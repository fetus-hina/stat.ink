<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\WeaponType;
use yii\db\Migration;

class m150923_124509_weapon extends Migration
{
    public function up()
    {
        $this->createTable('weapon', [
            'id' => $this->primaryKey(),
            'type_id' => $this->integer()->notNull(),
            'key' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->addForeignKey('fk_weapon_type', 'weapon', 'type_id', 'weapon_type', 'id', 'RESTRICT');

        $shooter = $this->getType('shooter');
        $roller = $this->getType('roller');
        $charger = $this->getType('charger');
        $slosher = $this->getType('slosher');
        $splatling = $this->getType('splatling');

        $this->batchInsert('weapon', ['type_id', 'key', 'name'], [
            [ $shooter, '52gal', '.52 Gal' ],
            [ $shooter, '52gal_deco', '.52 Gal Deco' ],
            [ $shooter, '96gal', '.96 Gal' ],
            [ $shooter, '96gal_deco', '.96 Gal Deco' ],
            [ $shooter, 'bold', 'Sploosh-o-matic' ],
            [ $shooter, 'dualsweeper', 'Dual Squelcher' ],
            [ $shooter, 'dualsweeper_custom', 'Custom Dual Squelcher' ],
            [ $shooter, 'h3reelgun', 'H-3 Nozzlenose' ],
            [ $shooter, 'heroshooter_replica', 'Hero Shot Replica' ],
            [ $shooter, 'hotblaster', 'Blaster' ],
            [ $shooter, 'hotblaster_custom', 'Custom Blaster' ],
            [ $shooter, 'jetsweeper', 'Jet Squelcher' ],
            [ $shooter, 'jetsweeper_custom', 'Custom Jet Squelcher' ],
            [ $shooter, 'l3reelgun', 'L-3 Nozzlenose' ],
            [ $shooter, 'l3reelgun_d', 'L-3 Nozzlenose D' ],
            [ $shooter, 'longblaster', 'Range Blaster' ],
            [ $shooter, 'momiji', 'Custom Splattershot Jr.' ],
            [ $shooter, 'nova', 'Luna Blaster' ],
            [ $shooter, 'nzap85', "N-ZAP '85" ],
            [ $shooter, 'nzap89', "N-ZAP '89" ],
            [ $shooter, 'octoshooter_replica', 'Octoshot Replica' ],
            [ $shooter, 'prime', 'Splattershot Pro' ],
            [ $shooter, 'prime_collabo', 'Forge Splattershot Pro' ],
            [ $shooter, 'promodeler_mg', 'Aerospray MG' ],
            [ $shooter, 'promodeler_rg', 'Aerospray RG' ],
            [ $shooter, 'rapid', 'Rapid Blaster' ],
            [ $shooter, 'rapid_deco', 'Rapid Blaster Deco' ],
            [ $shooter, 'sharp', 'Splash-o-matic' ],
            [ $shooter, 'sharp_neo', 'Neo Splash-o-matic' ],
            [ $shooter, 'sshooter', 'Splattershot' ],
            [ $shooter, 'sshooter_collabo', 'Tentatek Splattershot' ],
            [ $shooter, 'wakaba', 'Splattershot Jr.' ],

            [ $roller, 'carbon', 'Carbon Roller' ],
            [ $roller, 'dynamo', 'Dynamo Roller' ],
            [ $roller, 'dynamo_tesla', 'Gold Dynamo Roller' ],
            [ $roller, 'heroroller_replica', 'Hero Roller Replica' ],
            [ $roller, 'hokusai', 'Octobrush' ],
            [ $roller, 'pablo', 'Inkbrush' ],
            [ $roller, 'pablo_hue', 'Inkbrush Nouveau' ],
            [ $roller, 'splatroller', 'Splat Roller' ],
            [ $roller, 'splatroller_collabo', 'Krak-On Splat Roller' ],

            [ $charger, 'bamboo14mk1', 'Bamboozler 14 MK I' ],
            [ $charger, 'herocharger_replica', 'Hero Charger Replica' ],
            [ $charger, 'liter3k', 'E-liter 3K' ],
            [ $charger, 'liter3k_custom', 'Custom E-liter 3K' ],
            [ $charger, 'liter3k_scope', 'E-liter 3K Scope' ],
            [ $charger, 'splatcharger', 'Splat Charger' ],
            [ $charger, 'splatcharger_wakame', 'Kelp Splat Charger' ],
            [ $charger, 'splatscope', 'Splatterscope' ],
            [ $charger, 'splatscope_wakame', 'Kelp Splatterscope' ],
            [ $charger, 'squiclean_a', 'Classic Squiffer' ],
            [ $charger, 'squiclean_b', 'New Squiffer' ],

            [ $slosher, 'bucketslosher', 'Slosher' ],
            [ $slosher, 'hissen', 'Tri-Slosher' ],

            [ $splatling, 'barrelspinner', 'Heavy Splatling' ],
            [ $splatling, 'splatspinner', 'Mini Splatling' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('weapon');
    }

    private function getType($key)
    {
        return WeaponType::findOne(['key' => $key])->id;
    }
}
