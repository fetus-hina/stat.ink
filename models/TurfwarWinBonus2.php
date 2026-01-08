<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "turfwar_win_bonus2".
 *
 * @property integer $id
 * @property integer $bonus
 * @property string $start_at
 */
class TurfwarWinBonus2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'turfwar_win_bonus2';
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
