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
 * @property string $start_at
 * @property string $end_at
 * @property string $agent
 * @property string $agent_version
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
            [['user_id', 'rule_id', 'map_id', 'weapon_id', 'level', 'rank_id', 'rank_in_team', 'kill', 'death'], 'integer'],
            [['is_win'], 'boolean'],
            [['start_at', 'end_at', 'at'], 'safe'],
            [['agent', 'agent_version'], 'string', 'max' => 16]
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
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'agent' => 'Agent',
            'agent_version' => 'Agent Version',
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

    public function getBattleImageJudge()
    {
        return $this->hasOne(BattleImage::className(), ['battle_id' => 'id'])
            ->andWhere(['type_id' => BattleImageType::ID_JUDGE]);
    }

    public function getBattleImageResult()
    {
        return $this->hasOne(BattleImage::className(), ['battle_id' => 'id'])
            ->andWhere(['type_id' => BattleImageType::ID_RESULT]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattleNawabari()
    {
        return $this->hasOne(BattleNawabari::className(), ['id' => 'id']);
    }

    public function getIsNawabari()
    {
        return $this->getIsThisGameMode('regular');
    }

    public function getIsGachi()
    {
        return $this->getIsThisGameMode('gachi');
    }

    private function getIsThisGameMode($key)
    {
        if ($this->rule_id === null) {
            return false;
        }
        if (!$rule = $this->getRule()->with('mode')->one()) {
            return false;
        }
        return $rule->mode && $rule->mode->key === $key;
    }

    public function getIsMeaningful()
    {
        $props = [
            'rule_id', 'map_id', 'weapon_id', 'is_win', 'rank_in_team', 'kill', 'death',
        ];
        foreach ($props as $prop) {
            if ($this->$prop !== null) {
                return true;
            }
        }
        return true;
    }
}
