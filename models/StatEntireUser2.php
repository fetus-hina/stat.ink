<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_entire_user2".
 *
 * @property string $date
 * @property int $battle_count
 * @property int $user_count
 */
class StatEntireUser2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_entire_user2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date', 'battle_count', 'user_count'], 'required'],
            [['date'], 'safe'],
            [['battle_count', 'user_count'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'date' => 'Date',
            'battle_count' => 'Battle Count',
            'user_count' => 'User Count',
        ];
    }
}
