<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatfest2".
 *
 * @property integer $id
 * @property string $name_a
 * @property string $name_b
 * @property string $term
 *
 * @property Region2[] $regions
 * @property Splatfest2Region[] $splatfest2Regions
 */
class Splatfest2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'splatfest2';
    }

    public function rules()
    {
        return [
            [['name_a', 'name_b', 'term'], 'required'],
            [['term'], 'string'],
            [['name_a', 'name_b'], 'string', 'max' => 63],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name_a' => 'Name A',
            'name_b' => 'Name B',
            'term' => 'Term',
        ];
    }

    public function getRegions(): ActiveQuery
    {
        return $this->hasMany(Region2::class, ['id' => 'region_id'])
            ->viaTable('splatfest2_region', ['fest_id' => 'id']);
    }

    public function getSplatfest2Regions(): ActiveQuery
    {
        return $this->hasMany(Splatfest2Region::class, ['fest_id' => 'id']);
    }
}
