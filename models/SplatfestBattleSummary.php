<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatfest_battle_summary".
 *
 * @property integer $fest_id
 * @property string $timestamp
 * @property integer $alpha_win
 * @property integer $alpha_lose
 * @property integer $bravo_win
 * @property integer $bravo_lose
 * @property string $summarized_at
 *
 * @property Splatfest $fest
 */
class SplatfestBattleSummary extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'splatfest_battle_summary';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fest_id', 'timestamp', 'alpha_win', 'alpha_lose', 'bravo_win', 'bravo_lose'], 'required'],
            [['summarized_at'], 'required'],
            [['fest_id', 'alpha_win', 'alpha_lose', 'bravo_win', 'bravo_lose'], 'integer'],
            [['timestamp', 'summarized_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fest_id' => 'Fest ID',
            'timestamp' => 'Timestamp',
            'alpha_win' => 'Alpha Win',
            'alpha_lose' => 'Alpha Lose',
            'bravo_win' => 'Bravo Win',
            'bravo_lose' => 'Bravo Lose',
            'summarized_at' => 'Summarized At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getFest()
    {
        return $this->hasOne(Splatfest::class, ['id' => 'fest_id']);
    }
}
