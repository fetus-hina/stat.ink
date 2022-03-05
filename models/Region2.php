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
 * This is the model class for table "region2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Splatfest2[] $fests
 * @property Splatfest2Region[] $splatfest2Regions
 */
class Region2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'region2';
    }

    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 2],
            [['name'], 'string', 'max' => 63],
            [['key'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    public function getFests(): ActiveQuery
    {
        return $this->hasMany(Splatfest2::class, ['id' => 'fest_id'])
            ->viaTable('splatfest2_region', ['region_id' => 'id']);
    }

    public function getSplatfest2Regions(): ActiveQuery
    {
        return $this->hasMany(Splatfest2Region::class, ['region_id' => 'id']);
    }
}
