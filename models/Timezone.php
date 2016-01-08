<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "timezone".
 *
 * @property integer $id
 * @property string $identifier
 * @property string $name
 * @property integer $order
 * @property integer $region_id
 *
 * @property Region $region
 * @property TimezoneCountry[] $timezoneCountries
 * @property Country[] $countries
 */
class Timezone extends \yii\db\ActiveRecord
{
    public static function find()
    {
        return parent::find()->orderBy('{{timezone}}.[[order]] ASC');
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
            [['order', 'region_id'], 'integer'],
            [['identifier', 'name'], 'string', 'max' => 32],
            [['identifier'], 'unique'],
            [['name'], 'unique'],
            [['"order"'], 'unique']
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTimezoneCountries()
    {
        return $this->hasMany(TimezoneCountry::className(), ['timezone_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasMany(Country::className(), ['id' => 'country_id'])
            ->viaTable('timezone_country', ['timezone_id' => 'id'])
            ->orderBy('{{country}}.[[key]]');
    }
}
