<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "weapon3".
 *
 * @property integer $id
 * @property string $key
 * @property integer $mainweapon_id
 * @property integer $subweapon_id
 * @property integer $special_id
 * @property integer $canonical_id
 * @property string $name
 *
 * @property Weapon3 $canonical
 * @property Mainweapon3 $mainweapon
 * @property Special3 $special
 * @property Subweapon3 $subweapon
 * @property Weapon3Alias[] $weapon3Aliases
 * @property Weapon3[] $weapon3s
 */
class Weapon3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'weapon3';
    }

    public function rules()
    {
        return [
            [['key', 'mainweapon_id', 'name'], 'required'],
            [['mainweapon_id', 'subweapon_id', 'special_id', 'canonical_id'], 'default', 'value' => null],
            [['mainweapon_id', 'subweapon_id', 'special_id', 'canonical_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 48],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['mainweapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Mainweapon3::class, 'targetAttribute' => ['mainweapon_id' => 'id']],
            [['special_id'], 'exist', 'skipOnError' => true, 'targetClass' => Special3::class, 'targetAttribute' => ['special_id' => 'id']],
            [['subweapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subweapon3::class, 'targetAttribute' => ['subweapon_id' => 'id']],
            [['canonical_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapon3::class, 'targetAttribute' => ['canonical_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'mainweapon_id' => 'Mainweapon ID',
            'subweapon_id' => 'Subweapon ID',
            'special_id' => 'Special ID',
            'canonical_id' => 'Canonical ID',
            'name' => 'Name',
        ];
    }

    public function getCanonical(): ActiveQuery
    {
        return $this->hasOne(Weapon3::class, ['id' => 'canonical_id']);
    }

    public function getMainweapon(): ActiveQuery
    {
        return $this->hasOne(Mainweapon3::class, ['id' => 'mainweapon_id']);
    }

    public function getSpecial(): ActiveQuery
    {
        return $this->hasOne(Special3::class, ['id' => 'special_id']);
    }

    public function getSubweapon(): ActiveQuery
    {
        return $this->hasOne(Subweapon3::class, ['id' => 'subweapon_id']);
    }

    public function getWeapon3Aliases(): ActiveQuery
    {
        return $this->hasMany(Weapon3Alias::class, ['weapon_id' => 'id']);
    }

    public function getWeapon3s(): ActiveQuery
    {
        return $this->hasMany(Weapon3::class, ['canonical_id' => 'id']);
    }
}
