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

/**
 * @property-read stdClass $abilities
 * @property-read stdClass $brands
 */
final class m151227_094526_gear extends Migration
{
    public function up()
    {
        $this->createTable('gear_type', [
            'id'    => $this->primaryKey(),
            'key'   => 'VARCHAR(16) NOT NULL UNIQUE',
            'name'  => 'VARCHAR(32) NOT NULL',
        ]);
        $this->batchInsert('gear_type', ['key', 'name'], [
            [ 'headgear',   'Headgear' ],
            [ 'clothing',   'Clothing' ],
            [ 'shoes',      'Shoes' ],
        ]);

        $this->createTable('gear', [
            'id'            => $this->primaryKey(),
            'key'           => 'VARCHAR(32) NOT NULL UNIQUE',
            'type_id'       => 'INTEGER NOT NULL',
            'brand_id'      => 'INTEGER NOT NULL',
            'name'          => 'VARCHAR(32) NOT NULL',
            'ability_id'    => 'INTEGER NOT NULL',
        ]);
        $this->addForeignKey('fk_gear_1', 'gear', 'type_id', 'gear_type', 'id');
        $this->addForeignKey('fk_gear_2', 'gear', 'brand_id', 'brand', 'id');
        $this->addForeignKey('fk_gear_3', 'gear', 'ability_id', 'ability', 'id');

        $this->upHeadgear();
        $this->upClothing();
        $this->upShoes();
    }

