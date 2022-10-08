<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
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
 *
 * @property Battle3 $battle
 * @property SplashtagTitle3 $splashtagTitle
 * @property Weapon3 $weapon
 */
class BattlePlayer3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'battle_player3';
    }

    public function rules()
    {
        return [
            [['battle_id', 'is_our_team', 'is_me'], 'required'],
            [['battle_id', 'rank_in_team', 'weapon_id', 'inked', 'kill', 'assist', 'kill_or_assist', 'death', 'special', 'splashtag_title_id'], 'default', 'value' => null],
            [['battle_id', 'rank_in_team', 'weapon_id', 'inked', 'kill', 'assist', 'kill_or_assist', 'death', 'special', 'splashtag_title_id'], 'integer'],
            [['is_our_team', 'is_me', 'is_disconnected'], 'boolean'],
            [['name'], 'string', 'max' => 10],
            [['number'], 'string', 'max' => 32],
            [['battle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Battle3::class, 'targetAttribute' => ['battle_id' => 'id']],
            [['splashtag_title_id'], 'exist', 'skipOnError' => true, 'targetClass' => SplashtagTitle3::class, 'targetAttribute' => ['splashtag_title_id' => 'id']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
        ];
    }

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
        ];
    }

    public function getBattle(): ActiveQuery
    {
        return $this->hasOne(Battle3::class, ['id' => 'battle_id']);
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
