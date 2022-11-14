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
 * This is the model class for table "salmon_boss3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property SalmonBoss3Alias[] $salmonBoss3Aliases
 */
class SalmonBoss3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_boss3';
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

    public function getSalmonBoss3Aliases(): ActiveQuery
    {
        return $this->hasMany(SalmonBoss3Alias::class, ['salmonid_id' => 'id']);
    }
}
