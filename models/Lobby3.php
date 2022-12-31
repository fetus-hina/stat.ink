<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "lobby3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $rank
 * @property integer $group_id
 *
 * @property Battle3[] $battle3s
 * @property LobbyGroup3 $group
 * @property Schedule3[] $schedule3s
 * @property UserStat3[] $userStat3s
 * @property User[] $users
 */
class Lobby3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'lobby3';
    }

    public function rules()
    {
        return [
            [['key', 'name', 'rank', 'group_id'], 'required'],
            [['rank', 'group_id'], 'default', 'value' => null],
            [['rank', 'group_id'], 'integer'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => LobbyGroup3::class, 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'rank' => 'Rank',
            'group_id' => 'Group ID',
        ];
    }

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['lobby_id' => 'id']);
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(LobbyGroup3::class, ['id' => 'group_id']);
    }

    public function getSchedule3s(): ActiveQuery
    {
        return $this->hasMany(Schedule3::class, ['lobby_id' => 'id']);
    }

    public function getUserStat3s(): ActiveQuery
    {
        return $this->hasMany(UserStat3::class, ['lobby_id' => 'id']);
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_stat3', ['lobby_id' => 'id']);
    }
}
