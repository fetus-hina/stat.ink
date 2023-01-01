<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function hash;
use function hash_hmac;
use function hex2bin;
use function is_infinite;
use function is_nan;
use function preg_match;
use function substr;
use function trim;

use const INF;
use const NAN;
use const SORT_DESC;

/**
 * This is the model class for table "battle_player2".
 *
 * @property integer $id
 * @property integer $battle_id
 * @property boolean $is_my_team
 * @property boolean $is_me
 * @property integer $weapon_id
 * @property integer $level
 * @property integer $rank_in_team
 * @property integer $kill
 * @property integer $death
 * @property integer $point
 * @property integer $my_kill
 * @property integer $kill_or_assist
 * @property integer $special
 * @property integer $rank_id
 * @property string $name
 * @property integer $gender_id
 * @property integer $fest_title_id
 * @property string $splatnet_id
 * @property integer $star_rank
 * @property boolean $top_500
 * @property integer $species_id
 *
 * @property Battle2 $battle
 * @property FestTitle $festTitle
 * @property Gender $gender
 * @property Rank2 $rank
 * @property Species2 $species
 * @property Weapon2 $weapon
 *
 * @property-read ForceBlackout2 $forceBlackout
 * @property-read bool $isForceBlackouted
 * @property-read string $iconUrl
 */
class BattlePlayer2 extends ActiveRecord
{
    private $user = false;

