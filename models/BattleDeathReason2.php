<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "battle_death_reason2".
 *
 * @property integer $battle_id
 * @property integer $reason_id
 * @property integer $count
 *
 * @property Battle2 $battle
 * @property DeathReason2 $reason
 */
class BattleDeathReason2 extends ActiveRecord
{
    public static function find()
    {
        return parent::find()->with('reason');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_death_reason2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['battle_id', 'reason_id', 'count'], 'required'],
            [['battle_id', 'reason_id', 'count'], 'integer'],
            [['battle_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Battle2::class,
                'targetAttribute' => ['battle_id' => 'id'],
            ],
            [['reason_id'], 'exist', 'skipOnError' => true,
                'targetClass' => DeathReason2::class,
                'targetAttribute' => ['reason_id' => 'id'],
            ],
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
        return $this->hasOne(Battle2::class, ['id' => 'battle_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReason()
    {
        return $this->hasOne(DeathReason2::class, ['id' => 'reason_id']);
    }

    public function toJsonArray()
    {
        return [
            'reason' => $this->reason->toJsonArray(),
            'count' => (int)$this->count,
        ];
    }
}
