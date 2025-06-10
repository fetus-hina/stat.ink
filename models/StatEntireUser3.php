<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_entire_user3".
 *
 * @property string $date
 * @property integer $battles
 * @property integer $users
 */
class StatEntireUser3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_entire_user3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['date', 'battles', 'users'], 'required'],
            [['date'], 'safe'],
            [['battles', 'users'], 'default', 'value' => null],
            [['battles', 'users'], 'integer'],
            [['date'], 'unique'],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'date' => 'Date',
            'battles' => 'Battles',
            'users' => 'Users',
        ];
    }
}
