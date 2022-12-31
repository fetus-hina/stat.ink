<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m170611_074525_e3_weapons extends Migration
{
    public function safeUp()
    {
        $this->registerWeapons(
            $this->registerTypes(),
            $this->registerSubs(),
            $this->registerSpecials()
        );
    }

    public function safeDown()
    {
        $this->unregisterWeapons();
        $this->unregisterSubs();
        $this->unregisterSpecials();
        $this->unregisterTypes();
    }

    private function registerSpecials(): array
    {
        $this->batchInsert('special2', ['key', 'name'], [
            [ 'amefurashi', 'Ink Storm' ],
        ]);
        return ArrayHelper::map(
            (new Query())
                ->select(['id', 'key'])
                ->from('special2')
                ->all(),
            'key',
            'id'
        );
    }

    private function unregisterSpecials(): void
    {
        $this->delete('special2', ['key' => 'amefurashi']);
    }

    private function registerSubs(): array
    {
        $this->batchInsert('subweapon2', ['key', 'name'], [
            [ 'jumpbeacon', 'Squid Beakon' ],
            [ 'pointsensor', 'Point Sensor' ],
            [ 'poisonmist', 'Toxic Mist' ],
            [ 'rocketbomb', 'Autobomb' ],
            [ 'splashshield', 'Splash Wall' ],
            [ 'sprinkler', 'Sprinkler' ],
            [ 'trap', 'Ink Mine' ],
        ]);
        return ArrayHelper::map(
            (new Query())
                ->select(['id', 'key'])
                ->from('subweapon2')
                ->all(),
            'key',
            'id'
        );
    }

    private function unregisterSubs(): void
    {
        $this->delete('subweapon2', ['key' => [
            'jumpbeacon',
            'pointsensor',
            'poisonmist',
            'rocketbomb',
            'splashshield',
            'sprinkler',
            'trap',
        ]]);
    }

    private function registerWeapons(array $types, array $subs, array $specials): void
    {
        $this->insert('weapon2', [
            'key' => 'prime',
            'name' => 'Splattershot Pro',
            'type_id' => $types['shooter'],
            'subweapon_id' => $subs['pointsensor'],
            'special_id' => $specials['amefurashi'],
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);
        $this->insert('weapon2', [
            'key' => 'sharp',
            'name' => 'Splash-o-matic',
            'type_id' => $types['shooter'],
            'subweapon_id' => $subs['poisonmist'],
            'special_id' => $specials['jetpack'],
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);
        $this->insert('weapon2', [
            'key' => 'hotblaster',
            'name' => 'Blaster',
            'type_id' => $types['blaster'],
            'subweapon_id' => $subs['poisonmist'],
            'special_id' => $specials['chakuchi'],
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);
        $this->insert('weapon2', [
            'key' => 'dynamo',
            'name' => 'Dynamo Roller',
            'type_id' => $types['roller'],
            'subweapon_id' => $subs['trap'],
            'special_id' => $specials['presser'],
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);
        $this->insert('weapon2', [
            'key' => 'hokusai',
            'name' => 'Octobrush',
            'type_id' => $types['brush'],
            'subweapon_id' => $subs['rocketbomb'],
            'special_id' => $specials['jetpack'],
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);
        $this->insert('weapon2', [
            'key' => 'splatscope',
            'name' => 'Splatterscope',
            'type_id' => $types['charger'],
            'subweapon_id' => $subs['splashbomb'],
            'special_id' => $specials['presser'],
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => (new Query())
                ->select('id')
                ->from('weapon2')
                ->where(['key' => 'splatcharger'])
                ->scalar(),
        ]);
        $this->insert('weapon2', [
            'key' => 'bucketslosher',
            'name' => 'Slosher',
            'type_id' => $types['slosher'],
            'subweapon_id' => $subs['kyubanbomb'],
            'special_id' => $specials['missile'],
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);
        $this->insert('weapon2', [
            'key' => 'barrelspinner',
            'name' => 'Heavy Splatling',
            'type_id' => $types['splatling'],
            'subweapon_id' => $subs['sprinkler'],
            'special_id' => $specials['presser'],
            'canonical_id'  => new Expression("currval('weapon2_id_seq'::regclass)"),
            'main_group_id' => new Expression("currval('weapon2_id_seq'::regclass)"),
        ]);
    }

    private function unregisterWeapons(): void
    {
        $this->delete('weapon2', ['key' => [
            'barrelspinner',
            'bucketslosher',
            'dynamo',
            'hokusai',
            'hotblaster',
            'prime',
            'sharp',
            'splatscope',
        ]]);
    }

    private function registerTypes(): array
    {
        $this->insert('weapon_type2', [
            'key' => 'slosher',
            'name' => 'Sloshers',
            'category_id' => (new Query())
                ->select('id')
                ->from('weapon_category2')
                ->where(['key' => 'slosher'])
                ->scalar(),
        ]);
        return ArrayHelper::map(
            (new Query())->select(['id', 'key'])->from('weapon_type2')->all(),
            'key',
            'id'
        );
    }

    private function unregisterTypes(): void
    {
        $this->delete('weapon_type2', ['key' => 'slosher']);
    }
}
