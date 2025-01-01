<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "battle_player".
 *
 * @property integer $id
 * @property integer $battle_id
 * @property boolean $is_my_team
 * @property boolean $is_me
 * @property integer $weapon_id
 * @property integer $rank_id
 * @property integer $level
 * @property integer $rank_in_team
 * @property integer $kill
 * @property integer $death
 * @property integer $point
 * @property integer $my_kill
 *
 * @property Battle $battle
 * @property Rank $rank
 * @property Weapon $weapon
 */
class BattlePlayer extends ActiveRecord
{
    public static function find()
    {
        return parent::find()
            ->with(['rank', 'weapon']);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_player';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['battle_id', 'is_my_team', 'is_me'], 'required'],
            [['battle_id', 'weapon_id', 'rank_id', 'level', 'rank_in_team', 'kill', 'death', 'point'], 'integer'],
            [['my_kill'], 'integer'],
            [['is_my_team', 'is_me'], 'boolean'],
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
            'rank_id' => 'Rank ID',
            'level' => 'Level',
            'rank_in_team' => 'Rank In Team',
            'kill' => 'Kill',
            'death' => 'Death',
            'point' => 'Point',
            'my_kill' => 'My Kill',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBattle()
    {
        return $this->hasOne(Battle::class, ['id' => 'battle_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRank()
    {
        return $this->hasOne(Rank::class, ['id' => 'rank_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon::class, ['id' => 'weapon_id']);
    }

    public function toJsonArray()
    {
        return [
            'team' => $this->is_my_team ? 'my' : 'his',
            'is_me' => !!$this->is_me,
            'weapon' => $this->weapon ? $this->weapon->toJsonArray() : null,
            'rank' => $this->rank ? $this->rank->toJsonArray() : null,
            'level' => (string)$this->level === '' ? null : (int)$this->level,
            'rank_in_team' => (string)$this->rank_in_team === '' ? null : (int)$this->rank_in_team,
            'kill' => (string)$this->kill === '' ? null : (int)$this->kill,
            'death' => (string)$this->death === '' ? null : (int)$this->death,
            'my_kill' => (string)$this->my_kill === '' ? null : (int)$this->my_kill,
            'point' => (string)$this->point === '' ? null : (int)$this->point,
        ];
    }
}
