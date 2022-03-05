<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "login_method".
 *
 * @property int $id
 * @property string $name
 *
 * @property UserLoginHistory[] $userLoginHistories
 */
class LoginMethod extends ActiveRecord
{
    public const METHOD_PASSWORD = 1;
    public const METHOD_COOKIE = 2;
    public const METHOD_TWITTER = 3;

    public static function tableName()
    {
        return 'login_method';
    }

    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'default', 'value' => null],
            [['id'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['id'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public function getUserLoginHistories(): ActiveQuery
    {
        return $this->hasMany(UserLoginHistory::class, ['method_id' => 'id']);
    }
}