    public function down()
    {
        $this->dropTable('gear');
        $this->dropTable('gear_type');
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
        $a = $this->abilities;
        $b = $this->brands;
        $this->batchInsert('gear', ['type_id', 'key', 'brand_id', 'name', 'ability_id'], [
            [ $type, '18k_aviators', $b->rockenberg, '18K Aviators', $a->last_ditch_effort ],
            [ $type, 'armor_helmet_replica', $b->cuttlegear, 'Armor Helmet Replica', $a->tenacity ],
            [ $type, 'b_ball_headband', $b->zink, 'B-ball Headband', $a->opening_gambit ],
            [ $type, 'backwards_cap', $b->zekko, 'Backwards Cap', $a->quick_respawn ],
            [ $type, 'bamboo_hat', $b->inkline, 'Bamboo Hat', $a->ink_saver_main ],
            [ $type, 'bike_helmet', $b->skalop, 'Bike Helmet', $a->ink_recovery_up ],
            [ $type, 'black_arrowbands', $b->zekko, 'Black Arrowbands', $a->tenacity ],
            [ $type, 'blowfish_bell_hat', $b->firefin, 'Blowfish Bell Hat', $a->ink_recovery_up ],
            [ $type, 'bobble_hat', $b->splash_mob, 'Bobble Hat', $a->quick_super_jump ],
            [ $type, 'camo_mesh', $b->firefin, 'Camo Mesh', $a->swim_speed_up ],
            [ $type, 'camping_hat', $b->inkline, 'Camping Hat', $a->special_duration_up ],
            [ $type, 'clycling_cap', $b->zink, 'Clycling Cap', $a->bomb_range_up ],
            [ $type, 'cycle_king_cap', $b->tentatek, 'Cycle King Cap', $a->defense_up ],
            [ $type, 'designer_headphones', $b->forge, 'Designer Headphones', $a->ink_saver_sub ],
            [ $type, 'fake_contacts', $b->tentatek, 'Fake Contacts', $a->special_charge_up ],
            [ $type, 'fishfry_visor', $b->firefin, 'FishFry Visor', $a->special_charge_up ],
            [ $type, 'five_panel_cap', $b->zekko, 'Five-Panel Cap', $a->comeback ],
            [ $type, 'gas_mask', $b->forge, 'Gas Mask', $a->tenacity ],
            [ $type, 'golf_visor', $b->zink, 'Golf Visor', $a->run_speed_up ],
            [ $type, 'hero_headset_replica', $b->cuttlegear, 'Hero Headset Replica', $a->run_speed_up ],
            [ $type, 'jet_cap', $b->firefin, 'Jet Cap', $a->special_saver ],
            [ $type, 'jogging_headband', $b->zekko, 'Jogging Headband', $a->ink_saver_sub ],
            [ $type, 'jungle_hat', $b->firefin, 'Jungle Hat', $a->ink_saver_main ],
            [ $type, 'legendary_cap', $b->cuttlegear, 'Legendary Cap', $a->damage_up ],
            [ $type, 'lightweight_cap', $b->inkline, 'Lightweight Cap', $a->swim_speed_up ],
            [ $type, 'noise_cancelers', $b->forge, 'Noise Cancelers', $a->quick_respawn ],
            [ $type, 'octoling_goggles', $b->cuttlegear, 'Octoling Goggles', $a->bomb_range_up ],
            [ $type, 'paintball_mask', $b->forge, 'Paintball Mask', $a->comeback ],
            [ $type, 'paisley_bandana', $b->krak_on, 'Paisley Bandana', $a->ink_saver_sub ],
            [ $type, 'pilot_goggles', $b->forge, 'Pilot Goggles', $a->bomb_range_up ],
            [ $type, 'power_mask', $b->amiibo, 'Power Mask', $a->defense_up ],
            [ $type, 'retro_specs', $b->splash_mob, 'Retro Specs', $a->quick_respawn ],
            [ $type, 'safari_hat', $b->forge, 'Safari Hat', $a->last_ditch_effort ],
            [ $type, 'samurai_helmet', $b->amiibo, 'Samurai Helmet', $a->damage_up ],
            [ $type, 'short_beanie', $b->inkline, 'Short Beanie', $a->ink_saver_main ],
            [ $type, 'skate_helmet', $b->skalop, 'Skate Helmet', $a->special_saver ],
            [ $type, 'skull_bandana', $b->forge, 'Skull Bandana', $a->special_saver ],
            [ $type, 'snorkel_mask', $b->forge, 'Snorkel Mask', $a->damage_up ],
            [ $type, 'splash_goggles', $b->forge, 'Splash Goggles', $a->defense_up ],
            [ $type, 'sporty_bobble_hat', $b->skalop, 'Sporty Bobble Hat', $a->tenacity ],
            [ $type, 'squash_headband', $b->zink, 'Squash Headband', $a->damage_up ],
            [ $type, 'squid_girl_hat', $b->squid_girl, 'SQUID GIRL Hat', $a->opening_gambit ],
            [ $type, 'squid_hairclip', $b->amiibo, 'Squid Hairclip', $a->swim_speed_up ],
            [ $type, 'squid_stitch_cap', $b->skalop, 'Squid-Stitch Cap', $a->opening_gambit ],
            [ $type, 'squidvader_cap', $b->skalop, 'Squidvader Cap', $a->special_charge_up ],
            [ $type, 'stealth_goggles', $b->forge, 'Stealth Goggles', $a->swim_speed_up ],
            [ $type, 'straw_boater', $b->skalop, 'Straw Boater', $a->quick_super_jump ],
            [ $type, 'streetstyle_cap', $b->skalop, 'Streetstyle Cap', $a->ink_saver_sub ],
            [ $type, 'striped_beanie', $b->splash_mob, 'Striped Beanie', $a->opening_gambit ],
            [ $type, 'studio_headphones', $b->forge, 'Studio Headphones', $a->ink_saver_main ],
            [ $type, 'sun_visor', $b->tentatek, 'Sun Visor', $a->bomb_range_up ],
            [ $type, 'takoroka_mesh', $b->takoroka, 'Takoroka Mesh', $a->defense_up ],
            [ $type, 'tennis_headband', $b->tentatek, 'Tennis Headband', $a->comeback ],
            [ $type, 'tentacles_helmet', $b->forge, 'Tentacles Helmet', $a->run_speed_up ],
            [ $type, 'tinted_shades', $b->zekko, 'Tinted Shades', $a->last_ditch_effort ],
            [ $type, 'traditional_headband', $b->famitsu, 'Traditional Headband', $a->comeback ],
            [ $type, 'two_stripe_mesh', $b->krak_on, 'Two-Stripe Mesh', $a->special_saver ],
            [ $type, 'urchins_cap', $b->skalop, 'Urchins Cap', $a->bomb_range_up ],
            [ $type, 'visor_skate_helmet', $b->skalop, 'Visor Skate Helmet', $a->last_ditch_effort ],
            [ $type, 'white_arrowbands', $b->zekko, 'White Arrowbands', $a->special_duration_up ],
            [ $type, 'white_headband', $b->squidforce, 'White Headband', $a->ink_recovery_up ],
            [ $type, 'zekko_mesh', $b->zekko, 'Zekko Mesh', $a->quick_super_jump ],
        ]);
    }

