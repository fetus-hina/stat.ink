<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use app\models\query\MaintenanceScheduleQuery;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "maintenance_schedule".
 *
 * @property int $id
 * @property string $reason
 * @property string $start_at
 * @property string $end_at
 * @property bool $enabled
 */
class MaintenanceSchedule extends ActiveRecord
{
    public static function find(): ActiveQuery
    {
        return new MaintenanceScheduleQuery(static::class);
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
