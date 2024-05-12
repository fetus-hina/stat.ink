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
 * This is the model class for table "splatfest3_theme".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Battle3[] $battle3s
 * @property Battle3[] $battle3s0
 * @property Battle3[] $battle3s1
 */
class Splatfest3Theme extends ActiveRecord
{
    public static function tableName()
    {
        return 'splatfest3_theme';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 64],
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

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['our_team_theme_id' => 'id']);
    }

    public function getBattle3s0(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['their_team_theme_id' => 'id']);
    }

    public function getBattle3s1(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['third_team_theme_id' => 'id']);
    }
}
