<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rule_group3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $rank
 *
 * @property Rule3[] $rule3s
 */
class RuleGroup3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'rule_group3';
    }

    public function rules()
    {
        return [
            [['key', 'name', 'rank'], 'required'],
            [['rank'], 'default', 'value' => null],
            [['rank'], 'integer'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['rank'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'rank' => 'Rank',
        ];
    }

    public function getRule3s(): ActiveQuery
    {
        return $this->hasMany(Rule3::class, ['group_id' => 'id']);
    }
}
