<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splashtag_title3".
 *
 * @property integer $id
 * @property string $name
 *
 * @property BattlePlayer3[] $battlePlayer3s
 * @property BattleTricolorPlayer3[] $battleTricolorPlayer3s
 * @property SalmonPlayer3[] $salmonPlayer3s
 */
class SplashtagTitle3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'splashtag_title3';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public function getBattlePlayer3s(): ActiveQuery
    {
        return $this->hasMany(BattlePlayer3::class, ['splashtag_title_id' => 'id']);
    }

    public function getBattleTricolorPlayer3s(): ActiveQuery
    {
        return $this->hasMany(BattleTricolorPlayer3::class, ['splashtag_title_id' => 'id']);
    }

    public function getSalmonPlayer3s(): ActiveQuery
    {
        return $this->hasMany(SalmonPlayer3::class, ['splashtag_title_id' => 'id']);
    }
}
