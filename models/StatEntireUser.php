<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

/**
 * This is the model class for table "stat_entire_user".
 *
 * @property string $date
 * @property integer $battle_count
 * @property integer $user_count
 */
class StatEntireUser extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_entire_user';
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
