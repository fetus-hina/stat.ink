<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_wave3".
 *
 * @property integer $id
 * @property integer $salmon_id
 * @property integer $wave
 * @property integer $tide_id
 * @property integer $event_id
 * @property integer $golden_quota
 * @property integer $golden_delivered
 * @property integer $golden_appearances
 *
 * @property SalmonEvent3 $event
 * @property Salmon3 $salmon
 * @property SalmonSpecialUse3[] $salmonSpecialUse3s
 * @property Special3[] $specials
 * @property SalmonWaterLevel2 $tide
 */
class SalmonWave3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_wave3';
    }

    public function rules()
    {
        return [
            [['salmon_id', 'wave'], 'required'],
            [['salmon_id', 'wave', 'tide_id', 'event_id', 'golden_quota', 'golden_delivered', 'golden_appearances'], 'default', 'value' => null],
            [['salmon_id', 'wave', 'tide_id', 'event_id', 'golden_quota', 'golden_delivered', 'golden_appearances'], 'integer'],
            [['salmon_id', 'wave'], 'unique', 'targetAttribute' => ['salmon_id', 'wave']],
            [['salmon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Salmon3::class, 'targetAttribute' => ['salmon_id' => 'id']],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonEvent3::class, 'targetAttribute' => ['event_id' => 'id']],
            [['tide_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonWaterLevel2::class, 'targetAttribute' => ['tide_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'salmon_id' => 'Salmon ID',
            'wave' => 'Wave',
            'tide_id' => 'Tide ID',
            'event_id' => 'Event ID',
            'golden_quota' => 'Golden Quota',
            'golden_delivered' => 'Golden Delivered',
            'golden_appearances' => 'Golden Appearances',
        ];
    }

    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(SalmonEvent3::class, ['id' => 'event_id']);
    }

    public function getSalmon(): ActiveQuery
    {
        return $this->hasOne(Salmon3::class, ['id' => 'salmon_id']);
    }

    public function getSalmonSpecialUse3s(): ActiveQuery
    {
        return $this->hasMany(SalmonSpecialUse3::class, ['wave_id' => 'id']);
    }

    public function getSpecials(): ActiveQuery
    {
        return $this->hasMany(Special3::class, ['id' => 'special_id'])->viaTable('salmon_special_use3', ['wave_id' => 'id']);
    }

    public function getTide(): ActiveQuery
    {
        return $this->hasOne(SalmonWaterLevel2::class, ['id' => 'tide_id']);
    }
}
