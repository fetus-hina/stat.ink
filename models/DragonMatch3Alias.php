<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "dragon_match3_alias".
 *
 * @property integer $id
 * @property integer $dragon_id
 * @property string $key
 *
 * @property DragonMatch3 $dragon
 */
class DragonMatch3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'dragon_match3_alias';
    }

    public function rules()
    {
        return [
            [['dragon_id', 'key'], 'required'],
            [['dragon_id'], 'default', 'value' => null],
            [['dragon_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['dragon_id'], 'exist', 'skipOnError' => true, 'targetClass' => DragonMatch3::class, 'targetAttribute' => ['dragon_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dragon_id' => 'Dragon ID',
            'key' => 'Key',
        ];
    }

    public function getDragon(): ActiveQuery
    {
        return $this->hasOne(DragonMatch3::class, ['id' => 'dragon_id']);
    }
}
