<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_uniform3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $rank
 *
 * @property SalmonPlayer3[] $salmonPlayer3s
 * @property SalmonUniform3Alias[] $salmonUniform3Aliases
 */
class SalmonUniform3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_uniform3';
    }

    public function rules()
    {
        return [
            [['key', 'name', 'rank'], 'required'],
            [['rank'], 'default', 'value' => null],
            [['rank'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
            [['key'], 'unique'],
            [['rank'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'rank' => 'Rank',
        ];
    }

    public function getSalmonPlayer3s(): ActiveQuery
    {
        return $this->hasMany(SalmonPlayer3::class, ['uniform_id' => 'id']);
    }

    public function getSalmonUniform3Aliases(): ActiveQuery
    {
        return $this->hasMany(SalmonUniform3Alias::class, ['uniform_id' => 'id']);
    }
}
