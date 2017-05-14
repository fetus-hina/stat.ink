<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\behaviors\TrimAttributesBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "battle_player2".
 *
 * @property integer $id
 * @property integer $battle_id
 * @property boolean $is_my_team
 * @property boolean $is_me
 * @property integer $weapon_id
 * @property integer $level
 * @property integer $rank_in_team
 * @property integer $kill
 * @property integer $death
 * @property integer $point
 * @property integer $my_kill
 *
 * @property Battle2 $battle
 * @property Weapon2 $weapon
 */
class BattlePlayer2 extends ActiveRecord
{
    public function behaviors()
    {
        return [
            // [
            //     'class' => TrimAttributesBehavior::class,
            //     'targets' => array_keys($this->attributes),
            // ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_player2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['battle_id', 'is_my_team', 'is_me'], 'required'],
            [['battle_id', 'weapon_id'], 'integer'],
            [['level'], 'integer', 'min' => 1, 'max' => 50],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['kill', 'death', 'my_kill'], 'integer', 'min' => 0],
            [['point'], 'integer', 'min' => 0],
            [['is_my_team', 'is_me'], 'boolean'],
            [['battle_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Battle2::class,
                'targetAttribute' => ['battle_id' => 'id'],
            ],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'battle_id' => 'Battle ID',
            'is_my_team' => 'Is My Team',
            'is_me' => 'Is Me',
            'weapon_id' => 'Weapon ID',
            'level' => 'Level',
            'rank_in_team' => 'Rank In Team',
            'kill' => 'Kill',
            'death' => 'Death',
            'point' => 'Point',
            'my_kill' => 'My Kill',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattle()
    {
        return $this->hasOne(Battle2::class, ['id' => 'battle_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }

    public function toJsonArray()
    {
        return [
            'team'          => $this->is_my_team ? 'my' : 'his',
            'is_me'         => !!$this->is_me,
            'weapon'        => $this->weapon ? $this->weapon->toJsonArray() : null,
            'level'         => (string)$this->level === '' ? null : (int)$this->level,
            'rank_in_team'  => (string)$this->rank_in_team === '' ? null : (int)$this->rank_in_team,
            'kill'          => (string)$this->kill === '' ? null : (int)$this->kill,
            'death'         => (string)$this->death === '' ? null : (int)$this->death,
            'my_kill'       => (string)$this->my_kill === '' ? null : (int)$this->my_kill,
            'point'         => (string)$this->point === '' ? null : (int)$this->point,
        ];
    }
}
