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
 * This is the model class for table "user_badge3_rule".
 *
 * @property integer $user_id
 * @property integer $rule_id
 * @property integer $count
 *
 * @property Rule3 $rule
 * @property User $user
 */
class UserBadge3Rule extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_badge3_rule';
    }

    public function rules()
    {
        return [
            [['user_id', 'rule_id', 'count'], 'required'],
            [['user_id', 'rule_id', 'count'], 'default', 'value' => null],
            [['user_id', 'rule_id', 'count'], 'integer'],
            [['user_id', 'rule_id'], 'unique', 'targetAttribute' => ['user_id', 'rule_id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'rule_id' => 'Rule ID',
            'count' => 'Count',
        ];
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule3::class, ['id' => 'rule_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
