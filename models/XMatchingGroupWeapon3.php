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
 * This is the model class for table "x_matching_group_weapon3".
 *
 * @property integer $version_id
 * @property integer $weapon_id
 * @property integer $group_id
 *
 * @property XMatchingGroup3 $group
 * @property XMatchingGroupVersion3 $version
 * @property Weapon3 $weapon
 */
class XMatchingGroupWeapon3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'x_matching_group_weapon3';
    }

    public function rules()
    {
        return [
            [['version_id', 'weapon_id', 'group_id'], 'required'],
            [['version_id', 'weapon_id', 'group_id'], 'default', 'value' => null],
            [['version_id', 'weapon_id', 'group_id'], 'integer'],
            [['version_id', 'weapon_id'], 'unique', 'targetAttribute' => ['version_id', 'weapon_id']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => XMatchingGroup3::class, 'targetAttribute' => ['group_id' => 'id']],
            [['version_id'], 'exist', 'skipOnError' => true, 'targetClass' => XMatchingGroupVersion3::class, 'targetAttribute' => ['version_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'version_id' => 'Version ID',
            'weapon_id' => 'Weapon ID',
            'group_id' => 'Group ID',
        ];
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(XMatchingGroup3::class, ['id' => 'group_id']);
    }

    public function getVersion(): ActiveQuery
    {
        return $this->hasOne(XMatchingGroupVersion3::class, ['id' => 'version_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon3::class, ['id' => 'weapon_id']);
    }
}
