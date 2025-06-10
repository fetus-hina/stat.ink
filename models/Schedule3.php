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
 * This is the model class for table "schedule3".
 *
 * @property integer $id
 * @property integer $period
 * @property integer $lobby_id
 * @property integer $rule_id
 *
 * @property Lobby3 $lobby
 * @property Rule3 $rule
 * @property ScheduleMap3[] $scheduleMap3s
 */
class Schedule3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'schedule3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['period', 'lobby_id', 'rule_id'], 'required'],
            [['period', 'lobby_id', 'rule_id'], 'default', 'value' => null],
            [['period', 'lobby_id', 'rule_id'], 'integer'],
            [['period', 'lobby_id'], 'unique', 'targetAttribute' => ['period', 'lobby_id']],
            [['lobby_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lobby3::class, 'targetAttribute' => ['lobby_id' => 'id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'period' => 'Period',
            'lobby_id' => 'Lobby ID',
            'rule_id' => 'Rule ID',
        ];
    }

    public function getLobby(): ActiveQuery
    {
        return $this->hasOne(Lobby3::class, ['id' => 'lobby_id']);
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule3::class, ['id' => 'rule_id']);
    }

    public function getScheduleMap3s(): ActiveQuery
    {
        return $this->hasMany(ScheduleMap3::class, ['schedule_id' => 'id']);
    }
}
