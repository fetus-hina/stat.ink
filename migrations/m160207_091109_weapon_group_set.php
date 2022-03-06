<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Weapon;
use yii\db\Migration;

class m160207_091109_weapon_group_set extends Migration
{
    public function safeUp()
    {
        $this->updateMainGroupId();
        $this->updateCanonicalId();
    }

    public function safeDown()
    {
        $this->execute('UPDATE {{weapon}} SET [[canonical_id]] = [[id]], [[main_group_id]] = [[id]]');
    }

    protected function updateMainGroupId()
    {
        $list = [
            '52gal' => ['52gal_deco'],
            '96gal' => ['96gal_deco'],
            'bold' => ['bold_neo'],
            'dualsweeper' => ['dualsweeper_custom'],
            'h3reelgun' => ['h3reelgun_d'],
            'hotblaster' => ['hotblaster_custom'],
            'jetsweeper' => ['jetsweeper_custom'],
            'l3reelgun' => ['l3reelgun_d'],
            'longblaster' => ['longblaster_custom'],
            'nova' => ['nova_neo'],
            'nzap85' => ['nzap89'],
            'prime' => ['prime_collabo'],
            'promodeler_mg' => ['promodeler_rg'],
            'rapid' => ['rapid_deco'],
            'rapid_elite' => ['rapid_elite_deco'],
            'sharp' => ['sharp_neo'],
            'sshooter' => ['sshooter_collabo', 'heroshooter_replica', 'octoshooter_replica'],
            'wakaba' => ['momiji'],

            'carbon' => ['carbon_deco'],
            'dynamo' => ['dynamo_tesla'],
            'hokusai' => ['hokusai_hue'],
            'pablo' => ['pablo_hue'],
            'splatroller' => ['splatroller_collabo', 'heroroller_replica'],

            'bamboo14mk1' => ['bamboo14mk2'],
            'liter3k' => ['liter3k_custom', 'liter3k_scope', 'liter3k_scope_custom'],
            'splatcharger' => ['splatcharger_wakame', 'splatscope', 'splatscope_wakame', 'herocharger_replica'],
            'squiclean_a' => ['squiclean_b'],

            'bucketslosher' => ['bucketslosher_deco'],
            'hissen' => ['hissen_hue'],
            'screwslosher' => ['screwslosher_neo'],

            'barrelspinner' => ['barrelspinner_deco'],
            'hydra' => ['hydra_custom'],
            'splatspinner' => ['splatspinner_collabo'],
        ];
        foreach ($list as $main => $aliases) {
            $id = Weapon::findOne(['key' => $main])->id;
            $this->update('weapon', ['main_group_id' => $id], ['key' => $aliases]);
        }
    }

    protected function updateCanonicalId()
    {
        $list = [
            'splatcharger'      => 'herocharger_replica',
            'splatroller'       => 'heroroller_replica',
            'sshooter'          => 'heroshooter_replica',
            'sshooter_collabo'  => 'octoshooter_replica',
        ];
        foreach ($list as $canonical => $alias) {
            $canonical_id = Weapon::findOne(['key' => $canonical])->id;
            $this->update('weapon', ['canonical_id' => $canonical_id], ['key' => $alias]);
        }
    }
}
