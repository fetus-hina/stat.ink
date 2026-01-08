<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_x_power_distrib_abstract3".
 *
 * @property integer $season_id
 * @property integer $rule_id
 * @property integer $users
 * @property double $average
 * @property double $stddev
 * @property string $median
 * @property string $pct5
 * @property string $pct25
 * @property string $pct75
 * @property string $pct80
 * @property string $pct95
 * @property integer $histogram_width
 *
 * @property Rule3 $rule
 * @property Season3 $season
 */
class StatXPowerDistribAbstract3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_x_power_distrib_abstract3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['stddev', 'median', 'pct5', 'pct25', 'pct75', 'pct80', 'pct95', 'histogram_width'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'users', 'average'], 'required'],
            [['season_id', 'rule_id', 'users', 'histogram_width'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'users', 'histogram_width'], 'integer'],
            [['average', 'stddev', 'median', 'pct5', 'pct25', 'pct75', 'pct80', 'pct95'], 'number'],
            [['season_id', 'rule_id'], 'unique', 'targetAttribute' => ['season_id', 'rule_id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'rule_id' => 'Rule ID',
            'users' => 'Users',
            'average' => 'Average',
            'stddev' => 'Stddev',
            'median' => 'Median',
            'pct5' => 'Pct5',
            'pct25' => 'Pct25',
            'pct75' => 'Pct75',
            'pct80' => 'Pct80',
            'pct95' => 'Pct95',
            'histogram_width' => 'Histogram Width',
        ];
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule3::class, ['id' => 'rule_id']);
    }

    public function getSeason(): ActiveQuery
    {
        return $this->hasOne(Season3::class, ['id' => 'season_id']);
    }
}
