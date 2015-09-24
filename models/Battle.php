<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "battle".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $rule_id
 * @property integer $map_id
 * @property integer $weapon_id
 * @property integer $level
 * @property integer $rank_id
 * @property boolean $is_win
 * @property integer $rank_in_team
 * @property integer $kill
 * @property integer $death
 * @property string $at
 *
 * @property Map $map
 * @property Rank $rank
 * @property Rule $rule
 * @property User $user
 * @property Weapon $weapon
 * @property BattleGachi $battleGachi
 * @property BattleImage[] $battleImages
 * @property BattleNawabari $battleNawabari
 */
class Battle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'at'], 'required'],
            [['user_id', 'rule_id', 'map_id', 'weapon_id', 'level', 'rank_id'], 'integer'],
            [['rank_in_team', 'kill', 'death'], 'integer'],
            [['is_win'], 'boolean'],
            [['at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
            'weapon_id' => 'Weapon ID',
            'level' => 'Level',
            'rank_id' => 'Rank ID',
            'is_win' => 'Is Win',
            'rank_in_team' => 'Rank In Team',
            'kill' => 'Kill',
            'death' => 'Death',
            'at' => 'At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map::className(), ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRank()
    {
        return $this->hasOne(Rank::className(), ['id' => 'rank_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::className(), ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon::className(), ['id' => 'weapon_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattleGachi()
    {
        return $this->hasOne(BattleGachi::className(), ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattleImages()
    {
        return $this->hasMany(BattleImage::className(), ['battle_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattleNawabari()
    {
        return $this->hasOne(BattleNawabari::className(), ['id' => 'id']);
    }
}
