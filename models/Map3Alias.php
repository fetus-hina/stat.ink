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
 * This is the model class for table "map3_alias".
 *
 * @property integer $id
 * @property integer $map_id
 * @property string $key
 *
 * @property Map3 $map
 */
class Map3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'map3_alias';
    }

    public function rules()
    {
        return [
            [['map_id', 'key'], 'required'],
            [['map_id'], 'default', 'value' => null],
            [['map_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['map_id', 'key'], 'unique', 'targetAttribute' => ['map_id', 'key']],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => Map3::class, 'targetAttribute' => ['map_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'map_id' => 'Map ID',
            'key' => 'Key',
        ];
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(Map3::class, ['id' => 'map_id']);
    }
}
