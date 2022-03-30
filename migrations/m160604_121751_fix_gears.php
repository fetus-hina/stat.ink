<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Ability;
use app\models\Brand;
use app\models\GearType;
use yii\db\Migration;

class m160604_121751_fix_gears extends Migration
{
    public function safeUp()
    {
        $this->upHeadgear();
        $this->upClothing();
        $this->upShoes();
    }

    public function safeDown()
    {
        $keys = [
            'black_polo',
            'blue_sailor_suit',
            'blue_sea_slugs',
            'bubble_rain_boots',
            'classic_straw_boater',
            'fc_albacore',
            'forest_vest',
            'forge_inkling_parka',
            'forge_octarian_jacket',
            'fugu_tee',
            'full_moon_glasses',
            'herbivore_tee',
            'icy_down_boots',
            'krak_on_528',
            'le_soccer_cleats',
            'mawcasins',
            'octoglasses',
            'pearl_tee',
            'punk_cherries',
            'punk_yellows',
            'purple_camo_ls',
            'reel_sweat',
            'reggae_tee',
            'roasted_brogues',
            'shark_moccasins',
            'slipstream_united',
            'snowy_down_boots',
            'soccer_headband',
            'special_forces_beret',
            'squid_nordic',
            'squidstar_waistcoat',
            'striped_peaks_ls',
            'treasure_hunter',
            'white_sailor_suit',
        ];
        $this->delete('gear', ['key' => $keys]);
    }

    public function getAbilities()
    {
        $ret = [];
        foreach (Ability::find()->all() as $o) {
            $ret[$o->key] = $o->id;
        }
        return (object)$ret;
    }

    public function getBrands()
    {
        $ret = [];
        foreach (Brand::find()->all() as $o) {
            $ret[$o->key] = $o->id;
        }
        return (object)$ret;
    }

    private function upHeadgear()
    {
        $type = GearType::findOne(['key' => 'headgear'])->id;
        $a = $this->getAbilities();
        $b = $this->getBrands();
        $this->batchInsert('gear', ['type_id', 'key', 'brand_id', 'name', 'ability_id'], [
            [ $type, 'classic_straw_boater', $b->skalop,   'Classic Straw Boater', $a->special_duration_up ],
            [ $type, 'full_moon_glasses',    $b->krak_on,  'Full Moon Glasses',    $a->quick_super_jump ],
            [ $type, 'octoglasses',          $b->firefin,  'Octoglasses',          $a->last_ditch_effort ],
            [ $type, 'soccer_headband',      $b->tentatek, 'Soccer Headband',      $a->tenacity ],
            [ $type, 'special_forces_beret', $b->forge,    'Special Forces Beret', $a->opening_gambit ],
            [ $type, 'squid_nordic',         $b->skalop,   'Squid Nordic',         $a->comeback ],
            [ $type, 'treasure_hunter',      $b->forge,    'Treasure Hunter',      $a->ink_recovery_up ],
        ]);
    }

    private function upClothing()
    {
        $type = GearType::findOne(['key' => 'clothing'])->id;
        $a = $this->getAbilities();
        $b = $this->getBrands();
        $this->batchInsert('gear', ['type_id', 'key', 'brand_id', 'name', 'ability_id'], [
            [ $type, 'black_polo',            $b->zekko,    'Black Polo',            $a->recon ],
            [ $type, 'blue_sailor_suit',      $b->forge,    'Blue Sailor Suit',      $a->bomb_range_up ],
            [ $type, 'fc_albacore',           $b->takoroka, 'FC Albacore',           $a->damage_up ],
            [ $type, 'forest_vest',           $b->inkline,  'Forest Vest',           $a->ink_recovery_up ],
            [ $type, 'forge_inkling_parka',   $b->forge,    'Forge Inkling Parka',   $a->run_speed_up ],
            [ $type, 'forge_octarian_jacket', $b->forge,    'Forge Octarian Jacket', $a->haunt ],
            [ $type, 'fugu_tee',              $b->firefin,  'Fugu Tee',              $a->swim_speed_up ],
            [ $type, 'herbivore_tee',         $b->firefin,  'Herbivore Tee',         $a->ninja_squid ],
            [ $type, 'krak_on_528',           $b->krak_on,  'Krak-On 528',           $a->run_speed_up ],
            [ $type, 'pearl_tee',             $b->skalop,   'Pearl Tee',             $a->ink_saver_sub ],
            [ $type, 'purple_camo_ls',        $b->takoroka, 'Purple Camo LS',        $a->bomb_range_up ],
            [ $type, 'reel_sweat',            $b->zekko,    'Reel Sweat',            $a->special_duration_up ],
            [ $type, 'reggae_tee',            $b->skalop,   'Reggae Tee',            $a->special_saver ],
            [ $type, 'slipstream_united',     $b->takoroka, 'Slipstream United',     $a->defense_up ],
            [ $type, 'squidstar_waistcoat',   $b->krak_on,  'Squidstar Waistcoat',   $a->cold_blooded ],
            [ $type, 'striped_peaks_ls',      $b->inkline,  'Striped Peaks LS',      $a->quick_super_jump ],
            [ $type, 'white_sailor_suit',     $b->forge,    'White Sailor Suit',     $a->ink_saver_main ],
        ]);
    }

    private function upShoes()
    {
        $type = GearType::findOne(['key' => 'shoes'])->id;
        $a = $this->getAbilities();
        $b = $this->getBrands();
        $this->batchInsert('gear', ['type_id', 'key', 'brand_id', 'name', 'ability_id'], [
            [ $type, 'blue_sea_slugs',    $b->tentatek,   'Blue Sea Slugs',    $a->special_charge_up ],
            [ $type, 'bubble_rain_boots', $b->inkline,    'Bubble Rain Boots', $a->damage_up ],
            [ $type, 'icy_down_boots',    $b->tentatek,   'Icy Down Boots',    $a->stealth_jump ],
            [ $type, 'le_soccer_cleats',  $b->takoroka,   'LE Soccer Cleats',  $a->ink_resistance_up ],
            [ $type, 'mawcasins',         $b->splash_mob, 'Mawcasins',         $a->ink_recovery_up ],
            [ $type, 'punk_cherries',     $b->rockenberg, 'Punk Cherries',     $a->bomb_sniffer ],
            [ $type, 'punk_yellows',      $b->rockenberg, 'Punk Yellows',      $a->special_saver ],
            [ $type, 'roasted_brogues',   $b->rockenberg, 'Roasted Brogues',   $a->defense_up ],
            [ $type, 'shark_moccasins',   $b->splash_mob, 'Shark Moccasins',   $a->bomb_range_up ],
            [ $type, 'snowy_down_boots',  $b->tentatek,   'Snowy Down Boots',  $a->quick_super_jump ],
        ]);
    }
}
