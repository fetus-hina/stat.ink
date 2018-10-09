<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_wave2".
 *
 * @property integer $salmon_id
 * @property integer $wave
 * @property integer $event_id
 * @property integer $water_id
 * @property integer $golden_egg_quota
 * @property integer $golden_egg_appearances
 * @property integer $golden_egg_delivered
 * @property integer $power_egg_collected
 *
 * @property Salmon2 $salmon
 * @property SalmonEvent2 $event
 * @property SalmonWaterLevel2 $water
 */
class SalmonWave2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salmon_wave2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['salmon_id', 'wave'], 'required'],
                                                                                                   |
            [['salmon_id', 'wave', 'event_id', 'water_id', 'golden_egg_quota'], 'default',
                'value' => null,
            ],
            [['golden_egg_appearances', 'golden_egg_delivered', 'power_egg_collected'], 'default',
                'value' => null,
            ],
            [['salmon_id', 'wave', 'event_id', 'water_id', 'golden_egg_quota'], 'integer'],
            [['golden_egg_appearances', 'golden_egg_delivered', 'power_egg_collected'], 'integer'],
            [['salmon_id', 'wave'], 'unique', 'targetAttribute' => ['salmon_id', 'wave']],
            [['salmon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Salmon2::class,
                'targetAttribute' => ['salmon_id' => 'id'],
            ],
            [['event_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonEvent2::class,
                'targetAttribute' => ['event_id' => 'id'],
            ],
            [['water_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonWaterLevel2::class,
                'targetAttribute' => ['water_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'salmon_id' => 'Salmon ID',
            'wave' => 'Wave',
            'event_id' => 'Event ID',
            'water_id' => 'Water ID',
            'golden_egg_quota' => 'Golden Egg Quota',
            'golden_egg_appearances' => 'Golden Egg Appearances',
            'golden_egg_delivered' => 'Golden Egg Delivered',
            'power_egg_collected' => 'Power Egg Collected',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalmon()
    {
        return $this->hasOne(Salmon2::class, ['id' => 'salmon_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(SalmonEvent2::class, ['id' => 'event_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWater()
    {
        return $this->hasOne(SalmonWaterLevel2::class, ['id' => 'water_id']);
    }
}
