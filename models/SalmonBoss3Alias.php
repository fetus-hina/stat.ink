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
 * This is the model class for table "salmon_boss3_alias".
 *
 * @property integer $id
 * @property string $key
 * @property integer $salmonid_id
 *
 * @property SalmonBoss3 $salmonid
 */
class SalmonBoss3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_boss3_alias';
    }

    public function rules()
    {
        return [
            [['key', 'salmonid_id'], 'required'],
            [['salmonid_id'], 'default', 'value' => null],
            [['salmonid_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['salmonid_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonBoss3::class, 'targetAttribute' => ['salmonid_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'salmonid_id' => 'Salmonid ID',
        ];
    }

    public function getSalmonid(): ActiveQuery
    {
        return $this->hasOne(SalmonBoss3::class, ['id' => 'salmonid_id']);
    }
}