    public function behaviors()
    {
        return [
            // [
            //     'class' => TrimAttributesBehavior::class,
            //     'targets' => array_keys($this->attributes),
            // ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_player2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['battle_id', 'is_my_team', 'is_me'], 'required'],
            [['battle_id', 'weapon_id', 'rank_id', 'species_id', 'gender_id', 'fest_title_id', 'star_rank'], 'integer'],
            [['level'], 'integer', 'min' => 1, 'max' => 99],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['kill', 'death', 'my_kill'], 'integer', 'min' => 0],
            [['kill_or_assist', 'special'], 'integer', 'min' => 0],
            [['point'], 'integer', 'min' => 0],
            [['is_my_team', 'is_me'], 'boolean'],
            [['name'], 'string', 'max' => 10],
            [['splatnet_id'], 'string', 'max' => 16],
            [['top_500'], 'boolean'],
            [['battle_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Battle2::class,
                'targetAttribute' => ['battle_id' => 'id'],
            ],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
            [['rank_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rank2::class,
                'targetAttribute' => ['rank_id' => 'id'],
            ],
            [['species_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Species2::class,
                'targetAttribute' => ['species_id' => 'id'],
            ],
            [['gender_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Gender::class,
                'targetAttribute' => ['gender_id' => 'id'],
            ],
            [['fest_title_id'], 'exist', 'skipOnError' => true,
                'targetClass' => FestTitle::class,
                'targetAttribute' => ['fest_title_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'battle_id' => 'Battle ID',
            'is_my_team' => 'Is My Team',
            'is_me' => 'Is Me',
            'weapon_id' => 'Weapon ID',
            'level' => 'Level',
            'star_rank' => 'Star Rank',
            'rank_id' => 'Rank ID',
            'rank_in_team' => 'Rank In Team',
            'kill' => 'Kill',
            'death' => 'Death',
            'kill_or_assist' => 'Kill or Assist',
            'special' => 'Special',
            'point' => 'Point',
            'my_kill' => 'My Kill',
            'name' => 'Name',
            'gender_id' => 'Gender',
            'species_id' => 'Species',
            'fest_title_id' => 'Fest Title',
            'splatnet_id' => 'SplatNet ID',
            'top_500' => 'Is Top 500',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBattle()
    {
        return $this->hasOne(Battle2::class, ['id' => 'battle_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRank()
    {
        return $this->hasOne(Rank2::class, ['id' => 'rank_id']);
    }

    public function getGender()
    {
        return $this->hasOne(Gender::class, ['id' => 'gender_id']);
    }

    public function getSpecies()
    {
        return $this->hasOne(Species2::class, ['id' => 'species_id']);
    }

    public function getFestTitle()
    {
        return $this->hasOne(FestTitle::class, ['id' => 'fest_title_id']);
    }

    public function getForceBlackout()
    {
        return $this->hasOne(ForceBlackout2::class, ['splatnet_id' => 'splatnet_id']);
    }

    public function getIsForceBlackouted(): bool
    {
        return $this->forceBlackout !== null;
    }

    public function getUser(): ?User
    {
        if ($this->user === false) {
            $id = trim((string)$this->splatnet_id);
            if ($id === '') {
                $this->user = null;
            } else {
                $model = Splatnet2UserMap::find()
                    ->with('user')
                    ->andWhere(['splatnet_id' => $id])
                    ->orderBy(['battles' => SORT_DESC])
                    ->limit(1)
                    ->one();
                $this->user = $model->user ?? null;
            }
        }
        return $this->user;
    }

    public function getKillRatio(): ?float
    {
        if ($this->kill === null || $this->death === null) {
            return null;
        }
        if ($this->death == 0) {
            if ($this->kill == 0) {
                return NAN;
            }
            return INF;
        }
        return $this->kill / $this->death;
    }

    public function getFormattedKillRatio(): ?string
    {
        $ratio = $this->getKillRatio();
        if ($ratio === null) {
            return null;
        }
        if (is_nan($ratio)) {
            return Yii::t('app', 'N/A');
        }
        $fmt = Yii::$app->formatter;
        if (is_infinite($ratio)) {
            return $fmt->asDecimal(99.99, 2);
        }
        return $fmt->asDecimal($ratio, 2);
    }

    public function getKillRate(): ?float
    {
        if ($this->kill === null || $this->death === null) {
            return null;
        }
        if ($this->kill == 0 && $this->death == 0) {
            return NAN;
        }
        return $this->kill * 100 / ($this->kill + $this->death);
    }

    public function getFormattedKillRate(): ?string
    {
        $rate = $this->getKillRate();
        if ($rate === null) {
            return null;
        }
        if (is_nan($rate)) {
            return Yii::t('app', 'N/A');
        }
        $fmt = Yii::$app->formatter;
        return $fmt->asPercent($rate / 100, 2);
    }

    public function getJdenticonHash(): string
    {
        $id = $this->getAnonymizeSeed();
        if (preg_match('/^([0-9a-f]{2}+)[0-9a-f]?$/', $id, $match)) {
            $id = hex2bin($match[1]);
        }
        return substr(
            hash('sha256', $id, false),
            0,
            40,
        );
    }

    public function getIconUrl(string $ext = 'svg'): string
    {
        if ($user = $this->getUser()) {
            return $user->getIconUrl($ext);
        }
        $hash = $this->getJdenticonHash();
        return Yii::getAlias('@jdenticon') . '/' . $hash . '.' . $ext;
    }

    public function getIsDisconnected(): bool
    {
        if ((string)$this->point === '') {
            return false;
        }
        return $this->point == 0;
    }

    public function getAnonymizeSeed(): string
    {
        $value = trim($this->splatnet_id);
        return $value !== ''
            ? $value
            : hash_hmac('sha256', $this->id, $this->battle_id);
    }

    public function toJsonArray(?Battle2 $battle = null)
    {
        if (!$battle && $this->fest_title_id) {
            $battle = $this->battle;
        }
        return [
            'team' => $this->is_my_team ? 'my' : 'his',
            'is_me' => !!$this->is_me,
            'weapon' => $this->weapon_id ? $this->weapon->toJsonArray() : null,
            'level' => (string)$this->level === '' ? null : (int)$this->level,
            'rank' => $this->rank_id ? $this->rank->toJsonArray() : null,
            'star_rank' => (string)$this->star_rank === '' ? null : (int)$this->star_rank,
            'rank_in_team' => (string)$this->rank_in_team === '' ? null : (int)$this->rank_in_team,
            'kill' => (string)$this->kill === '' ? null : (int)$this->kill,
            'death' => (string)$this->death === '' ? null : (int)$this->death,
            'kill_or_assist' => (string)$this->kill_or_assist === '' ? null : (int)$this->kill_or_assist,
            'special' => (string)$this->special === '' ? null : (int)$this->special,
            'my_kill' => (string)$this->my_kill === '' ? null : (int)$this->my_kill,
            'point' => (string)$this->point === '' ? null : (int)$this->point,
            'name' => (string)$this->name === '' ? null : $this->name,
            'species' => $this->species_id ? $this->species->toJsonArray() : null,
            'gender' => $this->gender_id ? $this->gender->toJsonArray() : null,
            'fest_title' => $this->fest_title_id
                ? $this->festTitle->toJsonArray(
                    $this->gender,
                    $battle && $battle->my_team_fest_theme_id && $battle->his_team_fest_theme_id
                        ? ($this->is_my_team ? $battle->myTeamFestTheme->name : $battle->hisTeamFestTheme->name)
                        : null,
                )
                : null,
            'splatnet_id' => (string)$this->splatnet_id === '' ? null : $this->splatnet_id,
            'top_500' => $this->top_500,
            'icon' => $this->iconUrl,
        ];
    }
}
