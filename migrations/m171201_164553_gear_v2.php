<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\db\Query;

class m171201_164553_gear_v2 extends Migration
{
    public function safeUp()
    {
        if (!$data = $this->getData()) {
            return false;
        }
        $types = $this->types;
        $brands = $this->brands;
        $abilities = $this->abilities;
        $insert = array_map(
            function (array $row) use ($types, $brands, $abilities): array {
                return [
                    $row['key'],
                    $types[$row['type']],
                    $brands[$row['brand']],
                    $row['name'],
                    $abilities[$row['ability']],
                ];
            },
            $data,
        );
        $this->batchInsert(
            'gear2',
            ['key', 'type_id', 'brand_id', 'name', 'ability_id'],
            $insert,
        );
    }

    public function safeDown()
    {
        if (!$data = $this->getData()) {
            return false;
        }
        $this->delete('gear2', ['key' => array_map(
            function (array $row): string {
                return $row['key'];
            },
            $data,
        )]);
    }

    public function getData(): ?array
    {
        $result = [];
        if (!$fh = fopen(__FILE__, 'r')) {
            return null;
        }
        fseek($fh, __COMPILER_HALT_OFFSET__, SEEK_SET);
        while (!feof($fh)) {
            $line = trim(fgets($fh));
            if ($line != '') {
                $result[] = Json::decode($line);
            }
        }
        fclose($fh);
        return $result;
    }

    public function getTypes(): array
    {
        return ArrayHelper::map(
            (new Query())->select(['id', 'key'])->from('gear_type')->all(),
            'key',
            'id',
        );
    }

    public function getBrands(): array
    {
        return ArrayHelper::map(
            (new Query())->select(['id', 'key'])->from('brand2')->all(),
            'key',
            'id',
        );
    }

    public function getAbilities(): array
    {
        return ArrayHelper::map(
            (new Query())->select(['id', 'key'])->from('ability2')->all(),
            'key',
            'id',
        );
    }
}

// phpcs:disable

