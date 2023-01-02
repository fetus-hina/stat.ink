<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;
use app\models\Ability2;
use app\models\Brand2;
use yii\helpers\ArrayHelper;

class m180427_125916_v3_gears extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        foreach ($this->upEach() as $item) {
            call_user_func_array([$this, 'upGear2'], $item);
        }
    }

    public function safeDown()
    {
        foreach ($this->upEach() as $item) {
            $this->downGear2($item[0]);
        }
    }

    // generator
    // [ str $key, str $name, str $type, str $brand, ?str $ability, ?int $splatnet ]
    protected function upEach()
    {
        $brands = ArrayHelper::map(
            Brand2::find()->asArray()->all(),
            'name',
            'key',
        );

        $abilities = ArrayHelper::map(
            Ability2::find()->asArray()->all(),
            'name',
            'key',
        );

        $type = null;
        if (!$fh = fopen(__FILE__, 'rt')) {
            throw new Exception('Could not open ' . __FILE__);
        }

        try {
            if (fseek($fh, __COMPILER_HALT_OFFSET__, SEEK_SET) === -1) {
                throw new Exception('Could not seek');
            }

            while (!feof($fh)) {
                $line = trim(fgets($fh));
                if ($line === '' || substr($line, 0, 1) === '#') {
                    continue;
                }

                if (substr($line, 0, 1) === '!') {
                    $type = substr($line, 1);
                    continue;
                }

                if (!preg_match('/^\d+/', $line)) {
                    throw new Exception('Invalid format ' . $line);
                }

                $data = explode(';', rtrim($line, ';'));
                if (count($data) !== 4) {
                    throw new Exception('Invalid number of data ' . json_encode($data));
                }

                if (!isset($brands[$data[2]])) {
                    throw new Exception('Unknown brand ' . $data[2]);
                }

                if (!isset($abilities[$data[3]])) {
                    throw new Exception('Unknown ability ' . $data[3]);
                }

                yield [
                    static::name2key($data[1]),
                    $data[1],
                    $type,
                    $brands[$data[2]],
                    $abilities[$data[3]],
                    (int)$data[0],
                ];
            }
        } finally {
            fclose($fh);
        }
    }
}

// phpcs:disable
__halt_compiler();
!headgear
1004;Squid-Stitch Cap;Skalop;Opening Gambit
1008;Zekko Mesh;Zekko;Quick Super Jump
1018;Long-Billed Cap;Krak-On;Ink Recovery Up
1025;Blowfish Newsie;Firefin;Quick Super Jump
1026;Do-Rag, Cap, & Glasses;Skalop;Cold-Blooded
1027;Pilot Hat;Splash Mob;Ink Resistance Up
2005;Squid Nordic;Skalop;Comeback
3006;White Arrowbands;Zekko;Special Power Up
3010;Octoglasses;Firefin;Last-Ditch Effort
3013;Zekko Cap;Zekko;Opening Gambit
3014;SV925 Circle Shades;Rockenberg;Swim Speed Up
3015;Annaki Beret & Glasses;Annaki;Ink Saver (Main)
3016;Swim Goggles;Zink;Last-Ditch Effort
3017;Ink-Guard Goggles;Toni Kensa;Run Speed Up
3018;Toni Kensa Goggles;Toni Kensa;Comeback
4012;Seashell Bamboo Hat;Inkline;Quick Respawn
4013;Hothouse Hat;Skalop;Ink Resistance Up
4014;Mountie Hat;Inkline;Special Charge Up
6000;Golf Visor;Zink;Run Speed Up
7009;Octo Tackle Helmet Deco;Forge;Bomb Defense Up
7011;Deca Tackle Visor Helmet;Forge;Sub Power Up
8000;Gas Mask;Forge;Tenacity
8006;Octoking Facemask;Enperry;Tenacity
8012;Digi-Camo Forge Mask;Forge;Swim Speed Up
9004;Jogging Headband;Zekko;Ink Saver (Sub)
9008;Black FishFry Bandana;Firefin;Bomb Defense Up

