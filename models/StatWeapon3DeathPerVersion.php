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
 * This is the model class for table "stat_weapon3_death_per_version".
 *
 * @property integer $version_id
 * @property integer $lobby_id
 * @property integer $rule_id
 * @property integer $weapon_id
 * @property integer $death
 * @property integer $battles
 * @property integer $wins
 *
 * @property Lobby3 $lobby
 * @property Rule3 $rule
 * @property SplatoonVersion3 $version
 * @property Weapon3 $weapon
 */
class StatWeapon3DeathPerVersion extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_weapon3_death_per_version';
    }

    #[Override]
    public function rules()
    {
        return [
            [['version_id', 'lobby_id', 'rule_id', 'weapon_id', 'death', 'battles', 'wins'], 'required'],
            [['version_id', 'lobby_id', 'rule_id', 'weapon_id', 'death', 'battles', 'wins'], 'default', 'value' => null],
            [['version_id', 'lobby_id', 'rule_id', 'weapon_id', 'death', 'battles', 'wins'], 'integer'],
            [['version_id', 'lobby_id', 'rule_id', 'weapon_id', 'death'], 'unique', 'targetAttribute' => ['version_id', 'lobby_id', 'rule_id', 'weapon_id', 'death']],
            [['lobby_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lobby3::class, 'targetAttribute' => ['lobby_id' => 'id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['version_id'], 'exist', 'skipOnError' => true, 'targetClass' => SplatoonVersion3::class, 'targetAttribute' => ['version_id' => 'id']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'version_id' => 'Version ID',
            'lobby_id' => 'Lobby ID',
            'rule_id' => 'Rule ID',
            'weapon_id' => 'Weapon ID',
            'death' => 'Death',
            'battles' => 'Battles',
            'wins' => 'Wins',
        ];
    }

    public function getLobby(): ActiveQuery
    {
        return $this->hasOne(Lobby3::class, ['id' => 'lobby_id']);
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule3::class, ['id' => 'rule_id']);
    }

    public function getVersion(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersion3::class, ['id' => 'version_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon3::class, ['id' => 'weapon_id']);
    }
}
