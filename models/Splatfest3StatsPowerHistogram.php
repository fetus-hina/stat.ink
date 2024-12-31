<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatfest3_stats_power_histogram".
 *
 * @property integer $splatfest_id
 * @property integer $class_value
 * @property integer $battles
 *
 * @property Splatfest3 $splatfest
 */
class Splatfest3StatsPowerHistogram extends ActiveRecord
{
    public static function tableName()
    {
        return 'splatfest3_stats_power_histogram';
    }

    public function rules()
    {
        return [
            [['splatfest_id', 'class_value', 'battles'], 'required'],
            [['splatfest_id', 'class_value', 'battles'], 'default', 'value' => null],
            [['splatfest_id', 'class_value', 'battles'], 'integer'],
            [['splatfest_id', 'class_value'], 'unique', 'targetAttribute' => ['splatfest_id', 'class_value']],
            [['splatfest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Splatfest3::class, 'targetAttribute' => ['splatfest_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'splatfest_id' => 'Splatfest ID',
            'class_value' => 'Class Value',
            'battles' => 'Battles',
        ];
    }

    public function getSplatfest(): ActiveQuery
    {
        return $this->hasOne(Splatfest3::class, ['id' => 'splatfest_id']);
    }
}
