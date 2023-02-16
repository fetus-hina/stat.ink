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
 * This is the model class for table "ability3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $rank
 * @property boolean $primary_only
 * @property string $sendouink
 *
 * @property GearConfiguration3[] $gearConfiguration3s
 * @property GearConfigurationSecondary3[] $gearConfigurationSecondary3s
 */
class Ability3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'ability3';
    }

    public function rules()
    {
        return [
            [['key', 'name', 'rank', 'primary_only', 'sendouink'], 'required'],
            [['rank'], 'default', 'value' => null],
            [['rank'], 'integer'],
            [['primary_only'], 'boolean'],
            [['key', 'name'], 'string', 'max' => 32],
            [['sendouink'], 'string', 'max' => 3],
            [['key'], 'unique'],
            [['rank'], 'unique'],
            [['sendouink'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'rank' => 'Rank',
            'primary_only' => 'Primary Only',
            'sendouink' => 'Sendouink',
        ];
    }

    public function getGearConfiguration3s(): ActiveQuery
    {
        return $this->hasMany(GearConfiguration3::class, ['ability_id' => 'id']);
    }

    public function getGearConfigurationSecondary3s(): ActiveQuery
    {
        return $this->hasMany(GearConfigurationSecondary3::class, ['ability_id' => 'id']);
    }
}
