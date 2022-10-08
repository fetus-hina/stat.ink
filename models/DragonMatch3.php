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
 * This is the model class for table "dragon_match3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Battle3[] $battle3s
 * @property DragonMatch3Alias[] $dragonMatch3Aliases
 */
class DragonMatch3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'dragon_match3';
    }

    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 63],
            [['key'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['fest_dragon_id' => 'id']);
    }

    public function getDragonMatch3Aliases(): ActiveQuery
    {
        return $this->hasMany(DragonMatch3Alias::class, ['dragon_id' => 'id']);
    }
}
