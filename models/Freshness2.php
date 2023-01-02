<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use app\components\helpers\Translator;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "freshness2".
 *
 * @property integer $id
 * @property string $name
 * @property string $color
 * @property string $range
 */
class Freshness2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'freshness2';
    }

    public function rules()
    {
        return [
            [['name', 'color', 'range'], 'required'],
            [['range'], 'string'],
            [['name'], 'string', 'max' => 12],
            [['color'], 'string', 'max' => 8],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'color' => 'Color',
            'range' => 'Range',
        ];
    }

    public function toJsonArray(): array
    {
        return Translator::translateToAll('app-freshness2', $this->name);
    }
}
