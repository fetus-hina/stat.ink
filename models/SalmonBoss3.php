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
 * This is the model class for table "salmon_boss3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property boolean $has_badge
 * @property integer $rank
 *
 * @property Salmon3[] $salmon
 * @property SalmonBoss3Alias[] $salmonBoss3Aliases
 * @property SalmonBossAppearance3[] $salmonBossAppearance3s
 * @property UserBadge3BossSalmonid[] $userBadge3BossSalmons
 * @property User[] $users
 */
class SalmonBoss3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_boss3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['key', 'name', 'has_badge', 'rank'], 'required'],
            [['has_badge'], 'boolean'],
            [['rank'], 'default', 'value' => null],
            [['rank'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
            [['key'], 'unique'],
            [['rank'], 'unique'],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'has_badge' => 'Has Badge',
            'rank' => 'Rank',
        ];
    }

    public function getSalmon(): ActiveQuery
    {
        return $this->hasMany(Salmon3::class, ['id' => 'salmon_id'])->viaTable('salmon_boss_appearance3', ['boss_id' => 'id']);
    }

    public function getSalmonBoss3Aliases(): ActiveQuery
    {
        return $this->hasMany(SalmonBoss3Alias::class, ['salmonid_id' => 'id']);
    }

    public function getSalmonBossAppearance3s(): ActiveQuery
    {
        return $this->hasMany(SalmonBossAppearance3::class, ['boss_id' => 'id']);
    }

    public function getUserBadge3BossSalmons(): ActiveQuery
    {
        return $this->hasMany(UserBadge3BossSalmonid::class, ['boss_id' => 'id']);
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_badge3_boss_salmonid', ['boss_id' => 'id']);
    }
}
