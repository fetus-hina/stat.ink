<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "x_matching_group3".
 *
 * @property integer $id
 * @property string $name
 * @property string $short_name
 * @property string $color
 * @property integer $rank
 *
 * @property XMatchingGroupWeapon3[] $xMatchingGroupWeapon3s
 */
class XMatchingGroup3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'x_matching_group3';
    }

    public function rules()
    {
        return [
            [['name', 'short_name', 'color', 'rank'], 'required'],
            [['rank'], 'default', 'value' => null],
            [['rank'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['short_name'], 'string', 'max' => 8],
            [['color'], 'string', 'max' => 6],
            [['name'], 'unique'],
            [['rank'], 'unique'],
            [['short_name'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'short_name' => 'Short Name',
            'color' => 'Color',
            'rank' => 'Rank',
        ];
    }

    public function getXMatchingGroupWeapon3s(): ActiveQuery
    {
        return $this->hasMany(XMatchingGroupWeapon3::class, ['group_id' => 'id']);
    }
}
