<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "weapon_category2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property WeaponType $weaponTypes
 */
class WeaponCategory2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'weapon_category2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['name'], 'unique'],
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
    public function getWeaponTypes()
    {
        return $this->hasMany(WeaponType2::class, ['category_id' => 'id']);
    }

    public function toJsonArray() : array
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-weapon2', $this->name),
        ];
    }
}
