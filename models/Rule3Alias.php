<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rule3_alias".
 *
 * @property integer $id
 * @property string $key
 * @property integer $rule_id
 *
 * @property Rule3 $rule
 */
class Rule3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'rule3_alias';
    }

    public function rules()
    {
        return [
            [['key', 'rule_id'], 'required'],
            [['rule_id'], 'default', 'value' => null],
            [['rule_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'rule_id' => 'Rule ID',
        ];
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule3::class, ['id' => 'rule_id']);
    }
}
