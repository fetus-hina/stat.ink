<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatoon_version_group3".
 *
 * @property integer $id
 * @property string $tag
 * @property string $name
 *
 * @property SplatoonVersion3[] $splatoonVersion3s
 */
class SplatoonVersionGroup3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'splatoon_version_group3';
    }

    public function rules()
    {
        return [
            [['tag', 'name'], 'required'],
            [['tag'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag' => 'Tag',
            'name' => 'Name',
        ];
    }

    public function getSplatoonVersion3s(): ActiveQuery
    {
        return $this->hasMany(SplatoonVersion3::class, ['group_id' => 'id']);
    }
}
