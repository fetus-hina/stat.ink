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
 * This is the model class for table "x_matching_group_version3".
 *
 * @property integer $id
 * @property string $minimum_version
 *
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
            [['minimum_version'], 'required'],
            [['minimum_version'], 'string', 'max' => 16],
            [['minimum_version'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'minimum_version' => 'Minimum Version',
        ];
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
