<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;

/**
 * This is the model class for table "ability".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Brand[] $strengthBrands
 * @property Brand[] $weaknessBrands
 */
class Ability extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ability';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique']
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
    public function getStrengthBrands()
    {
        return $this->hasMany(Brand::className(), ['strength_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeaknessBrands()
    {
        return $this->hasMany(Brand::className(), ['weakness_id' => 'id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-ability', $this->name),
        ];
    }
}
