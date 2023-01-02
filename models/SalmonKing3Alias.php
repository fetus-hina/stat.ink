<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_king3_alias".
 *
 * @property integer $id
 * @property string $key
 * @property integer $salmonid_id
 *
 * @property SalmonKing3 $salmonid
 */
class SalmonKing3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_king3_alias';
    }

    public function rules()
    {
        return [
            [['key', 'salmonid_id'], 'required'],
            [['salmonid_id'], 'default', 'value' => null],
            [['salmonid_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['salmonid_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonKing3::class, 'targetAttribute' => ['salmonid_id' => 'id']],
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
        return $this->hasOne(SalmonKing3::class, ['id' => 'salmonid_id']);
    }
}
