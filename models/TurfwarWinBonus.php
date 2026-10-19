<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

use app\models\query\TurfwarWinBonusQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "turfwar_win_bonus".
 *
 * @property integer $id
 * @property integer $bonus
 * @property string $start_at
 */
class TurfwarWinBonus extends ActiveRecord
{
    public static function find(): TurfwarWinBonusQuery
    {
        return new TurfwarWinBonusQuery(static::class);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'turfwar_win_bonus';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bonus', 'start_at'], 'required'],
            [['bonus'], 'integer'],
            [['start_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bonus' => 'Bonus',
            'start_at' => 'Start At',
        ];
    }
}
