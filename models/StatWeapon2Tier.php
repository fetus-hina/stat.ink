<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "stat_weapon2_tier".
 *
 * @property integer $id
 * @property integer $version_group_id
 * @property string $month
 * @property integer $rule_id
 * @property integer $weapon_id
 * @property integer $players_count
 * @property integer $win_count
 * @property double $win_percent
 * @property double $avg_kill
 * @property double $med_kill
 * @property double $stderr_kill
 * @property double $stddev_kill
 * @property double $avg_death
 * @property double $med_death
 * @property double $stderr_death
 * @property double $stddev_death
 * @property string $updated_at
 *
 * @property Rule2 $rule
 * @property SplatoonVersionGroup2 $versionGroup
 * @property Weapon2 $weapon
 */
class StatWeapon2Tier extends ActiveRecord
{
    public const PLAYERS_COUNT_THRESHOLD = 50;

    public static function find(): ActiveQuery
    {
        return new class (static::class) extends ActiveQuery {
            public function thresholded(): self
            {
                $this->andWhere(
                    ['>=',
                        '{{stat_weapon2_tier}}.[[players_count]]',
                        StatWeapon2Tier::PLAYERS_COUNT_THRESHOLD,
                    ],
                );
                return $this;
            }
        };
    }

    public static function tableName()
    {
        return 'stat_weapon2_tier';
    }

    public function rules()
    {
        return [
            [
                [
                    'version_group_id',
                    'month',
                    'rule_id',
                    'weapon_id',
                    'players_count',
                    'win_count',
                    'win_percent',
                    'avg_kill',
                    'med_kill',
                    'stderr_kill',
                    'stddev_kill',
                    'avg_death',
                    'med_death',
                    'stderr_death',
                    'stddev_death',
                    'updated_at',
                ],
                'required',
            ],
            [['version_group_id', 'rule_id', 'weapon_id', 'players_count', 'win_count'], 'default',
                'value' => null,
            ],
            [['version_group_id', 'rule_id', 'weapon_id', 'players_count', 'win_count'], 'integer'],
            [['month', 'updated_at'], 'safe'],
            [
                [
                    'win_percent',
                    'avg_kill',
                    'med_kill',
                    'stderr_kill',
                    'stddev_kill',
                    'avg_death',
                    'med_death',
                    'stderr_death',
                    'stddev_death',
                ],
                'number',
            ],
            [['version_group_id', 'month', 'rule_id', 'weapon_id'], 'unique',
                'targetAttribute' => ['version_group_id', 'month', 'rule_id', 'weapon_id'],
            ],
            [['rule_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['version_group_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => SplatoonVersionGroup2::class,
                'targetAttribute' => ['version_group_id' => 'id'],
            ],
            [['weapon_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version_group_id' => 'Version Group ID',
            'month' => 'Month',
            'rule_id' => 'Rule ID',
            'weapon_id' => 'Weapon ID',
            'players_count' => 'Players Count',
            'win_count' => 'Win Count',
            'win_percent' => 'Win Percent',
            'avg_kill' => 'Avg Kill',
            'med_kill' => 'Med Kill',
            'stderr_kill' => 'Stderr Kill',
            'stddev_kill' => 'Stddev Kill',
            'avg_death' => 'Avg Death',
            'med_death' => 'Med Death',
            'stderr_death' => 'Stderr Death',
            'stddev_death' => 'Stddev Death',
            'updated_at' => 'Updated At',
        ];
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    public function getVersionGroup(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersionGroup2::class, ['id' => 'version_group_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }

    public function getWinRates(): ?array
    {
        if ($this->players_count < 1) {
            return null;
        }
        $rate = (int)$this->win_count / (int)$this->players_count;
        $errorPctPt = $this->getErrorPoint();
        return [
            $errorPctPt === null ? null : max(0, $rate - $errorPctPt / 100),
            $rate,
            $errorPctPt === null ? null : min(1, $rate + $errorPctPt / 100),
        ];
    }

    public function getErrorPoint(): ?float
    {
        $stdError = $this->calcError();
        return ($stdError === null)
            ? null
            : $stdError * 100 * 2;
    }

    public function calcError(): ?float
    {
        $battles = (int)$this->players_count;
        $wins = (int)$this->win_count;

        if ($battles < 1 || $wins < 0) {
            return null;
        }

        // ref. http://lfics81.techblog.jp/archives/2982884.html
        $winRate = $wins / $battles;
        $s = sqrt(($battles / ($battles - 1.5)) * $winRate * (1.0 - $winRate));
        return $s / sqrt($battles);
    }

    public static function getDateVersionPatterns(Rule2 $rule): array
    {
        $list = (new Query())
            ->select([
                'vtag' => 'MAX({{v}}.[[tag]])',
                'vname' => 'MAX({{v}}.[[name]])',
                'month' => '{{t}}.[[month]]',
            ])
            ->from(['t' => static::tableName()])
            ->innerJoin(
                ['v' => SplatoonVersionGroup2::tableName()],
                '{{t}}.[[version_group_id]] = {{v}}.[[id]]',
            )
            ->andWhere(['{{t}}.[[rule_id]]' => $rule->id])
            ->groupBy([
                't.version_group_id',
                't.month',
            ])
            ->andHaving(['>=', 'MAX({{t}}.[[players_count]])', static::PLAYERS_COUNT_THRESHOLD])
            ->all();
        usort($list, fn (array $a, array $b): int => version_compare($b['vtag'], $a['vtag'])
                ?: strcmp($b['month'], $a['month']));
        return ArrayHelper::map(
            $list,
            fn (array $row): string => vsprintf('v%s@%s', [
                    $row['vtag'],
                    substr($row['month'], 0, 7),
                ]),
            fn (array $row): array => [
                    'month' => substr($row['month'], 0, 7),
                    'vTag' => $row['vtag'],
                    'vName' => $row['vname'],
                ],
        );
    }
}
