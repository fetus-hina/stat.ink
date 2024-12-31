<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "battle_medal3".
 *
 * @property integer $id
 * @property integer $battle_id
 * @property integer $medal_id
 *
 * @property Battle3 $battle
 * @property Medal3 $medal
 */
class BattleMedal3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'battle_medal3';
    }

    public function rules()
    {
        return [
            [['battle_id', 'medal_id'], 'required'],
            [['battle_id', 'medal_id'], 'default', 'value' => null],
            [['battle_id', 'medal_id'], 'integer'],
            [['battle_id', 'medal_id'], 'unique', 'targetAttribute' => ['battle_id', 'medal_id']],
            [['battle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Battle3::class, 'targetAttribute' => ['battle_id' => 'id']],
            [['medal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Medal3::class, 'targetAttribute' => ['medal_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'battle_id' => 'Battle ID',
            'medal_id' => 'Medal ID',
        ];
    }

    public function getBattle(): ActiveQuery
    {
        return $this->hasOne(Battle3::class, ['id' => 'battle_id']);
    }

    public function getMedal(): ActiveQuery
    {
        return $this->hasOne(Medal3::class, ['id' => 'medal_id']);
    }
}
