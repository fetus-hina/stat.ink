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
 * This is the model class for table "eggstra_work_official_result3".
 *
 * @property integer $schedule_id
 * @property integer $gold
 * @property integer $silver
 * @property integer $bronze
 *
 * @property SalmonSchedule3 $schedule
 */
class EggstraWorkOfficialResult3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'eggstra_work_official_result3';
    }

    public function rules()
    {
        return [
            [['schedule_id', 'gold', 'silver', 'bronze'], 'required'],
            [['schedule_id', 'gold', 'silver', 'bronze'], 'default', 'value' => null],
            [['schedule_id', 'gold', 'silver', 'bronze'], 'integer'],
            [['schedule_id'], 'unique'],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'schedule_id' => 'Schedule ID',
            'gold' => 'Gold',
            'silver' => 'Silver',
            'bronze' => 'Bronze',
        ];
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(SalmonSchedule3::class, ['id' => 'schedule_id']);
    }
}
