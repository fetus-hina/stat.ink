<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon3_user_stats_golden_egg_individual_histogram".
 *
 * @property integer $user_id
 * @property integer $map_id
 * @property integer $class_value
 * @property integer $count
 *
 * @property SalmonMap3 $map
 * @property User $user
 */
class Salmon3UserStatsGoldenEggIndividualHistogram extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon3_user_stats_golden_egg_individual_histogram';
    }

    #[Override]
    public function rules()
    {
        return [
            [['user_id', 'map_id', 'class_value', 'count'], 'required'],
            [['user_id', 'map_id', 'class_value', 'count'], 'default', 'value' => null],
            [['user_id', 'map_id', 'class_value', 'count'], 'integer'],
            [['user_id', 'map_id', 'class_value'], 'unique', 'targetAttribute' => ['user_id', 'map_id', 'class_value']],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonMap3::class, 'targetAttribute' => ['map_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'map_id' => 'Map ID',
            'class_value' => 'Class Value',
            'count' => 'Count',
        ];
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(SalmonMap3::class, ['id' => 'map_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
