<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_player_boss_kill2".
 *
 * @property integer $player_id
 * @property integer $boss_id
 * @property integer $count
 *
 * @property SalmonBoss2 $boss
 * @property SalmonPlayer2 $player
 */
class SalmonPlayerBossKill2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_player_boss_kill2';
    }

    public function rules()
    {
        return [
            [['player_id', 'boss_id', 'count'], 'required'],
            [['player_id', 'boss_id', 'count'], 'default', 'value' => null],
            [['player_id', 'boss_id', 'count'], 'integer'],
            [['player_id', 'boss_id'], 'unique', 'targetAttribute' => ['player_id', 'boss_id']],
            [['boss_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonBoss2::class,
                'targetAttribute' => ['boss_id' => 'id'],
            ],
            [['player_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonPlayer2::class,
                'targetAttribute' => ['player_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'player_id' => 'Player ID',
            'boss_id' => 'Boss ID',
            'count' => 'Count',
        ];
    }

    public function getBoss(): ActiveQuery
    {
        return $this->hasOne(SalmonBoss2::class, ['id' => 'boss_id']);
    }

    public function getPlayer(): ActiveQuery
    {
        return $this->hasOne(SalmonPlayer2::class, ['id' => 'player_id']);
    }

    public function toJsonArray(): array
    {
        return [
            'boss' => $this->boss->toJsonArray(),
            'count' => (int)$this->count,
        ];
    }
}
