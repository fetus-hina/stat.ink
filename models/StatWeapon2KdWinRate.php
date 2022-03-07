<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use app\models\query\StatWeapon2KdWinRateQuery;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_weapon2_kd_win_rate".
 *
 * @property int $rule_id
 * @property int $weapon_type_id
 * @property int $map_id
 * @property int $version_group_id
 * @property int $rank_group_id
 * @property int $kill
 * @property int $death
 * @property int $battles
 * @property int $wins
 *
 * @property Map2 $map
 * @property RankGroup2 $rankGroup
 * @property Rule2 $rule
 * @property SplatoonVersion2 $versionGroup
 * @property Weapon2 $weapon
 */
class StatWeapon2KdWinRate extends ActiveRecord
{
    public static function find(): StatWeapon2KdWinRateQuery
    {
        return new StatWeapon2KdWinRateQuery(static::class);
    }

    public static function tableName()
    {
        return 'stat_weapon2_kd_win_rate';
    }

    public function rules()
    {
        $pKeyGroup = [
            'rule_id',
            'weapon_type_id',
            'map_id',
            'version_group_id',
            'rank_group_id',
            'kill',
            'death',
        ];
        return [
            [array_merge($pKeyGroup, ['battles', 'wins']), 'required'],
            [array_merge($pKeyGroup, ['battles', 'wins']), 'default', 'value' => null],
            [array_merge($pKeyGroup, ['battles', 'wins']), 'integer'],
            [$pKeyGroup, 'unique', 'targetAttribute' => $pKeyGroup],
            [['map_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Map2::class,
                'targetAttribute' => ['map_id' => 'id'],
            ],
            [['rank_group_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => RankGroup2::class,
                'targetAttribute' => ['rank_group_id' => 'id'],
            ],
            [['rule_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['version_group_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => SplatoonVersion2::class,
                'targetAttribute' => ['version_group_id' => 'id'],
            ],
            [['weapon_type_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => WeaponType2::class,
                'targetAttribute' => ['weapon_type_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'rule_id' => 'Rule ID',
            'weapon_type_id' => 'Weapon Type ID',
            'map_id' => 'Map ID',
            'version_group_id' => 'Version Group ID',
            'rank_group_id' => 'Rank Group ID',
            'kill' => 'Kill',
            'death' => 'Death',
            'battles' => 'Battles',
            'wins' => 'Wins',
        ];
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(Map2::class, ['id' => 'map_id']);
    }

    public function getRankGroup(): ActiveQuery
    {
        return $this->hasOne(RankGroup2::class, ['id' => 'rank_group_id']);
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    public function getVersionGroup(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersion2::class, ['id' => 'version_group_id']);
    }

    public function getWeaponType(): ActiveQuery
    {
        return $this->hasOne(WeaponType2::class, ['id' => 'weapon_type_id']);
    }
}
