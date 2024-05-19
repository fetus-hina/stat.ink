<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_entire_salmon3".
 *
 * @property string $date
 * @property integer $jobs
 * @property integer $users
 */
class StatEntireSalmon3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_entire_salmon3';
    }

    public function rules()
    {
        return [
            [['date', 'jobs', 'users'], 'required'],
            [['date'], 'safe'],
            [['jobs', 'users'], 'default', 'value' => null],
            [['jobs', 'users'], 'integer'],
            [['date'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'date' => 'Date',
            'jobs' => 'Jobs',
            'users' => 'Users',
        ];
    }
}
