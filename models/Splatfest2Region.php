<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatfest2_region".
 *
 * @property int $fest_id
 * @property int $region_id
 *
 * @property Splatfest2 $fest
 * @property Region2 $region
 */
class Splatfest2Region extends ActiveRecord
{
    public static function tableName()
    {
        return 'splatfest2_region';
    }

    public function rules()
    {
        return [
            [['fest_id', 'region_id'], 'required'],
            [['fest_id', 'region_id'], 'default', 'value' => null],
            [['fest_id', 'region_id'], 'integer'],
            [['fest_id', 'region_id'], 'unique',
                'targetAttribute' => ['fest_id', 'region_id'],
            ],
            [['region_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Region2::class,
                'targetAttribute' => ['region_id' => 'id'],
            ],
            [['fest_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Splatfest2::class,
                'targetAttribute' => ['fest_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'fest_id' => 'Fest ID',
            'region_id' => 'Region ID',
        ];
    }

    public function getFest(): ActiveQuery
    {
        return $this->hasOne(Splatfest2::class, ['id' => 'fest_id']);
    }

    public function getRegion(): ActiveQuery
    {
        return $this->hasOne(Region2::class, ['id' => 'region_id']);
    }
}
