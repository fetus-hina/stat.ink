<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_uniform3_alias".
 *
 * @property integer $id
 * @property integer $uniform_id
 * @property string $key
 *
 * @property SalmonUniform3 $uniform
 */
class SalmonUniform3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_uniform3_alias';
    }

    public function rules()
    {
        return [
            [['uniform_id', 'key'], 'required'],
            [['uniform_id'], 'default', 'value' => null],
            [['uniform_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['uniform_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonUniform3::class, 'targetAttribute' => ['uniform_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uniform_id' => 'Uniform ID',
            'key' => 'Key',
        ];
    }

    public function getUniform(): ActiveQuery
    {
        return $this->hasOne(SalmonUniform3::class, ['id' => 'uniform_id']);
    }
}
