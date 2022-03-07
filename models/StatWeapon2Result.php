<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use app\models\query\StatWeapon2ResultQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_weapon2_result".
 *
 * @property int $weapon_id
 * @property int $rule_id
 * @property int $map_id
 * @property int $lobby_id
 * @property int $mode_id
 * @property int $rank_id
 * @property int $version_id
 * @property int $kill
 * @property int $death
 * @property int $assist
 * @property int $special
 * @property int $points
 * @property int $battles
 * @property int $wins
 *
 * @property Lobby2 $lobby
 * @property Map2 $map
 * @property Mode2 $mode
 * @property Rank2 $rank
 * @property Rule2 $rule
 * @property SplatoonVersion2 $version
 * @property Weapon2 $weapon
 */
class StatWeapon2Result extends ActiveRecord
{
    public static function find(): StatWeapon2ResultQuery
    {
        return new StatWeapon2ResultQuery(static::class);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_weapon2_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['weapon_id', 'rule_id', 'map_id', 'lobby_id', 'mode_id', 'rank_id'], 'required'],
            [['version_id', 'kill', 'death', 'assist', 'special', 'points', 'battles', 'wins'], 'required'],
            [['weapon_id', 'rule_id', 'map_id', 'lobby_id', 'mode_id', 'rank_id'], 'default',
                'value' => null,
            ],
            [['version_id', 'kill', 'death', 'assist', 'special', 'points', 'battles', 'wins'], 'default',
                'value' => null,
            ],
            [['weapon_id', 'rule_id', 'map_id', 'lobby_id', 'mode_id', 'rank_id'], 'integer'],
            [['version_id', 'kill', 'death', 'assist', 'special', 'points', 'battles', 'wins'], 'integer'],
            [['lobby_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Lobby2::class,
                'targetAttribute' => ['lobby_id' => 'id'],
            ],
            [['map_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Map2::class,
                'targetAttribute' => ['map_id' => 'id'],
            ],
            [['mode_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Mode2::class,
                'targetAttribute' => ['mode_id' => 'id'],
            ],
            [['rank_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rank2::class,
                'targetAttribute' => ['rank_id' => 'id'],
            ],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['version_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SplatoonVersion2::class,
                'targetAttribute' => ['version_id' => 'id'],
            ],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'weapon_id' => 'Weapon ID',
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
            'lobby_id' => 'Lobby ID',
            'mode_id' => 'Mode ID',
            'rank_id' => 'Rank ID',
            'version_id' => 'Version ID',
            'kill' => 'Kill',
            'death' => 'Death',
            'assist' => 'Assist',
            'special' => 'Special',
            'points' => 'Points',
            'battles' => 'Battles',
            'wins' => 'Wins',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLobby()
    {
        return $this->hasOne(Lobby2::class, ['id' => 'lobby_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map2::class, ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMode()
    {
        return $this->hasOne(Mode2::class, ['id' => 'mode_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRank()
    {
        return $this->hasOne(Rank2::class, ['id' => 'rank_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVersion()
    {
        return $this->hasOne(SplatoonVersion2::class, ['id' => 'version_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }
}
