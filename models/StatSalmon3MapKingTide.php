<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_salmon3_map_king_tide".
 *
 * @property integer $map_id
 * @property integer $big_map_id
 * @property integer $king_id
 * @property integer $tide_id
 * @property integer $jobs
 * @property integer $cleared
 *
 * @property BigrunMap3 $bigMap
 * @property SalmonKing3 $king
 * @property SalmonMap3 $map
 * @property SalmonWaterLevel2 $tide
 */
class StatSalmon3MapKingTide extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_salmon3_map_king_tide';
    }

    public function rules()
    {
        return [
            [['map_id', 'big_map_id', 'king_id', 'tide_id', 'jobs', 'cleared'], 'default', 'value' => null],
            [['map_id', 'big_map_id', 'king_id', 'tide_id', 'jobs', 'cleared'], 'integer'],
            [['king_id', 'tide_id', 'jobs', 'cleared'], 'required'],
            [['map_id', 'big_map_id', 'king_id', 'tide_id'], 'unique', 'targetAttribute' => ['map_id', 'big_map_id', 'king_id', 'tide_id']],
            [['big_map_id'], 'exist', 'skipOnError' => true, 'targetClass' => BigrunMap3::class, 'targetAttribute' => ['big_map_id' => 'id']],
            [['king_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonKing3::class, 'targetAttribute' => ['king_id' => 'id']],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonMap3::class, 'targetAttribute' => ['map_id' => 'id']],
            [['tide_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonWaterLevel2::class, 'targetAttribute' => ['tide_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'map_id' => 'Map ID',
            'big_map_id' => 'Big Map ID',
            'king_id' => 'King ID',
            'tide_id' => 'Tide ID',
            'jobs' => 'Jobs',
            'cleared' => 'Cleared',
        ];
    }

    public function getBigMap(): ActiveQuery
    {
        return $this->hasOne(BigrunMap3::class, ['id' => 'big_map_id']);
    }

    public function getKing(): ActiveQuery
    {
        return $this->hasOne(SalmonKing3::class, ['id' => 'king_id']);
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(SalmonMap3::class, ['id' => 'map_id']);
    }

    public function getTide(): ActiveQuery
    {
        return $this->hasOne(SalmonWaterLevel2::class, ['id' => 'tide_id']);
    }
}
