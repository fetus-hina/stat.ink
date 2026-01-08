<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "region".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Splatfest[] $splatfests
 * @property Timezone[] $timezones
 */
class Region extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'region';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 2],
            [['name'], 'string', 'max' => 64],
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
     * @return ActiveQuery
     */
    public function getSplatfests()
    {
        return $this->hasMany(Splatfest::class, ['region_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTimezones()
    {
        return $this->hasMany(Timezone::class, ['region_id' => 'id']);
    }
}
