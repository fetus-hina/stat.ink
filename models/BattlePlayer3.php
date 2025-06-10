<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "battle_player3".
 *
 * @property integer $id
 * @property integer $battle_id
 * @property boolean $is_our_team
 * @property boolean $is_me
 * @property integer $rank_in_team
 * @property string $name
 * @property integer $weapon_id
 * @property integer $inked
 * @property integer $kill
 * @property integer $assist
 * @property integer $kill_or_assist
 * @property integer $death
 * @property integer $special
 * @property boolean $is_disconnected
 * @property integer $splashtag_title_id
 * @property string $number
 * @property integer $headgear_id
 * @property integer $clothing_id
 * @property integer $shoes_id
 * @property boolean $is_crowned
 * @property integer $species_id
 * @property integer $crown_id
 *
 * @property Ability3[] $abilities
 * @property Battle3 $battle
 * @property BattlePlayerGearPower3[] $battlePlayerGearPower3s
 * @property GearConfiguration3 $clothing
 * @property Crown3 $crown
 * @property GearConfiguration3 $headgear
 * @property GearConfiguration3 $shoes
 * @property Species3 $species
 * @property SplashtagTitle3 $splashtagTitle
 * @property Weapon3 $weapon
 */
class BattlePlayer3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'battle_player3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['rank_in_team', 'name', 'weapon_id', 'inked', 'kill', 'assist', 'kill_or_assist', 'death', 'special', 'is_disconnected', 'splashtag_title_id', 'number', 'headgear_id', 'clothing_id', 'shoes_id', 'is_crowned', 'species_id', 'crown_id'], 'default', 'value' => null],
            [['battle_id', 'is_our_team', 'is_me'], 'required'],
            [['battle_id', 'rank_in_team', 'weapon_id', 'inked', 'kill', 'assist', 'kill_or_assist', 'death', 'special', 'splashtag_title_id', 'headgear_id', 'clothing_id', 'shoes_id', 'species_id', 'crown_id'], 'default', 'value' => null],
            [['battle_id', 'rank_in_team', 'weapon_id', 'inked', 'kill', 'assist', 'kill_or_assist', 'death', 'special', 'splashtag_title_id', 'headgear_id', 'clothing_id', 'shoes_id', 'species_id', 'crown_id'], 'integer'],
            [['is_our_team', 'is_me', 'is_disconnected', 'is_crowned'], 'boolean'],
            [['name'], 'string', 'max' => 10],
            [['number'], 'string', 'max' => 32],
            [['battle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Battle3::class, 'targetAttribute' => ['battle_id' => 'id']],
            [['crown_id'], 'exist', 'skipOnError' => true, 'targetClass' => Crown3::class, 'targetAttribute' => ['crown_id' => 'id']],
            [['headgear_id'], 'exist', 'skipOnError' => true, 'targetClass' => GearConfiguration3::class, 'targetAttribute' => ['headgear_id' => 'id']],
            [['clothing_id'], 'exist', 'skipOnError' => true, 'targetClass' => GearConfiguration3::class, 'targetAttribute' => ['clothing_id' => 'id']],
            [['shoes_id'], 'exist', 'skipOnError' => true, 'targetClass' => GearConfiguration3::class, 'targetAttribute' => ['shoes_id' => 'id']],
            [['species_id'], 'exist', 'skipOnError' => true, 'targetClass' => Species3::class, 'targetAttribute' => ['species_id' => 'id']],
            [['splashtag_title_id'], 'exist', 'skipOnError' => true, 'targetClass' => SplashtagTitle3::class, 'targetAttribute' => ['splashtag_title_id' => 'id']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'battle_id' => 'Battle ID',
            'is_our_team' => 'Is Our Team',
            'is_me' => 'Is Me',
            'rank_in_team' => 'Rank In Team',
            'name' => 'Name',
            'weapon_id' => 'Weapon ID',
            'inked' => 'Inked',
            'kill' => 'Kill',
            'assist' => 'Assist',
            'kill_or_assist' => 'Kill Or Assist',
            'death' => 'Death',
            'special' => 'Special',
            'is_disconnected' => 'Is Disconnected',
            'splashtag_title_id' => 'Splashtag Title ID',
            'number' => 'Number',
            'headgear_id' => 'Headgear ID',
            'clothing_id' => 'Clothing ID',
            'shoes_id' => 'Shoes ID',
            'is_crowned' => 'Is Crowned',
            'species_id' => 'Species ID',
            'crown_id' => 'Crown ID',
        ];
    }

    public function getAbilities(): ActiveQuery
    {
        return $this->hasMany(Ability3::class, ['id' => 'ability_id'])->viaTable('battle_player_gear_power3', ['player_id' => 'id']);
    }

    public function getBattle(): ActiveQuery
    {
        return $this->hasOne(Battle3::class, ['id' => 'battle_id']);
    }

    public function getBattlePlayerGearPower3s(): ActiveQuery
    {
        return $this->hasMany(BattlePlayerGearPower3::class, ['player_id' => 'id']);
    }

    public function getClothing(): ActiveQuery
    {
        return $this->hasOne(GearConfiguration3::class, ['id' => 'clothing_id']);
    }

    public function getCrown(): ActiveQuery
    {
        return $this->hasOne(Crown3::class, ['id' => 'crown_id']);
    }

    public function getHeadgear(): ActiveQuery
    {
        return $this->hasOne(GearConfiguration3::class, ['id' => 'headgear_id']);
    }

    public function getShoes(): ActiveQuery
    {
        return $this->hasOne(GearConfiguration3::class, ['id' => 'shoes_id']);
    }

    public function getSpecies(): ActiveQuery
    {
        return $this->hasOne(Species3::class, ['id' => 'species_id']);
    }

    public function getSplashtagTitle(): ActiveQuery
    {
        return $this->hasOne(SplashtagTitle3::class, ['id' => 'splashtag_title_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon3::class, ['id' => 'weapon_id']);
    }
}
