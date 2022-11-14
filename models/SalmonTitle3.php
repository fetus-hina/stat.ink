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
 * This is the model class for table "salmon_title3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $rank
 *
 * @property Salmon3[] $salmon3s
 * @property Salmon3[] $salmon3s0
 * @property SalmonTitle3Alias[] $salmonTitle3Aliases
 */
class SalmonTitle3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_title3';
    }

    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
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

    public function getSalmon3s(): ActiveQuery
    {
        return $this->hasMany(Salmon3::class, ['title_before_id' => 'id']);
    }

    public function getSalmon3s0(): ActiveQuery
    {
        return $this->hasMany(Salmon3::class, ['title_after_id' => 'id']);
    }

    public function getSalmonTitle3Aliases(): ActiveQuery
    {
        return $this->hasMany(SalmonTitle3Alias::class, ['title_id' => 'id']);
    }
}
