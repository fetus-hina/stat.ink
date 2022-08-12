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
 * This is the model class for table "special3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Special3Alias[] $special3Aliases
 */
class Special3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'special3';
    }

    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 48],
            [['key'], 'unique'],
            [['name'], 'unique'],
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

    public function getSpecial3Aliases(): ActiveQuery
    {
        return $this->hasMany(Special3Alias::class, ['special_id' => 'id']);
    }
}