    private function upClothing()
    {
        $type = GearType::findOne(['key' => 'clothing'])->id;
        $a = $this->abilities;
        $b = $this->brands;
        $this->batchInsert('gear', ['type_id', 'key', 'brand_id', 'name', 'ability_id'], [
            [ $type, 'aloha_shirt', $b->forge, 'Aloha Shirt', $a->ink_recovery_up ],
            [ $type, 'anchor_sweat', $b->squidforce, 'Anchor Sweat', $a->cold_blooded ],
            [ $type, 'armor_jacket_replica', $b->cuttlegear, 'Armor Jacket Replica', $a->special_charge_up ],
            [ $type, 'b_ball_jersey_away', $b->zink, 'B-ball Jersey (Away)', $a->ink_saver_sub ],
            [ $type, 'b_ball_jersey_home', $b->zink, 'B-ball Jersey (Home)', $a->special_saver ],
            [ $type, 'baby_jelly_shirt', $b->splash_mob, 'Baby-Jelly Shirt', $a->defense_up ],
            [ $type, 'baseball_jersey', $b->firefin, 'Baseball Jersey', $a->special_charge_up ],
            [ $type, 'basic_tee', $b->squidforce, 'Basic Tee', $a->quick_respawn ],
            [ $type, 'berry_ski_jacket', $b->inkline, 'Berry Ski Jacket', $a->special_duration_up ],
            [ $type, 'black_8_bit_fishfry', $b->firefin, 'Black 8-Bit FishFry', $a->defense_up ],
            [ $type, 'black_anchor_tee', $b->squidforce, 'Black Anchor Tee', $a->recon ],
            [ $type, 'black_baseball_ls', $b->rockenberg, 'Black Baseball LS', $a->swim_speed_up ],
            [ $type, 'black_inky_rider', $b->rockenberg, 'Black Inky Rider', $a->bomb_range_up ],
            [ $type, 'black_layered_ls', $b->squidforce, 'Black Layered LS', $a->ink_saver_main ],
            [ $type, 'black_ls', $b->zekko, 'Black LS', $a->quick_super_jump ],
            [ $type, 'black_pipe_tee', $b->kog, 'Black Pipe Tee', $a->special_saver ],
            [ $type, 'black_squideye', $b->tentatek, 'Black Squideye', $a->run_speed_up ],
            [ $type, 'black_tee', $b->squidforce, 'Black Tee', $a->special_duration_up ],
            [ $type, 'blue_peaks_tee', $b->inkline, 'Blue Peaks Tee', $a->ink_saver_sub ],
            [ $type, 'camo_layered_ls', $b->squidforce, 'Camo Layered LS', $a->special_charge_up ],
            [ $type, 'camo_zip_hoodie', $b->firefin, 'Camo Zip Hoodie', $a->quick_respawn ],
            [ $type, 'carnivore_tee', $b->firefin, 'Carnivore Tee', $a->defense_up ],
            [ $type, 'choco_layered_ls', $b->takoroka, 'Choco Layered LS', $a->ink_saver_sub ],
            [ $type, 'cycle_king_jersey', $b->tentatek, 'Cycle King Jersey', $a->defense_up ],
            [ $type, 'cycling_shirt', $b->zink, 'Cycling Shirt', $a->cold_blooded ],
            [ $type, 'dark_urban_vest', $b->firefin, 'Dark Urban Vest', $a->cold_blooded ],
            [ $type, 'firefin_navy_sweat', $b->firefin, 'Firefin Navy Sweat', $a->bomb_range_up ],
            [ $type, 'grape_tee', $b->skalop, 'Grape Tee', $a->ink_recovery_up ],
            [ $type, 'gray_college_sweat', $b->splash_mob, 'Gray College Sweat', $a->swim_speed_up ],
            [ $type, 'gray_mixed_shirt', $b->zekko, 'Gray Mixed Shirt', $a->quick_super_jump ],
            [ $type, 'gray_vector_tee', $b->takoroka, 'Gray Vector Tee', $a->quick_super_jump ],
            [ $type, 'green_cardigan', $b->inkline, 'Green Cardigan', $a->recon ],
            [ $type, 'green_check_shirt', $b->zekko, 'Green-Check Shirt', $a->bomb_range_up ],
            [ $type, 'green_striped_ls', $b->inkline, 'Green Striped LS', $a->ninja_squid ],
            [ $type, 'green_tee', $b->forge, 'Green Tee', $a->special_saver ],
            [ $type, 'green_zip_hoodie', $b->firefin, 'Green Zip Hoodie', $a->special_duration_up ],
            [ $type, 'hero_jacket_replica', $b->cuttlegear, 'Hero Jacket Replica', $a->swim_speed_up ],
            [ $type, 'ivory_peaks_tee', $b->inkline, 'Ivory Peaks Tee', $a->haunt ],
            [ $type, 'layered_anchor_ls', $b->squidforce, 'Layered Anchor LS', $a->run_speed_up ],
            [ $type, 'layered_vector_ls', $b->takoroka, 'Layered Vector LS', $a->special_saver ],
            [ $type, 'linen_shirt', $b->splash_mob, 'Linen Shirt', $a->bomb_range_up ],
            [ $type, 'loga_aloha_shirt', $b->zekko, 'Loga Aloha Shirt', $a->ink_recovery_up ],
            [ $type, 'mint_tee', $b->skalop, 'Mint Tee', $a->defense_up ],
            [ $type, 'mountain_vest', $b->inkline, 'Mountain Vest', $a->swim_speed_up ],
            [ $type, 'navy_college_sweat', $b->splash_mob, 'Navy College Sweat', $a->damage_up ],
            [ $type, 'navy_striped_ls', $b->splash_mob, 'Navy Striped LS', $a->damage_up ],
            [ $type, 'octo_tee', $b->cuttlegear, 'Octo Tee', $a->haunt ],
            [ $type, 'octoling_armor', $b->cuttlegear, 'Octoling Armor', $a->ink_saver_sub ],
            [ $type, 'olive_ski_jacket', $b->inkline, 'Olive Ski Jacket', $a->recon ],
            [ $type, 'orange_cardigan', $b->splash_mob, 'Orange Cardigan', $a->special_charge_up ],
            [ $type, 'part_time_pirate', $b->tentatek, 'Part-Time Pirate', $a->damage_up ],
            [ $type, 'pirate_stripe_tee', $b->splash_mob, 'Pirate-Stripe Tee', $a->special_duration_up ],
            [ $type, 'power_armor', $b->amiibo, 'Power Armor', $a->quick_respawn ],
            [ $type, 'rainy_day_tee', $b->krak_on, 'Rainy-Day Tee', $a->ink_saver_main ],
            [ $type, 'red_check_shirt', $b->zekko, 'Red-Check Shirt', $a->ink_saver_main ],
            [ $type, 'red_vector_tee', $b->takoroka, 'Red Vector Tee', $a->ink_saver_main ],
            [ $type, 'retro_gamer_jersey', $b->squidforce, 'Retro Gamer Jersey', $a->quick_respawn ],
            [ $type, 'retro_sweat', $b->squidforce, 'Retro Sweat', $a->defense_up ],
            [ $type, 'rockenberg_black', $b->rockenberg, 'Rockenberg Black', $a->damage_up ],
            [ $type, 'rockenberg_white', $b->rockenberg, 'Rockenberg White', $a->ink_recovery_up ],
            [ $type, 'round_collar_shirt', $b->rockenberg, 'Round-Collar Shirt', $a->ink_saver_sub ],
            [ $type, 'sage_polo', $b->splash_mob, 'Sage Polo', $a->cold_blooded ],
            [ $type, 'sailor_stripe_tee', $b->splash_mob, 'Sailor-Stripe Tee', $a->run_speed_up ],
            [ $type, 'samurai_jacket', $b->amiibo, 'Samurai Jacket', $a->special_charge_up ],
            [ $type, 'school_uniform', $b->amiibo, 'School Uniform', $a->ink_recovery_up ],
            [ $type, 'shirt_and_tie', $b->splash_mob, 'Shirt & Tie', $a->special_saver ],
            [ $type, 'shrimp_pink_polo', $b->splash_mob, 'Shrimp-Pink Polo', $a->ninja_squid ],
            [ $type, 'sky_blue_squideye', $b->tentatek, 'Sky-Blue Squideye', $a->cold_blooded ],
            [ $type, 'splatfest_tee', $b->squidforce, 'Splatfest Tee', $a->special_saver ],
            [ $type, 'squid_girl_tunic', $b->squid_girl, 'SQUID GIRL Tunic', $a->damage_up ],
            [ $type, 'squid_pattern_waistcoat', $b->krak_on, 'Squid-Pattern Waistcoat', $a->special_duration_up ],
            [ $type, 'squid_satin_jacket', $b->zekko, 'Squid Satin Jacket', $a->quick_respawn ],
            [ $type, 'squid_stitch_tee', $b->skalop, 'Squid-Stitch Tee', $a->swim_speed_up ],
            [ $type, 'squidmark_ls', $b->squidforce, 'Squidmark LS', $a->haunt ],
            [ $type, 'squidmark_sweat', $b->squidforce, 'Squidmark Sweat', $a->bomb_range_up ],
            [ $type, 'striped_rugby', $b->takoroka, 'Striped Rugby', $a->run_speed_up ],
            [ $type, 'striped_shirt', $b->splash_mob, 'Striped Shirt', $a->quick_super_jump ],
            [ $type, 'sunny_day_tee', $b->krak_on, 'Sunny-Day Tee', $a->special_charge_up ],
            [ $type, 'traditional_apron', $b->famitsu, 'Traditional Apron', $a->quick_respawn ],
            [ $type, 'tricolor_rugby', $b->takoroka, 'Tricolor Rugby', $a->quick_respawn ],
            [ $type, 'urchins_jersey', $b->zink, 'Urchins Jersey', $a->run_speed_up ],
            [ $type, 'varsity_baseball_ls', $b->zekko, 'Varsity Baseball LS', $a->damage_up ],
            [ $type, 'varsity_jacket', $b->splash_mob, 'Varsity Jacket', $a->run_speed_up ],
            [ $type, 'vintage_check_shirt', $b->rockenberg, 'Vintage Check Shirt', $a->haunt ],
            [ $type, 'white_8_bit_fishfry', $b->firefin, 'White 8-Bit FishFry', $a->recon ],
            [ $type, 'white_anchor_tee', $b->squidforce, 'White Anchor Tee', $a->ninja_squid ],
            [ $type, 'white_baseball_ls', $b->rockenberg, 'White Baseball LS', $a->quick_super_jump ],
            [ $type, 'white_inky_rider', $b->rockenberg, 'White Inky Rider', $a->special_duration_up ],
            [ $type, 'white_layered_ls', $b->squidforce, 'White Layered LS', $a->special_saver ],
            [ $type, 'white_line_tee', $b->kog, 'White Line Tee', $a->swim_speed_up ],
            [ $type, 'white_shirt', $b->splash_mob, 'White Shirt', $a->ink_recovery_up ],
            [ $type, 'white_striped_ls', $b->splash_mob, 'White Striped LS', $a->quick_respawn ],
            [ $type, 'white_tee', $b->squidforce, 'White Tee', $a->ink_saver_sub ],
            [ $type, 'yellow_layered_ls', $b->squidforce, 'Yellow Layered LS', $a->quick_super_jump ],
            [ $type, 'yellow_urban_vest', $b->firefin, 'Yellow Urban Vest', $a->haunt ],
            [ $type, 'zapfish_satin_jacket', $b->zekko, 'Zapfish Satin Jacket', $a->special_charge_up ],
            [ $type, 'zekko_basseball_ls', $b->zekko, 'Zekko Basseball LS', $a->defense_up ],
            [ $type, 'zekko_hoodie', $b->zekko, 'Zekko Hoodie', $a->ninja_squid ],
            [ $type, 'zink_layered_ls', $b->zink, 'Zink Layered LS', $a->damage_up ],
            [ $type, 'zink_ls', $b->zink, 'Zink LS', $a->special_duration_up ],
        ]);
    }

