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
 * This is the model class for table "special3_alias".
 *
 * @property integer $id
 * @property integer $special_id
 * @property string $key
 *
 * @property Special3 $special
 */
class Special3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'special3_alias';
    }

    public function rules()
    {
        return [
            [['special_id', 'key'], 'required'],
            [['special_id'], 'default', 'value' => null],
            [['special_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['special_id', 'key'], 'unique', 'targetAttribute' => ['special_id', 'key']],
            [['special_id'], 'exist', 'skipOnError' => true, 'targetClass' => Special3::class, 'targetAttribute' => ['special_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'special_id' => 'Special ID',
            'key' => 'Key',
        ];
    }

    public function getSpecial(): ActiveQuery
    {
        return $this->hasOne(Special3::class, ['id' => 'special_id']);
    }
}
