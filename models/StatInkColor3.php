<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_ink_color3".
 *
 * @property string $color1
 * @property string $color2
 * @property integer $battles
 * @property integer $wins
 */
class StatInkColor3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_ink_color3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['color1', 'color2', 'battles', 'wins'], 'required'],
            [['battles', 'wins'], 'default', 'value' => null],
            [['battles', 'wins'], 'integer'],
            [['color1', 'color2'], 'string', 'max' => 8],
            [['color1', 'color2'], 'unique', 'targetAttribute' => ['color1', 'color2']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'color1' => 'Color1',
            'color2' => 'Color2',
            'battles' => 'Battles',
            'wins' => 'Wins',
        ];
    }
}