    private function upShoes()
    {
        $type = GearType::findOne(['key' => 'shoes'])->id;
        $a = $this->abilities;
        $b = $this->brands;
        $this->batchInsert('gear', ['type_id', 'key', 'brand_id', 'name', 'ability_id'], [
            [ $type, 'acerola_rain_boots', $b->inkline, 'Acerola Rain Boots', $a->run_speed_up ],
            [ $type, 'armor_boot_replicas', $b->cuttlegear, 'Armor Boot Replicas', $a->ink_saver_main ],
            [ $type, 'banana_basics', $b->krak_on, 'Banana Basics', $a->bomb_sniffer ],
            [ $type, 'black_seahorses', $b->zink, 'Black Seahorses', $a->swim_speed_up ],
            [ $type, 'black_trainers', $b->tentatek, 'Black Trainers', $a->quick_respawn ],
            [ $type, 'blue_lo_tops', $b->zekko, 'Blue Lo-Tops', $a->defense_up ],
            [ $type, 'blue_moto_boots', $b->rockenberg, 'Blue Moto Boots', $a->ink_resistance_up ],
            [ $type, 'blue_slip_ons', $b->krak_on, 'Blue Slip-Ons', $a->bomb_range_up ],
            [ $type, 'blueberry_casuals', $b->krak_on, 'Blueberry Casuals', $a->ink_saver_sub ],
            [ $type, 'cherry_kicks', $b->rockenberg, 'Cherry Kicks', $a->stealth_jump ],
            [ $type, 'choco_clogs', $b->krak_on, 'Choco Clogs', $a->quick_respawn ],
            [ $type, 'clownfish_basics', $b->krak_on, 'Clownfish Basics', $a->special_charge_up ],
            [ $type, 'crazy_arrows', $b->takoroka, 'Crazy Arrows', $a->stealth_jump ],
            [ $type, 'cream_basics', $b->krak_on, 'Cream Basics', $a->special_saver ],
            [ $type, 'cream_hi_tops', $b->krak_on, 'Cream Hi-Tops', $a->stealth_jump ],
            [ $type, 'custom_trail_boots', $b->inkline, 'Custom Trail Boots', $a->special_duration_up ],
            [ $type, 'cyan_trainers', $b->tentatek, 'Cyan Trainers', $a->damage_up ],
            [ $type, 'gold_hi_horses', $b->zink, 'Gold Hi-Horses', $a->run_speed_up ],
            [ $type, 'green_rain_boots', $b->inkline, 'Green Rain Boots', $a->stealth_jump ],
            [ $type, 'hero_runner_replicas', $b->cuttlegear, 'Hero Runner Replicas', $a->quick_super_jump ],
            [ $type, 'hunter_hi_tops', $b->krak_on, 'Hunter Hi-Tops', $a->ink_recovery_up ],
            [ $type, 'le_lo_tops', $b->zekko, 'LE Lo-Tops', $a->ink_saver_sub ],
            [ $type, 'moto_boots', $b->rockenberg, 'Moto Boots', $a->quick_respawn ],
            [ $type, 'neon_sea_slugs', $b->tentatek, 'Neon Sea Slugs', $a->ink_resistance_up ],
            [ $type, 'octoling_boots', $b->cuttlegear, 'Octoling Boots', $a->special_saver ],
            [ $type, 'orange_arrows', $b->takoroka, 'Orange Arrows', $a->ink_saver_main ],
            [ $type, 'orange_lo_tops', $b->zekko, 'Orange Lo-Tops', $a->swim_speed_up ],
            [ $type, 'oyster_clogs', $b->krak_on, 'Oyster Clogs', $a->run_speed_up ],
            [ $type, 'pink_trainers', $b->tentatek, 'Pink Trainers', $a->bomb_range_up ],
            [ $type, 'plum_casuals', $b->krak_on, 'Plum Casuals', $a->damage_up ],
            [ $type, 'power_boots', $b->amiibo, 'Power Boots', $a->ink_saver_main ],
            [ $type, 'pro_trail_boots', $b->inkline, 'Pro Trail Boots', $a->bomb_sniffer ],
            [ $type, 'punk_whites', $b->rockenberg, 'Punk Whites', $a->special_duration_up ],
            [ $type, 'purple_hi_horses', $b->zink, 'Purple Hi-Horses', $a->special_duration_up ],
            [ $type, 'purple_sea_slugs', $b->tentatek, 'Purple Sea Slugs', $a->run_speed_up ],
            [ $type, 'red_hi_horses', $b->zink, 'Red Hi-Horses', $a->ink_saver_main ],
            [ $type, 'red_hi_tops', $b->krak_on, 'Red Hi-Tops', $a->ink_resistance_up ],
            [ $type, 'red_sea_slugs', $b->tentatek, 'Red Sea Slugs', $a->special_saver ],
            [ $type, 'red_slip_ons', $b->krak_on, 'Red Slip-Ons', $a->quick_super_jump ],
            [ $type, 'red_work_boots', $b->rockenberg, 'Red Work Boots', $a->quick_super_jump ],
            [ $type, 'samurai_shoes', $b->amiibo, 'Samurai Shoes', $a->special_duration_up ],
            [ $type, 'school_shoes', $b->amiibo, 'School Shoes', $a->ink_saver_sub ],
            [ $type, 'soccer_cleats', $b->takoroka, 'Soccer Cleats', $a->bomb_sniffer ],
            [ $type, 'squid_girl_shoes', $b->squid_girl, 'SQUID GIRL Shoes', $a->swim_speed_up ],
            [ $type, 'squid_stitch_slip_ons', $b->krak_on, 'Squid-Stitch Slip-Ons', $a->defense_up ],
            [ $type, 'squink_wingtops', $b->krak_on, 'Squink Wingtops', $a->quick_respawn ],
            [ $type, 'strapping_reds', $b->splash_mob, 'Strapping Reds', $a->ink_resistance_up ],
            [ $type, 'strapping_whites', $b->splash_mob, 'Strapping Whites', $a->ink_saver_sub ],
            [ $type, 'tan_work_boots', $b->rockenberg, 'Tan Work Boots', $a->bomb_range_up ],
            [ $type, 'traditional_sandals', $b->famitsu, 'Traditional Sandals', $a->run_speed_up ],
            [ $type, 'trail_boots', $b->inkline, 'Trail Boots', $a->ink_recovery_up ],
            [ $type, 'turquoise_kicks', $b->rockenberg, 'Turquoise Kicks', $a->special_charge_up ],
            [ $type, 'white_arrows', $b->takoroka, 'White Arrows', $a->special_duration_up ],
            [ $type, 'white_kicks', $b->rockenberg, 'White Kicks', $a->swim_speed_up ],
            [ $type, 'white_seahorses', $b->zink, 'White Seahorses', $a->ink_recovery_up ],
            [ $type, 'yellow_seahorses', $b->zink, 'Yellow Seahorses', $a->bomb_sniffer ],
            [ $type, 'zombie_hi_horses', $b->zink, 'Zombie Hi-Horses', $a->special_charge_up ],
        ]);
    }
}
