<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

use app\components\helpers\Battle;
use stdClass;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function time;

/**
 * This is the model class for table "period_map".
 *
 * @property integer $id
 * @property integer $period
 * @property integer $rule_id
 * @property integer $map_id
 *
 * @property Map $map
 * @property Rule $rule
 */
class PeriodMap extends ActiveRecord
{
    public static function findCurrentRegular(): ActiveQuery
    {
        return static::findByModeAndPeriod(
            'regular',
            Battle::calcPeriod(
                @$_SERVER['REQUEST_TIME'] ?: time(),
            ),
        );
    }

    public static function findCurrentGachi(): ActiveQuery
    {
        return static::findByModeAndPeriod(
            'gachi',
            Battle::calcPeriod(
                @$_SERVER['REQUEST_TIME'] ?: time(),
            ),
        );
    }

    public static function findNextRegular(): ActiveQuery
    {
        return static::findByModeAndPeriod(
            'regular',
            Battle::calcPeriod(
                @$_SERVER['REQUEST_TIME'] ?: time(),
            ),
        );
    }

    public static function findNextGachi(): ActiveQuery
    {
        return static::findByModeAndPeriod(
            'gachi',
            Battle::calcPeriod(
                @$_SERVER['REQUEST_TIME'] ?: time(),
            ),
        );
    }

    /**
     * @param string|string[] $mode
     * @param int|int[] $period
     * @return ActiveQuery<static>
     */
    public static function findByModeAndPeriod(string|array $mode, int|array $period): ActiveQuery
    {
        return static::find()
            ->innerJoinWith(['rule', 'rule.mode'])
            ->with(['rule', 'rule.mode', 'map'])
            ->andWhere(['{{game_mode}}.[[key]]' => $mode])
            ->andWhere(['{{period_map}}.[[period]]' => $period])
            ->orderBy('{{period_map}}.[[id]]');
    }

    public static function getSchedule(): stdClass
    {
        $currentPeriod = Battle::calcPeriod(
            @$_SERVER['REQUEST_TIME'] ?: time(),
        );
        $ret = (object)[
            'current' => (object)[
                't' => Battle::periodToRange($currentPeriod),
                'regular' => [],
                'gachi' => [],
            ],
            'next' => (object)[
                't' => Battle::periodToRange($currentPeriod + 1),
                'regular' => [],
                'gachi' => [],
            ],
        ];
        $list = static::findByModeAndPeriod(
            ['regular', 'gachi'],
            [$currentPeriod, $currentPeriod + 1],
        )->all();
        foreach ($list as $o) {
            $key = $o->period == $currentPeriod ? 'current' : 'next';
            $ret->$key->{$o->rule->mode->key}[] = $o;
        }
        return $ret;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'period_map';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['period', 'rule_id', 'map_id'], 'required'],
            [['period', 'rule_id', 'map_id'], 'integer'],
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
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map::class, ['id' => 'map_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::class, ['id' => 'rule_id']);
    }

    public function getWeaponTrends(): ActiveQuery
    {
        $query = StatWeaponMapTrend::find()
            ->andWhere([
                'rule_id' => $this->rule_id,
                'map_id' => $this->map_id,
            ])
            ->orderBy('[[battles]] DESC')
            ->with('weapon')
            ->limit(5);
        $query->multiple = true;
        return $query;
    }
}
