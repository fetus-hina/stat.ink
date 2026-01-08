<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use const SORT_ASC;

/**
 * This is the model class for table "timezone".
 *
 * @property integer $id
 * @property string $identifier
 * @property string $name
 * @property integer $order
 * @property integer $region_id
 * @property integer $group_id
 *
 * @property Region $region
 * @property TimezoneGroup $group
 * @property TimezoneCountry[] $timezoneCountries
 * @property Country[] $countries
 */
class Timezone extends ActiveRecord
{
    public static function find()
    {
        return parent::find()->orderBy([
            '{{timezone}}.[[order]]' => SORT_ASC,
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timezone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identifier', 'name', 'region_id'], 'required'],
            [['order', 'region_id', 'group_id'], 'default', 'value' => null],
            [['order', 'region_id', 'group_id'], 'integer'],
            [['identifier', 'name'], 'string', 'max' => 32],
            [['identifier'], 'unique'],
            [['name'], 'unique'],
            [['"order"'], 'unique'],
            [['region_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Region::class,
                'targetAttribute' => ['region_id' => 'id'],
            ],
            [['group_id'], 'exist', 'skipOnError' => true,
                'targetClass' => TimezoneGroup::class,
                'targetAttribute' => ['group_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'identifier' => 'Identifier',
            'name' => 'Name',
            'order' => 'Order',
            'region_id' => 'Region ID',
            'group_id' => 'Group ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::class, ['id' => 'region_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(TimezoneGroup::class, ['id' => 'group_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTimezoneCountries()
    {
        return $this->hasMany(TimezoneCountry::class, ['timezone_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasMany(Country::class, ['id' => 'country_id'])
            ->viaTable('timezone_country', ['timezone_id' => 'id'])
            ->orderBy([
                '{{country}}.[[key]]' => SORT_ASC,
            ]);
    }
}