!clothing
1005;Rockenberg Black;Rockenberg;Respawn Punisher
1014;Gray Vector Tee;Takoroka;Quick Super Jump
1016;Ivory Peaks Tee;Inkline;Haunt
1017;Squid-Stitch Tee;Skalop;Swim Speed Up
1028;Octo Tee;Cuttlegear;Haunt
1029;Herbivore Tee;Firefin;Ninja Squid
1047;Annaki Polpo-Pic Tee;Annaki;Run Speed Up
1051;Missus Shrug Tee;Krak-On;Ink Saver (Sub)
1052;League Tee;SquidForce;Special Power Up
1053;Friend Tee;SquidForce;Thermal Ink
1056;Octoking HK Jersey;Enperry;Special Charge Up
1057;Dakro Nana Tee;Zink;Quick Respawn
1058;Dakro Golden Tee;Zink;Thermal Ink
1059;Black Velour Octoking Tee;Enperry;Cold-Blooded
1060;Green Velour Octoking Tee;Enperry;Special Saver
2008;White LS;SquidForce;Ink Recovery Up
2011;Zink LS;Zink;Special Power Up
2012;Striped Peaks LS;Inkline;Quick Super Jump
2020;Black Cuttlegear LS;Cuttlegear;Swim Speed Up
2021;Takoroka Crazy Baseball LS;Takoroka;Ninja Squid
2022;Red Cuttlegear LS;Cuttlegear;Bomb Defense Up
2023;Khaki 16-Bit FishFry;Firefin;Ink Recovery Up
2024;Blue 16-Bit FishFry;Firefin;Special Saver
3002;Camo Layered LS;SquidForce;Special Charge Up
3003;Black Layered LS;SquidForce;Ink Saver (Main)
3013;Squid Yellow Layered LS;SquidForce;Swim Speed Up
4001;Striped Rugby;Takoroka;Run Speed Up
5005;Green Cardigan;Splash Mob;Ink Saver (Sub)
5008;Retro Gamer Jersey;Zink;Quick Respawn
5011;Forge Octarian Jacket;Forge;Haunt
5025;Deep-Octo Satin Jacket;Zekko;Cold-Blooded
5038;White Leather F-3;Forge;Respawn Punisher
5039;Chili-Pepper Ski Jacket;Inkline;Ink Resistance Up
5040;Whale-Knit Sweater;Splash Mob;Run Speed Up
5041;Rockin' Leather Jacket;Annaki;Sub Power Up
5042;Kung-Fu Zip-Up;Toni Kensa;Ninja Squid
5043;Panda Kung-Fu Zip-Up;Toni Kensa;Sub Power Up
6006;Lob-Stars Jersey;Tentatek;Sub Power Up
7004;Navy College Sweat;Splash Mob;Ink Resistance Up
7011;Annaki Yellow Cuff;Annaki;Quick Respawn
7014;Octarian Retro;Cuttlegear;Respawn Punisher
7015;Takoroka Jersey;Takoroka;Special Power Up
8006;Red-Check Shirt;Zekko;Ink Saver (Main)
8024;Chili Octo Aloha;Krak-On;Bomb Defense Up
8025;Annaki Flannel Hoodie;Annaki;Bomb Defense Up
8026;Ink-Wash Shirt;Toni Kensa;Ink Recovery Up
8027;Dots-On-Dots Shirt;Skalop;Quick Super Jump
8028;Toni K. Baseball Jersey;Toni Kensa;Special Charge Up
9001;Forest Vest;Inkline;Ink Recovery Up
9004;Squid-Pattern Waistcoat;Krak-On;Special Power Up
9009;Silver Tentatek Vest;Tentatek;Thermal Ink
10007;Hothouse Hoodie;Skalop;Run Speed Up
10010;Black Hoodie;Skalop;Ink Resistance Up

!shoes
1001;Banana Basics;Krak-On;Bomb Defense Up
1002;LE Lo-Tops;Zekko;Ink Saver (Sub)
1004;Orange Lo-Tops;Zekko;Swim Speed Up
1007;Yellow Seahorses;Zink;Bomb Defense Up
1017;Suede Gray Lace-Ups;Zekko;Ink Recovery Up
1018;Suede Nation Lace-Ups;Zekko;Ink Saver (Main)
1019;Suede Marine Lace-Ups;Zekko;Ink Resistance Up
1020;Toni Kensa Soccer Shoes;Toni Kensa;Ink Saver (Main)
2002;Cream Hi-Tops;Krak-On;Stealth Jump
2010;Chocolate Dakroniks;Zink;Ink Resistance Up
2025;Red & White Squidkid V;Enperry;Run Speed Up
2026;Honey & Orange Squidkid V;Enperry;Ink Saver (Sub)
2032;Blue Iromaki 750s;Tentatek;Ink Saver (Main)
2033;Orange Iromaki 750s;Tentatek;Drop Roller
2034;Red Power Stripes;Takoroka;Run Speed Up
2035;Blue Power Stripes;Takoroka;Quick Respawn
2036;Toni Kensa Black Hi-Tops;Toni Kensa;Sub Power Up
2038;Black & Blue Squidkid V;Enperry;Special Saver
2039;Orca Passion Hi-Tops;Takoroka;Quick Super Jump
2040;Truffle Canvas Hi-Tops;Krak-On;Special Power Up
3005;Blue Sea Slugs;Tentatek;Special Charge Up
3015;N-Pacer CaO;Enperry;Object Shredder
3019;Athletic Arrows;Takoroka;Object Shredder
4012;Yellow Fishfry Sandals;Firefin;Cold-Blooded
4013;Musselforge Flip-Flops;Inkline;Ink Saver (Sub)
5001;Custom Trail Boots;Inkline;Special Power Up
6002;Red Work Boots;Rockenberg;Quick Super Jump
6008;Punk Yellows;Rockenberg;Special Saver
6011;Icy Down Boots;Tentatek;Stealth Jump
6016;Annaki Arachno Boots;Annaki;Swim Speed Up
6017;New-Leaf Leather Boots;Rockenberg;Drop Roller
6018;Tea-Green Hunting Boots;Splash Mob;Quick Respawn
8009;Inky Kid Clams;Rockenberg;Ink Recovery Up
8011;Annaki Tigers;Annaki;Special Power Up
