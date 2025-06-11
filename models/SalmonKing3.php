<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_king3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Salmon3[] $salmon3s
 * @property SalmonKing3Alias[] $salmonKing3Aliases
 * @property SalmonSchedule3[] $salmonSchedule3s
 * @property StatSalmon3MapKingTide[] $statSalmon3MapKingTides
 * @property StatSalmon3MapKing[] $statSalmon3MapKings
 * @property UserBadge3KingSalmonid[] $userBadge3KingSalmons
 * @property User[] $users
 */
class SalmonKing3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_king3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
            [['key'], 'unique'],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    public function getSalmon3s(): ActiveQuery
    {
        return $this->hasMany(Salmon3::class, ['king_salmonid_id' => 'id']);
    }

    public function getSalmonKing3Aliases(): ActiveQuery
    {
        return $this->hasMany(SalmonKing3Alias::class, ['salmonid_id' => 'id']);
    }

    public function getSalmonSchedule3s(): ActiveQuery
    {
        return $this->hasMany(SalmonSchedule3::class, ['king_id' => 'id']);
    }

    public function getStatSalmon3MapKingTides(): ActiveQuery
    {
        return $this->hasMany(StatSalmon3MapKingTide::class, ['king_id' => 'id']);
    }

    public function getStatSalmon3MapKings(): ActiveQuery
    {
        return $this->hasMany(StatSalmon3MapKing::class, ['king_id' => 'id']);
    }

    public function getUserBadge3KingSalmons(): ActiveQuery
    {
        return $this->hasMany(UserBadge3KingSalmonid::class, ['king_id' => 'id']);
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_badge3_king_salmonid', ['king_id' => 'id']);
    }
}
