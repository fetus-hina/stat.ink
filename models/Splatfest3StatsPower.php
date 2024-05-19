<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatfest3_stats_power".
 *
 * @property integer $splatfest_id
 * @property integer $users
 * @property integer $battles
 * @property integer $agg_battles
 * @property double $average
 * @property double $stddev
 * @property double $minimum
 * @property double $p05
 * @property double $p25
 * @property double $p50
 * @property double $p75
 * @property double $p80
 * @property double $p95
 * @property double $maximum
 * @property integer $histogram_width
 * @property string $last_posted_at
 *
 * @property Splatfest3 $splatfest
 */
class Splatfest3StatsPower extends ActiveRecord
{
    public static function tableName()
    {
        return 'splatfest3_stats_power';
    }

    public function rules()
    {
        return [
            [['splatfest_id', 'users', 'battles', 'agg_battles', 'last_posted_at'], 'required'],
            [['splatfest_id', 'users', 'battles', 'agg_battles', 'histogram_width'], 'default', 'value' => null],
            [['splatfest_id', 'users', 'battles', 'agg_battles', 'histogram_width'], 'integer'],
            [['average', 'stddev', 'minimum', 'p05', 'p25', 'p50', 'p75', 'p80', 'p95', 'maximum'], 'number'],
            [['last_posted_at'], 'safe'],
            [['splatfest_id'], 'unique'],
            [['splatfest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Splatfest3::class, 'targetAttribute' => ['splatfest_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'splatfest_id' => 'Splatfest ID',
            'users' => 'Users',
            'battles' => 'Battles',
            'agg_battles' => 'Agg Battles',
            'average' => 'Average',
            'stddev' => 'Stddev',
            'minimum' => 'Minimum',
            'p05' => 'P05',
            'p25' => 'P25',
            'p50' => 'P50',
            'p75' => 'P75',
            'p80' => 'P80',
            'p95' => 'P95',
            'maximum' => 'Maximum',
            'histogram_width' => 'Histogram Width',
            'last_posted_at' => 'Last Posted At',
        ];
    }

    public function getSplatfest(): ActiveQuery
    {
        return $this->hasOne(Splatfest3::class, ['id' => 'splatfest_id']);
    }
}
