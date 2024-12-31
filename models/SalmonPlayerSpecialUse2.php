<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_player_special_use2".
 *
 * @property integer $player_id
 * @property integer $wave
 * @property integer $count
 *
 * @property SalmonPlayer2 $player
 */
class SalmonPlayerSpecialUse2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_player_special_use2';
    }

    public function rules()
    {
        return [
            [['player_id', 'wave', 'count'], 'required'],
            [['player_id', 'wave', 'count'], 'default', 'value' => null],
            [['player_id', 'wave', 'count'], 'integer'],
            [['player_id', 'wave'], 'unique', 'targetAttribute' => ['player_id', 'wave']],
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
            'wave' => 'Wave',
            'count' => 'Count',
        ];
    }

    public function getPlayer(): ActiveQuery
    {
        return $this->hasOne(SalmonPlayer2::class, ['id' => 'player_id']);
    }
}
