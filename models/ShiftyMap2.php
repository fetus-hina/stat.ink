<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shifty_map2".
 *
 * @property integer $id
 * @property string $period_range
 * @property string $range_hint
 * @property integer $map_id
 *
 * @property Map2 $map
 */
class ShiftyMap2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'shifty_map2';
    }

    public function rules()
    {
        return [
            [['period_range', 'range_hint', 'map_id'], 'required'],
            [['period_range', 'range_hint'], 'string'],
            [['map_id'], 'default', 'value' => null],
            [['map_id'], 'integer'],
            [['map_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Map2::class,
                'targetAttribute' => ['map_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'period_range' => 'Period Range',
            'range_hint' => 'Range Hint',
            'map_id' => 'Map ID',
        ];
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(Map2::class, ['id' => 'map_id']);
    }
}
