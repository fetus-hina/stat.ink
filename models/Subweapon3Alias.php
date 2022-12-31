<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "subweapon3_alias".
 *
 * @property integer $id
 * @property integer $subweapon_id
 * @property string $key
 *
 * @property Subweapon3 $subweapon
 */
class Subweapon3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'subweapon3_alias';
    }

    public function rules()
    {
        return [
            [['subweapon_id', 'key'], 'required'],
            [['subweapon_id'], 'default', 'value' => null],
            [['subweapon_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['subweapon_id', 'key'], 'unique', 'targetAttribute' => ['subweapon_id', 'key']],
            [['subweapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subweapon3::class, 'targetAttribute' => ['subweapon_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subweapon_id' => 'Subweapon ID',
            'key' => 'Key',
        ];
    }

    public function getSubweapon(): ActiveQuery
    {
        return $this->hasOne(Subweapon3::class, ['id' => 'subweapon_id']);
    }
}
