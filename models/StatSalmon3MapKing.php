<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_salmon3_map_king".
 *
 * @property integer $map_id
 * @property integer $big_map_id
 * @property integer $king_id
 * @property integer $jobs
 * @property integer $cleared
 *
 * @property BigrunMap3 $bigMap
 * @property SalmonKing3 $king
 * @property SalmonMap3 $map
 */
class StatSalmon3MapKing extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_salmon3_map_king';
    }

    #[Override]
    public function rules()
    {
        return [
            [['map_id', 'big_map_id'], 'default', 'value' => null],
            [['map_id', 'big_map_id', 'king_id', 'jobs', 'cleared'], 'default', 'value' => null],
            [['map_id', 'big_map_id', 'king_id', 'jobs', 'cleared'], 'integer'],
            [['king_id', 'jobs', 'cleared'], 'required'],
            [['map_id', 'big_map_id', 'king_id'], 'unique', 'targetAttribute' => ['map_id', 'big_map_id', 'king_id']],
            [['big_map_id'], 'exist', 'skipOnError' => true, 'targetClass' => BigrunMap3::class, 'targetAttribute' => ['big_map_id' => 'id']],
            [['king_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonKing3::class, 'targetAttribute' => ['king_id' => 'id']],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonMap3::class, 'targetAttribute' => ['map_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'map_id' => 'Map ID',
            'big_map_id' => 'Big Map ID',
            'king_id' => 'King ID',
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
}
