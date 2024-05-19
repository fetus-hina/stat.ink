<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "x_matching_group_version3".
 *
 * @property integer $id
 * @property string $minimum_version
 * @property integer $minimum_season_id
 *
 * @property Season3 $minimumSeason
 * @property Weapon3[] $weapons
 * @property XMatchingGroupWeapon3[] $xMatchingGroupWeapon3s
 */
class XMatchingGroupVersion3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'x_matching_group_version3';
    }

    public function rules()
    {
        return [
            [['minimum_version', 'minimum_season_id'], 'required'],
            [['minimum_season_id'], 'default', 'value' => null],
            [['minimum_season_id'], 'integer'],
            [['minimum_version'], 'string', 'max' => 16],
            [['minimum_version'], 'unique'],
            [['minimum_season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['minimum_season_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'minimum_version' => 'Minimum Version',
            'minimum_season_id' => 'Minimum Season ID',
        ];
    }

    public function getMinimumSeason(): ActiveQuery
    {
        return $this->hasOne(Season3::class, ['id' => 'minimum_season_id']);
    }

    public function getWeapons(): ActiveQuery
    {
        return $this->hasMany(Weapon3::class, ['id' => 'weapon_id'])->viaTable('x_matching_group_weapon3', ['version_id' => 'id']);
    }

    public function getXMatchingGroupWeapon3s(): ActiveQuery
    {
        return $this->hasMany(XMatchingGroupWeapon3::class, ['version_id' => 'id']);
    }
}
