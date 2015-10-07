<?php
namespace app\models;

use Yii;
use app\components\helpers\Translator;

/**
 * This is the model class for table "battle_death_reason".
 *
 * @property integer $battle_id
 * @property integer $reason_id
 * @property integer $count
 *
 * @property Battle $battle
 * @property DeathReason $reason
 */
class BattleDeathReason extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_death_reason';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['battle_id', 'reason_id', 'count'], 'required'],
            [['battle_id', 'reason_id', 'count'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'battle_id' => 'Battle ID',
            'reason_id' => 'Reason ID',
            'count' => 'Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattle()
    {
        return $this->hasOne(Battle::className(), ['id' => 'battle_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReason()
    {
        return $this->hasOne(DeathReason::className(), ['id' => 'reason_id']);
    }

    protected function getRealReasonNames()
    {
        $reason = $this->reason;
        switch ($reason->type->key) {
            case 'main':
                return Translator::translateToAll('app-weapon', $reason->name);

            case 'sub':
                return Translator::translateToAll('app-subweapon', $reason->name);

            case 'special':
                return Translator::translateToAll('app-special', $reason->name);

            default:
                return Translator::translateToAll('app-death', $reason->name);
        }
    }

    public function toJsonArray()
    {
        return [
            'reason' => [
                'key' => $this->reason->key,
                'type' => $this->reason->type
                    ? [
                        'key' => $this->reason->type->key,
                        'name' => Translator::translateToAll('app-death', $this->reason->type->name),
                    ]
                    : [
                        'key' => null,
                        'name' => Translator::translateToAll('app-death', 'Unknown'),
                    ],
            ],
            'count' => (int)$this->count,
        ];
    }
}
