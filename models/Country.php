<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "country".
 *
 * @property int $id
 * @property string $key
 * @property string $name
 *
 * @property TimezoneCountry[] $timezoneCountries
 * @property Timezone[] $timezones
 */
class Country extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 2],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTimezoneCountries()
    {
        return $this->hasMany(TimezoneCountry::class, ['country_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTimezones()
    {
        return $this->hasMany(Timezone::class, ['id' => 'timezone_id'])
            ->viaTable('timezone_country', ['country_id' => 'id']);
    }

    public function getRegionalIndicatorSymbols(): ?array // ?int[]
    {
        if (strlen($this->key) !== 2) {
            return null;
        }

        $results = [];
        for ($i = 0; $i < 2; ++$i) {
            $c = strtoupper(substr($this->key, $i, 1));
            if ($c < 'A' || $c > 'Z') {
                return null;
            }
            $results[] = 0x1f1e6 + ord($c) - ord('A');
        }
        return $results;
    }
}
