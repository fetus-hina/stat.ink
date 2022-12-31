<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use app\components\helpers\db\Now;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "maintenance_schedule".
 *
 * @property integer $id
 * @property string $reason
 * @property string $start_at
 * @property string $end_at
 * @property boolean $enabled
 */
class MaintenanceSchedule extends ActiveRecord
{
    public static function find(): ActiveQuery
    {
        return new class (static::class) extends ActiveQuery {
            public function enabled(): ActiveQuery
            {
                $table = MaintenanceSchedule::tableName();
                $this->andWhere(['and',
                    ["{{{$table}}}.[[enabled]]" => true],
                    ['>', "{{{$table}}}.[[end_at]]", new Now()],
                ]);
                return $this;
            }

            public function recently(): ActiveQuery
            {
                $table = MaintenanceSchedule::tableName();
                $this->orderBy([
                    "{{{$table}}}.[[start_at]]" => SORT_ASC,
                ]);
                return $this;
            }
        };
    }

    public static function tableName()
    {
        return 'maintenance_schedule';
    }

    public function rules()
    {
        return [
            [['reason', 'start_at', 'end_at'], 'required'],
            [['reason'], 'string'],
            [['start_at', 'end_at'], 'safe'],
            [['enabled'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reason' => 'Reason',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'enabled' => 'Enabled',
        ];
    }
}