__halt_compiler();
{"key":"b_ball_headband","type":"headgear","brand":"zink","name":"B-ball Headband","ability":"opening_gambit"}
{"key":"black_arrowbands","type":"headgear","brand":"zekko","name":"Black Arrowbands","ability":"tenacity"}
{"key":"classic_straw_boater","type":"headgear","brand":"skalop","name":"Classic Straw Boater","ability":"special_power_up"}
{"key":"cycling_cap","type":"headgear","brand":"zink","name":"Cycling Cap","ability":"sub_power_up"}
{"key":"designer_headphones","type":"headgear","brand":"forge","name":"Designer Headphones","ability":"ink_saver_sub"}
{"key":"double_egg_shades","type":"headgear","brand":"zekko","name":"Double Egg Shades","ability":"run_speed_up"}
{"key":"face_visor","type":"headgear","brand":"toni_kensa","name":"Face Visor","ability":"bomb_defense_up"}
{"key":"fishfry_biscuit_bandana","type":"headgear","brand":"firefin","name":"FishFry Biscuit Bandana","ability":"special_power_up"}
{"key":"forge_mask","type":"headgear","brand":"forge","name":"Forge Mask","ability":"cold_blooded"}
{"key":"fugu_bell_hat","type":"headgear","brand":"firefin","name":"Fugu Bell Hat","ability":"quick_respawn"}
{"key":"full_moon_glasses","type":"headgear","brand":"krak_on","name":"Full Moon Glasses","ability":"quick_super_jump"}
{"key":"house_tag_denim_cap","type":"headgear","brand":"splash_mob","name":"House-Tag Denim Cap","ability":"special_charge_up"}
{"key":"jet_cap","type":"headgear","brand":"firefin","name":"Jet Cap","ability":"special_saver"}
{"key":"jungle_hat","type":"headgear","brand":"firefin","name":"Jungle Hat","ability":"ink_saver_main"}
{"key":"matte_bike_helmet","type":"headgear","brand":"zekko","name":"Matte Bike Helmet","ability":"sub_power_up"}
{"key":"moist_ghillie_helmet","type":"headgear","brand":"forge","name":"Moist Ghillie Helmet","ability":"run_speed_up"}
{"key":"motocross_nose_guard","type":"headgear","brand":"forge","name":"Motocross Nose Guard","ability":"special_charge_up"}
{"key":"paisley_bandana","type":"headgear","brand":"krak_on","name":"Paisley Bandana","ability":"ink_saver_sub"}
{"key":"short_beanie","type":"headgear","brand":"inkline","name":"Short Beanie","ability":"ink_saver_main"}
{"key":"skate_helmet","type":"headgear","brand":"skalop","name":"Skate Helmet","ability":"special_saver"}
{"key":"sneaky_beanie","type":"headgear","brand":"skalop","name":"Sneaky Beanie","ability":"swim_speed_up"}
{"key":"splash_goggles","type":"headgear","brand":"forge","name":"Splash Goggles","ability":"bomb_defense_up"}
{"key":"sporty_bobble_hat","type":"headgear","brand":"skalop","name":"Sporty Bobble Hat","ability":"tenacity"}
{"key":"squidlife_headphones","type":"headgear","brand":"forge","name":"Squidlife Headphones","ability":"ink_recovery_up"}
{"key":"stealth_goggles","type":"headgear","brand":"forge","name":"Stealth Goggles","ability":"swim_speed_up"}
{"key":"streetstyle_cap","type":"headgear","brand":"skalop","name":"Streetstyle Cap","ability":"ink_saver_sub"}
{"key":"treasure_hunter","type":"headgear","brand":"forge","name":"Treasure Hunter","ability":"ink_recovery_up"}
{"key":"tulip_parasol","type":"headgear","brand":"inkline","name":"Tulip Parasol","ability":"ink_resistance_up"}
{"key":"two_stripe_mesh","type":"headgear","brand":"krak_on","name":"Two-Stripe Mesh","ability":"special_saver"}
{"key":"woolly_urchins_classic","type":"headgear","brand":"krak_on","name":"Woolly Urchins Classic","ability":"comeback"}
{"key":"yamagiri_beanie","type":"headgear","brand":"inkline","name":"Yamagiri Beanie","ability":"cold_blooded"}
{"key":"aloha_shirt","type":"clothing","brand":"forge","name":"Aloha Shirt","ability":"ink_recovery_up"}
{"key":"annaki_blue_cuff","type":"clothing","brand":"annaki","name":"Annaki Blue Cuff","ability":"special_saver"}
{"key":"annaki_red_cuff","type":"clothing","brand":"annaki","name":"Annaki Red Cuff","ability":"haunt"}
{"key":"b_ball_jersey_home","type":"clothing","brand":"zink","name":"B-ball Jersey (Home)","ability":"special_saver"}
{"key":"baseball_jersey","type":"clothing","brand":"firefin","name":"Baseball Jersey","ability":"special_charge_up"}
{"key":"black_8_bit_fishfry","type":"clothing","brand":"firefin","name":"Black 8-Bit FishFry","ability":"bomb_defense_up"}
{"key":"black_anchor_tee","type":"clothing","brand":"squidforce","name":"Black Anchor Tee","ability":"respawn_punisher"}
{"key":"black_baseball_ls","type":"clothing","brand":"rockenberg","name":"Black Baseball LS","ability":"swim_speed_up"}
{"key":"black_polo","type":"clothing","brand":"zekko","name":"Black Polo","ability":"ninja_squid"}
{"key":"carnivore_tee","type":"clothing","brand":"firefin","name":"Carnivore Tee","ability":"bomb_defense_up"}
{"key":"custom_painted_f_3","type":"clothing","brand":"forge","name":"Custom Painted F-3","ability":"ink_resistance_up"}
{"key":"cycling_shirt","type":"clothing","brand":"zink","name":"Cycling Shirt","ability":"cold_blooded"}
{"key":"dark_bomber_jacket","type":"clothing","brand":"toni_kensa","name":"Dark Bomber Jacket","ability":"special_power_up"}
{"key":"firefin_navy_sweat","type":"clothing","brand":"firefin","name":"Firefin Navy Sweat","ability":"sub_power_up"}
{"key":"firewave_tee","type":"clothing","brand":"skalop","name":"Firewave Tee","ability":"special_charge_up"}
{"key":"fishing_vest","type":"clothing","brand":"inkline","name":"Fishing Vest","ability":"quick_respawn"}
{"key":"forge_inkling_parka","type":"clothing","brand":"forge","name":"Forge Inkling Parka","ability":"run_speed_up"}
{"key":"front_zip_vest","type":"clothing","brand":"toni_kensa","name":"Front Zip Vest","ability":"ink_resistance_up"}
{"key":"grape_tee","type":"clothing","brand":"skalop","name":"Grape Tee","ability":"ink_recovery_up"}
{"key":"gray_college_sweat","type":"clothing","brand":"splash_mob","name":"Gray College Sweat","ability":"swim_speed_up"}
{"key":"gray_mixed_shirt","type":"clothing","brand":"zekko","name":"Gray Mixed Shirt","ability":"quick_super_jump"}
{"key":"green_striped_ls","type":"clothing","brand":"inkline","name":"Green Striped LS","ability":"ninja_squid"}
{"key":"green_zip_hoodie","type":"clothing","brand":"firefin","name":"Green Zip Hoodie","ability":"special_power_up"}
{"key":"icewave_tee","type":"clothing","brand":"skalop","name":"Icewave Tee","ability":"ninja_squid"}
{"key":"kensa_coat","type":"clothing","brand":"toni_kensa","name":"Kensa Coat","ability":"respawn_punisher"}
{"key":"krak_on_528","type":"clothing","brand":"krak_on","name":"Krak-On 528","ability":"run_speed_up"}
{"key":"light_bomber_jacket","type":"clothing","brand":"toni_kensa","name":"Light Bomber Jacket","ability":"quick_super_jump"}
{"key":"linen_shirt","type":"clothing","brand":"splash_mob","name":"Linen Shirt","ability":"sub_power_up"}
{"key":"lumberjack_shirt","type":"clothing","brand":"rockenberg","name":"Lumberjack Shirt","ability":"ink_saver_main"}
{"key":"moist_ghillie_suit","type":"clothing","brand":"forge","name":"Moist Ghillie Suit","ability":"ink_saver_sub"}
{"key":"mountain_vest","type":"clothing","brand":"inkline","name":"Mountain Vest","ability":"swim_speed_up"}
{"key":"n_pacer_sweat","type":"clothing","brand":"enperry","name":"N-Pacer Sweat","ability":"thermal_ink"}
{"key":"navy_eminence_jacket","type":"clothing","brand":"enperry","name":"Navy Eminence Jacket","ability":"ink_saver_main"}
{"key":"olive_ski_jacket","type":"clothing","brand":"inkline","name":"Olive Ski Jacket","ability":"run_speed_up"}
{"key":"olive_zekko_parka","type":"clothing","brand":"zekko","name":"Olive Zekko Parka","ability":"swim_speed_up"}
{"key":"orange_cardigan","type":"clothing","brand":"splash_mob","name":"Orange Cardigan","ability":"special_charge_up"}
{"key":"part_time_pirate","type":"clothing","brand":"tentatek","name":"Part-Time Pirate","ability":"respawn_punisher"}
{"key":"pearl_tee","type":"clothing","brand":"skalop","name":"Pearl Tee","ability":"ink_saver_sub"}
{"key":"pink_hoodie","type":"clothing","brand":"splash_mob","name":"Pink Hoodie","ability":"bomb_defense_up"}
{"key":"pirate_stripe_tee","type":"clothing","brand":"splash_mob","name":"Pirate-Stripe Tee","ability":"special_power_up"}
{"key":"rainy_day_tee","type":"clothing","brand":"krak_on","name":"Rainy-Day Tee","ability":"ink_saver_main"}
{"key":"red_hula_punk_with_tie","type":"clothing","brand":"annaki","name":"Red Hula Punk with Tie","ability":"ink_resistance_up"}
{"key":"reggae_tee","type":"clothing","brand":"skalop","name":"Reggae Tee","ability":"special_saver"}
{"key":"rockenberg_white","type":"clothing","brand":"rockenberg","name":"Rockenberg White","ability":"ink_recovery_up"}
{"key":"rodeo_shirt","type":"clothing","brand":"krak_on","name":"Rodeo Shirt","ability":"quick_super_jump"}
{"key":"round_collar_shirt","type":"clothing","brand":"rockenberg","name":"Round-Collar Shirt","ability":"ink_saver_sub"}
{"key":"sage_polo","type":"clothing","brand":"splash_mob","name":"Sage Polo","ability":"cold_blooded"}
{"key":"school_jersey","type":"clothing","brand":"zink","name":"School Jersey","ability":"ninja_squid"}
{"key":"sky_blue_squideye","type":"clothing","brand":"tentatek","name":"Sky-Blue Squideye","ability":"cold_blooded"}
{"key":"squidmark_ls","type":"clothing","brand":"squidforce","name":"Squidmark LS","ability":"haunt"}
{"key":"squidmark_sweat","type":"clothing","brand":"squidforce","name":"Squidmark Sweat","ability":"sub_power_up"}
{"key":"squidstar_waistcoat","type":"clothing","brand":"krak_on","name":"Squidstar Waistcoat","ability":"cold_blooded"}
{"key":"striped_shirt","type":"clothing","brand":"splash_mob","name":"Striped Shirt","ability":"quick_super_jump"}
{"key":"takoroka_galactic_tie_dye","type":"clothing","brand":"takoroka","name":"Takoroka Galactic Tie Dye","ability":"thermal_ink"}
{"key":"takoroka_nylon_vintage","type":"clothing","brand":"takoroka","name":"Takoroka Nylon Vintage","ability":"thermal_ink"}
{"key":"takoroka_rainbow_tie_dye","type":"clothing","brand":"takoroka","name":"Takoroka Rainbow Tie Dye","ability":"quick_super_jump"}
{"key":"tentatek_slogan_tee","type":"clothing","brand":"tentatek","name":"Tentatek Slogan Tee","ability":"special_charge_up"}
{"key":"tricolor_rugby","type":"clothing","brand":"takoroka","name":"Tricolor Rugby","ability":"quick_respawn"}
{"key":"tumeric_zekko_coat","type":"clothing","brand":"zekko","name":"Tumeric Zekko Coat","ability":"thermal_ink"}
{"key":"varsity_baseball_ls","type":"clothing","brand":"splash_mob","name":"Varsity Baseball LS","ability":"haunt"}
{"key":"white_layered_ls","type":"clothing","brand":"squidforce","name":"White Layered LS","ability":"special_saver"}
{"key":"white_sailor_suit","type":"clothing","brand":"forge","name":"White Sailor Suit","ability":"ink_saver_main"}
{"key":"white_shirt","type":"clothing","brand":"splash_mob","name":"White Shirt","ability":"ink_recovery_up"}
{"key":"white_striped_ls","type":"clothing","brand":"splash_mob","name":"White Striped LS","ability":"quick_respawn"}
{"key":"zapfish_satin_jacket","type":"clothing","brand":"zekko","name":"Zapfish Satin Jacket","ability":"special_charge_up"}
{"key":"zekko_long_carrot_tee","type":"clothing","brand":"zekko","name":"Zekko Long Carrot Tee","ability":"ink_resistance_up"}
{"key":"zekko_long_radish_tee","type":"clothing","brand":"zekko","name":"Zekko Long Radish Tee","ability":"haunt"}
{"key":"omega_3_tee","type":"clothing","brand":"firefin","name":"\u03c9-3 Tee","ability":"respawn_punisher"}
{"key":"amber_sea_slug_hi_tops","type":"shoes","brand":"tentatek","name":"Amber Sea Slug Hi-Tops","ability":"drop_roller"}
{"key":"annaki_habaneros","type":"shoes","brand":"annaki","name":"Annaki Habaneros","ability":"sub_power_up"}
{"key":"black_seahorses","type":"shoes","brand":"zink","name":"Black Seahorses","ability":"swim_speed_up"}
{"key":"blue_laceless_dakroniks","type":"shoes","brand":"zink","name":"Blue Laceless Dakroniks","ability":"stealth_jump"}
{"key":"blue_lo_tops","type":"shoes","brand":"zekko","name":"Blue Lo-Tops","ability":"bomb_defense_up"}
{"key":"bubble_rain_boots","type":"shoes","brand":"inkline","name":"Bubble Rain Boots","ability":"drop_roller"}
{"key":"clowfish_basics","type":"shoes","brand":"krak_on","name":"Clowfish Basics","ability":"special_charge_up"}
{"key":"cyan_trainers","type":"shoes","brand":"tentatek","name":"Cyan Trainers","ability":"stealth_jump"}
{"key":"deepsea_leather_boots","type":"shoes","brand":"rockenberg","name":"Deepsea Leather Boots","ability":"ink_saver_sub"}
{"key":"gray_yellow_soled_wingtips","type":"shoes","brand":"rockenberg","name":"Gray Yellow-Soled Wingtips","ability":"quick_super_jump"}
{"key":"green_iromaki_750s","type":"shoes","brand":"tentatek","name":"Green Iromaki 750s","ability":"special_power_up"}
{"key":"green_laceups","type":"shoes","brand":"splash_mob","name":"Green Laceups","ability":"cold_blooded"}
{"key":"green_rain_boots","type":"shoes","brand":"inkline","name":"Green Rain Boots","ability":"stealth_jump"}
{"key":"luminous_delta_straps","type":"shoes","brand":"inkline","name":"Luminous Delta Straps","ability":"cold_blooded"}
{"key":"moist_ghillie_boots","type":"shoes","brand":"forge","name":"Moist Ghillie Boots","ability":"object_shredder"}
{"key":"n_pacer_ag","type":"shoes","brand":"enperry","name":"N-Pacer Ag","ability":"ink_recovery_up"}
{"key":"n_pacer_au","type":"shoes","brand":"enperry","name":"N-Pacer Au","ability":"quick_respawn"}
{"key":"navy_enperrials","type":"shoes","brand":"enperry","name":"Navy Enperrials","ability":"ink_saver_main"}
{"key":"navy_red_soled_wingtips","type":"shoes","brand":"rockenberg","name":"Navy Red-Soled Wingtips","ability":"ink_saver_main"}
{"key":"orca_woven_hi_tops","type":"shoes","brand":"takoroka","name":"Orca Woven Hi-Tops","ability":"ink_saver_sub"}
{"key":"polka_dot_slip_ons","type":"shoes","brand":"krak_on","name":"Polka-dot Slip-Ons","ability":"drop_roller"}
{"key":"punk_cherries","type":"shoes","brand":"rockenberg","name":"Punk Cherries","ability":"bomb_defense_up"}
{"key":"purple_iromaki_750s","type":"shoes","brand":"tentatek","name":"Purple Iromaki 750s","ability":"swim_speed_up"}
{"key":"red_fishfry_sandals","type":"shoes","brand":"firefin","name":"Red FishFry Sandals","ability":"object_shredder"}
{"key":"red_hi_tops","type":"shoes","brand":"krak_on","name":"Red Hi-Tops","ability":"ink_resistance_up"}
{"key":"red_iromaki_750s","type":"shoes","brand":"tentatek","name":"Red Iromaki 750s","ability":"ink_saver_sub"}
{"key":"red_sea_slugs","type":"shoes","brand":"tentatek","name":"Red Sea Slugs","ability":"special_saver"}
{"key":"red_slip_ons","type":"shoes","brand":"krak_on","name":"Red Slip-Ons","ability":"quick_super_jump"}
{"key":"shark_moccasins","type":"shoes","brand":"splash_mob","name":"Shark Moccasins","ability":"sub_power_up"}
{"key":"snowy_down_boots","type":"shoes","brand":"tentatek","name":"Snowy Down Boots","ability":"quick_super_jump"}
{"key":"soccer_shoes","type":"shoes","brand":"takoroka","name":"Soccer Shoes","ability":"bomb_defense_up"}
{"key":"squid_stitch_slip_ons","type":"shoes","brand":"krak_on","name":"Squid-Stitch Slip-Ons","ability":"bomb_defense_up"}
{"key":"squink_wingtips","type":"shoes","brand":"rockenberg","name":"Squink Wingtips","ability":"quick_respawn"}
{"key":"sun_and_shade_squidkid_iv","type":"shoes","brand":"enperry","name":"Sun & Shade Squidkid IV","ability":"cold_blooded"}
{"key":"tan_work_boots","type":"shoes","brand":"rockenberg","name":"Tan Work Boots","ability":"sub_power_up"}
{"key":"turquoise_kicks","type":"shoes","brand":"rockenberg","name":"Turquoise Kicks","ability":"special_charge_up"}
{"key":"violet_trainers","type":"shoes","brand":"tentatek","name":"Violet Trainers","ability":"object_shredder"}
{"key":"white_arrows","type":"shoes","brand":"takoroka","name":"White Arrows","ability":"special_power_up"}
{"key":"white_laceless_dakroniks","type":"shoes","brand":"zink","name":"White Laceless Dakroniks","ability":"run_speed_up"}
{"key":"yellow_iromaki_750s","type":"shoes","brand":"tentatek","name":"Yellow Iromaki 750s","ability":"special_saver"}
{"key":"zombie_hi_horses","type":"shoes","brand":"zink","name":"Zombie Hi-Horses","ability":"special_charge_up"}
