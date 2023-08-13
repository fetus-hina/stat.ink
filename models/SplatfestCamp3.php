<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatfest_camp3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Splatfest3[] $fests
 * @property SplatfestTeam3[] $splatfestTeam3s
 */
class SplatfestCamp3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'splatfest_camp3';
    }

    public function rules()
    {
        return [
            [['id', 'key', 'name'], 'required'],
            [['id'], 'default', 'value' => null],
            [['id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 127],
            [['key'], 'unique'],
            [['id'], 'unique'],
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
        return $this->hasMany(Splatfest3::class, ['id' => 'fest_id'])->viaTable('splatfest_team3', ['camp_id' => 'id']);
    }

    public function getSplatfestTeam3s(): ActiveQuery
    {
        return $this->hasMany(SplatfestTeam3::class, ['camp_id' => 'id']);
    }
}
