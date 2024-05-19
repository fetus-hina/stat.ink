<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "crown3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property BattlePlayer3[] $battlePlayer3s
 * @property BattleTricolorPlayer3[] $battleTricolorPlayer3s
 */
class Crown3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'crown3';
    }

    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    public function getBattlePlayer3s(): ActiveQuery
    {
        return $this->hasMany(BattlePlayer3::class, ['crown_id' => 'id']);
    }

    public function getBattleTricolorPlayer3s(): ActiveQuery
    {
        return $this->hasMany(BattleTricolorPlayer3::class, ['crown_id' => 'id']);
    }
}
