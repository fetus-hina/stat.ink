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
 * This is the model class for table "timezone_country".
 *
 * @property integer $timezone_id
 * @property integer $country_id
 *
 * @property Country $country
 * @property Timezone $timezone
 */
class TimezoneCountry extends ActiveRecord
{
    public static function tableName()
    {
        return 'timezone_country';
    }

    public function rules()
    {
        return [
            [['timezone_id', 'country_id'], 'required'],
            [['timezone_id', 'country_id'], 'default', 'value' => null],
            [['timezone_id', 'country_id'], 'integer'],
            [['timezone_id', 'country_id'], 'unique',
                'targetAttribute' => ['timezone_id', 'country_id'],
            ],
            [['country_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Country::class,
                'targetAttribute' => ['country_id' => 'id'],
            ],
            [['timezone_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Timezone::class,
                'targetAttribute' => ['timezone_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'timezone_id' => 'Timezone ID',
            'country_id' => 'Country ID',
        ];
    }

    public function getCountry(): ActiveQuery
    {
        return $this->hasOne(Country::class, ['id' => 'country_id']);
    }

    public function getTimezone(): ActiveQuery
    {
        return $this->hasOne(Timezone::class, ['id' => 'timezone_id']);
    }
}
