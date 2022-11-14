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
 * This is the model class for table "salmon_king3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property SalmonKing3Alias[] $salmonKing3Aliases
 */
class SalmonKing3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_king3';
    }

    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
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

    public function getSalmonKing3Aliases(): ActiveQuery
    {
        return $this->hasMany(SalmonKing3Alias::class, ['salmonid_id' => 'id']);
    }
}
