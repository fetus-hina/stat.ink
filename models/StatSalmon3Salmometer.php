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
 * This is the model class for table "stat_salmon3_salmometer".
 *
 * @property integer $king_smell
 * @property integer $jobs
 * @property integer $cleared
 */
class StatSalmon3Salmometer extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_salmon3_salmometer';
    }

    #[Override]
    public function rules()
    {
        return [
            [['king_smell', 'jobs', 'cleared'], 'required'],
            [['king_smell', 'jobs', 'cleared'], 'default', 'value' => null],
            [['king_smell', 'jobs', 'cleared'], 'integer'],
            [['king_smell'], 'unique'],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'king_smell' => 'King Smell',
            'jobs' => 'Jobs',
            'cleared' => 'Cleared',
        ];
    }
}
