<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bigrun_map3_alias".
 *
 * @property integer $id
 * @property integer $map_id
 * @property string $key
 *
 * @property BigrunMap3 $map
 */
class BigrunMap3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'bigrun_map3_alias';
    }

    #[Override]
    public function rules()
    {
        return [
            [['map_id', 'key'], 'required'],
            [['map_id'], 'default', 'value' => null],
            [['map_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => BigrunMap3::class, 'targetAttribute' => ['map_id' => 'id']],
        ];
    }

    #[Override]
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
        return $this->hasOne(BigrunMap3::class, ['id' => 'map_id']);
    }
}
