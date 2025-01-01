<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function array_merge;

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
    use openapi\Util;

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
            'golden_egg_quota' => 'Quota',
            'golden_egg_appearances' => 'Golden Eggs Appearances',
            'golden_egg_delivered' => 'Golden Eggs Delivered',
            'power_egg_collected' => 'Power Eggs Collected',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getSalmon()
    {
        return $this->hasOne(Salmon2::class, ['id' => 'salmon_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(SalmonEvent2::class, ['id' => 'event_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWater()
    {
        return $this->hasOne(SalmonWaterLevel2::class, ['id' => 'water_id']);
    }

    public function toJsonArray(): array
    {
        return [
            'known_occurrence' => $this->event_id ? $this->event->toJsonArray() : null,
            'water_level' => $this->water_id ? $this->water->toJsonArray() : null,
            'golden_egg_quota' => $this->golden_egg_quota,
            'golden_egg_appearances' => $this->golden_egg_appearances,
            'golden_egg_delivered' => $this->golden_egg_delivered,
            'power_egg_collected' => $this->power_egg_collected,
        ];
    }

    public static function openApiSchema(): array
    {
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Wave information'),
            'properties' => [
                'known_occurrence' => array_merge(SalmonEvent2::openApiSchema(), [
                    'nullable' => true,
                ]),
                'water_level' => array_merge(SalmonWaterLevel2::openApiSchema(), [
                    'nullable' => true,
                ]),
                'golden_egg_quota' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 1,
                    'maximum' => 25,
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Quota'),
                ],
                'golden_egg_appearances' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Golden Egg appearances'),
                ],
                'golden_egg_delivered' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Golden Eggs delivered'),
                ],
                'power_egg_collected' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Power Eggs collected'),
                ],
            ],
            'example' => static::openapiExample(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
        ];
    }

    public static function openapiExample(): array
    {
        return [
            'known_occurrence' => SalmonEvent2::openapiExample(),
            'water_level' => SalmonWaterLevel2::openapiExample(),
            'golden_egg_quota' => 21,
            'golden_egg_appearances' => 30,
            'golden_egg_delivered' => 24,
            'power_egg_collected' => 1200,
        ];
    }
}
