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
 * This is the model class for table "tricolor_role3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Battle3[] $battle3s
 * @property Battle3[] $battle3s0
 * @property Battle3[] $battle3s1
 * @property UserBadge3Tricolor[] $userBadge3Tricolors
 * @property User[] $users
 */
class TricolorRole3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'tricolor_role3';
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

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['our_team_role_id' => 'id']);
    }

    public function getBattle3s0(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['their_team_role_id' => 'id']);
    }

    public function getBattle3s1(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['third_team_role_id' => 'id']);
    }

    public function getUserBadge3Tricolors(): ActiveQuery
    {
        return $this->hasMany(UserBadge3Tricolor::class, ['role_id' => 'id']);
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_badge3_tricolor', ['role_id' => 'id']);
    }
}
