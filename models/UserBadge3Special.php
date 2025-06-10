<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_badge3_special".
 *
 * @property integer $user_id
 * @property integer $special_id
 * @property integer $count
 *
 * @property Special3 $special
 * @property User $user
 */
class UserBadge3Special extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_badge3_special';
    }

    #[Override]
    public function rules()
    {
        return [
            [['user_id', 'special_id', 'count'], 'required'],
            [['user_id', 'special_id', 'count'], 'default', 'value' => null],
            [['user_id', 'special_id', 'count'], 'integer'],
            [['user_id', 'special_id'], 'unique', 'targetAttribute' => ['user_id', 'special_id']],
            [['special_id'], 'exist', 'skipOnError' => true, 'targetClass' => Special3::class, 'targetAttribute' => ['special_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'special_id' => 'Special ID',
            'count' => 'Count',
        ];
    }

    public function getSpecial(): ActiveQuery
    {
        return $this->hasOne(Special3::class, ['id' => 'special_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
