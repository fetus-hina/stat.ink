<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use app\components\helpers\Battle;
use stdClass;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "schedule2".
 *
 * @property int $id
 * @property int $period
 * @property int $mode_id
 * @property int $rule_id
 *
 * @property Rule2 $rule
 * @property ScheduleMode2 $mode
 * @property ScheduleMap2[] $scheduleMaps
 * @property Map2[] $maps
 */
class Schedule2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'schedule2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['period', 'mode_id', 'rule_id'], 'required'],
            [['period', 'mode_id', 'rule_id'], 'integer'],
            [['period', 'mode_id'], 'unique',
                'targetAttribute' => ['period', 'mode_id'],
                'message' => 'The combination of Period and Mode ID has already been taken.',
            ],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['mode_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ScheduleMode2::class,
                'targetAttribute' => ['mode_id' => 'id'],
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
            'period' => 'Period',
            'mode_id' => 'Mode ID',
            'rule_id' => 'Rule ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMode()
    {
        return $this->hasOne(ScheduleMode2::class, ['id' => 'mode_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getScheduleMaps()
    {
        return $this->hasMany(ScheduleMap2::class, ['schedule_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaps()
    {
        return $this->hasMany(Map2::class, ['id' => 'map_id'])->viaTable('schedule_map2', ['schedule_id' => 'id']);
    }

    public static function getInfo(): stdClass
    {
        $currentPeriod = Battle::calcPeriod2(
            (int)($_SERVER['REQUEST_TIME'] ?? time())
        );
        $formatter = fn (int $period): array => array_merge(
            ['_t' => Battle::periodToRange2($period)],
            ArrayHelper::map(
                static::find()
                        ->andWhere(['period' => $period])
                        ->with(['mode', 'rule', 'maps'])
                        ->all(),
                'mode.key',
                fn (self $model) => (object)[
                    'rule' => $model->rule,
                    'maps' => $model->maps,
                ]
            )
        );
        return (object)[
            'current' => (object)$formatter($currentPeriod),
            'next' => (object)$formatter($currentPeriod + 1),
        ];
    }
}
