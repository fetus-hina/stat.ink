<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "result3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property boolean $is_win
 * @property boolean $aggregatable
 * @property string $label_color
 *
 * @property Battle3[] $battle3s
 */
class Result3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'result3';
    }

    public function rules()
    {
        return [
            [['id', 'key', 'name', 'is_win', 'aggregatable', 'label_color'], 'required'],
            [['id'], 'default', 'value' => null],
            [['id'], 'integer'],
            [['is_win', 'aggregatable'], 'boolean'],
            [['key', 'name', 'label_color'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['id'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'is_win' => 'Is Win',
            'aggregatable' => 'Aggregatable',
            'label_color' => 'Label Color',
        ];
    }

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['result_id' => 'id']);
    }
}
