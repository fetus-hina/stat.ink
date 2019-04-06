<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use app\components\helpers\db\Now;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "user_rule_stat2".
 *
 * @property integer $user_id
 * @property integer $mode_id
 * @property integer $rule_id
 * @property integer $battles
 * @property integer $have_win_lose
 * @property integer $battles_win
 * @property integer $have_kill
 * @property integer $total_kill
 * @property integer $seconds_kill
 * @property integer $min_kill
 * @property double $pct5_kill
 * @property double $q1_4_kill
 * @property double $median_kill
 * @property double $q3_4_kill
 * @property double $pct95_kill
 * @property integer $max_kill
 * @property double $stddev_kill
 * @property integer $have_death
 * @property integer $total_death
 * @property integer $seconds_death
 * @property integer $min_death
 * @property double $pct5_death
 * @property double $q1_4_death
 * @property double $median_death
 * @property double $q3_4_death
 * @property double $pct95_death
 * @property integer $max_death
 * @property double $stddev_death
 * @property integer $have_assist
 * @property integer $total_assist
 * @property integer $seconds_assist
 * @property integer $min_assist
 * @property double $pct5_assist
 * @property double $q1_4_assist
 * @property double $median_assist
 * @property double $q3_4_assist
 * @property double $pct95_assist
 * @property integer $max_assist
 * @property double $stddev_assist
 * @property integer $have_inked
 * @property integer $total_inked
 * @property integer $seconds_inked
 * @property integer $min_inked
 * @property double $pct5_inked
 * @property double $q1_4_inked
 * @property double $median_inked
 * @property double $q3_4_inked
 * @property double $pct95_inked
 * @property integer $max_inked
 * @property double $stddev_inked
 * @property integer $have_power
 * @property integer $total_power
 * @property integer $seconds_power
 * @property integer $min_power
 * @property double $pct5_power
 * @property double $q1_4_power
 * @property double $median_power
 * @property double $q3_4_power
 * @property double $pct95_power
 * @property integer $max_power
 * @property double $stddev_power
 * @property integer $rank_peak
 * @property integer $rank_current
 * @property integer $power_current
 * @property string $updated_at
 *
 * @property Mode2 $mode
 * @property Rule2 $rule
 * @property User $user
 */
class UserRuleStat2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'user_rule_stat2';
    }

    public function rules()
    {
        return [
            [['user_id', 'mode_id', 'rule_id', 'updated_at'], 'required'],
            [
                [
                    'user_id',
                    'mode_id',
                    'rule_id',
                    'battles',
                    'have_win_lose',
                    'battles_win',
                    'have_kill',
                    'total_kill',
                    'seconds_kill',
                    'min_kill',
                    'max_kill',
                    'have_death',
                    'total_death',
                    'seconds_death',
                    'min_death',
                    'max_death',
                    'have_assist',
                    'total_assist',
                    'seconds_assist',
                    'min_assist',
                    'max_assist',
                    'have_inked',
                    'total_inked',
                    'seconds_inked',
                    'min_inked',
                    'max_inked',
                    'have_power',
                    'total_power',
                    'seconds_power',
                    'min_power',
                    'max_power',
                    'rank_peak',
                    'rank_current',
                    'power_current',
                ],
                'default',
                'value' => null,
            ],
            [
                [
                    'user_id',
                    'mode_id',
                    'rule_id',
                    'battles',
                    'have_win_lose',
                    'battles_win',
                    'have_kill',
                    'total_kill',
                    'seconds_kill',
                    'min_kill',
                    'max_kill',
                    'have_death',
                    'total_death',
                    'seconds_death',
                    'min_death',
                    'max_death',
                    'have_assist',
                    'total_assist',
                    'seconds_assist',
                    'min_assist',
                    'max_assist',
                    'have_inked',
                    'total_inked',
                    'seconds_inked',
                    'min_inked',
                    'max_inked',
                    'have_power',
                    'total_power',
                    'seconds_power',
                    'min_power',
                    'max_power',
                    'rank_peak',
                    'rank_current',
                    'power_current',
                ],
                'integer',
            ],
            [
                [
                    'pct5_kill',
                    'q1_4_kill',
                    'median_kill',
                    'q3_4_kill',
                    'pct95_kill',
                    'stddev_kill',
                    'pct5_death',
                    'q1_4_death',
                    'median_death',
                    'q3_4_death',
                    'pct95_death',
                    'stddev_death',
                    'pct5_assist',
                    'q1_4_assist',
                    'median_assist',
                    'q3_4_assist',
                    'pct95_assist',
                    'stddev_assist',
                    'pct5_inked',
                    'q1_4_inked',
                    'median_inked',
                    'q3_4_inked',
                    'pct95_inked',
                    'stddev_inked',
                    'pct5_rank',
                    'q1_4_rank',
                    'median_rank',
                    'q3_4_rank',
                    'pct95_rank',
                    'stddev_rank',
                    'pct5_power',
                    'q1_4_power',
                    'median_power',
                    'q3_4_power',
                    'pct95_power',
                    'stddev_power',
                ],
                'number',
            ],
            [['updated_at'], 'safe'],
            [['user_id', 'mode_id', 'rule_id'], 'unique',
                'targetAttribute' => [
                    'user_id',
                    'mode_id',
                    'rule_id',
                ],
            ],
            [['mode_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Mode2::class,
                'targetAttribute' => ['mode_id' => 'id'],
            ],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'mode_id' => 'Mode ID',
            'rule_id' => 'Rule ID',
            'battles' => 'Battles',
            'have_win_lose' => 'Have Win Lose',
            'battles_win' => 'Battles Win',
            'have_kill' => 'Have Kill',
            'total_kill' => 'Total Kill',
            'seconds_kill' => 'Seconds Kill',
            'min_kill' => 'Min Kill',
            'pct5_kill' => 'Pct5 Kill',
            'q1_4_kill' => 'Q1 4 Kill',
            'median_kill' => 'Median Kill',
            'q3_4_kill' => 'Q3 4 Kill',
            'pct95_kill' => 'Pct95 Kill',
            'max_kill' => 'Max Kill',
            'stddev_kill' => 'Stddev Kill',
            'have_death' => 'Have Death',
            'total_death' => 'Total Death',
            'seconds_death' => 'Seconds Death',
            'min_death' => 'Min Death',
            'pct5_death' => 'Pct5 Death',
            'q1_4_death' => 'Q1 4 Death',
            'median_death' => 'Median Death',
            'q3_4_death' => 'Q3 4 Death',
            'pct95_death' => 'Pct95 Death',
            'max_death' => 'Max Death',
            'stddev_death' => 'Stddev Death',
            'have_assist' => 'Have Assist',
            'total_assist' => 'Total Assist',
            'seconds_assist' => 'Seconds Assist',
            'min_assist' => 'Min Assist',
            'pct5_assist' => 'Pct5 Assist',
            'q1_4_assist' => 'Q1 4 Assist',
            'median_assist' => 'Median Assist',
            'q3_4_assist' => 'Q3 4 Assist',
            'pct95_assist' => 'Pct95 Assist',
            'max_assist' => 'Max Assist',
            'stddev_assist' => 'Stddev Assist',
            'have_inked' => 'Have Inked',
            'total_inked' => 'Total Inked',
            'seconds_inked' => 'Seconds Inked',
            'min_inked' => 'Min Inked',
            'pct5_inked' => 'Pct5 Inked',
            'q1_4_inked' => 'Q1 4 Inked',
            'median_inked' => 'Median Inked',
            'q3_4_inked' => 'Q3 4 Inked',
            'pct95_inked' => 'Pct95 Inked',
            'max_inked' => 'Max Inked',
            'stddev_inked' => 'Stddev Inked',
            'have_power' => 'Have Power',
            'total_power' => 'Total Power',
            'seconds_power' => 'Seconds Power',
            'min_power' => 'Min Power',
            'pct5_power' => 'Pct5 Power',
            'q1_4_power' => 'Q1 4 Power',
            'median_power' => 'Median Power',
            'q3_4_power' => 'Q3 4 Power',
            'pct95_power' => 'Pct95 Power',
            'max_power' => 'Max Power',
            'stddev_power' => 'Stddev Power',
            'rank_peak' => 'Rank Peak',
            'rank_current' => 'Rank Current',
            'power_current' => 'Power Current',
            'updated_at' => 'Updated At',
        ];
    }

    public function getMode(): ActiveQuery
    {
        return $this->hasOne(Mode2::class, ['id' => 'mode_id']);
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function updateStats(?User $user): bool
    {
        return Yii::$app->db->transactionEx(function ($db) use ($user): bool {
            return static::updateStatsMain($user);
        });
    }

    private static function updateStatsMain(?User $user): bool
    {
        $turfWar = Rule2::findOne(['key' => 'nawabari']);

        $stats = function (string $nameAttr, string $dataAttr): array {
            $pct = function (string $percentile) use ($dataAttr): string {
                return "PERCENTILE_CONT({$percentile}) WITHIN GROUP (ORDER BY {$dataAttr})";
            };

            $toUnixTime = function (string $attr): string {
                return "EXTRACT(EPOCH FROM {$attr})"; 
            };

            $battleTimeConditions = [
                "WHEN battle2.start_at IS NULL THEN 0",
                "WHEN battle2.end_at IS NULL THEN 0",
                "WHEN battle2.end_at <= battle2.start_at THEN 0",
                "WHEN {$dataAttr} IS NULL THEN 0",
            ];

            $sumBattleTimeMin = sprintf('(SUM(CASE %s END) / 60.0)', implode(' ', array_merge(
                $battleTimeConditions,
                [
                    vsprintf('ELSE (%s - %s)', [
                        $toUnixTime('battle2.end_at'),
                        $toUnixTime('battle2.start_at'),
                    ]),
                ],
            )));

            $sumWhereTimeAvailable = sprintf(
                'SUM(CASE %s END)::double precision',
                implode(' ', array_merge(
                    $battleTimeConditions,
                    [
                        "ELSE {$dataAttr}",
                    ],
                ))
            );

            return [
                "avg_{$nameAttr}" => "AVG({$dataAttr})",
                "min_{$nameAttr}" => "MIN({$dataAttr})",
                "pct5_{$nameAttr}" => $pct('0.05'),
                "q1_4_{$nameAttr}" => $pct('0.25'),
                "median_{$nameAttr}" => $pct('0.5'),
                "q3_4_{$nameAttr}" => $pct('0.75'),
                "pct95_{$nameAttr}" => $pct('0.95'),
                "max_{$nameAttr}" => "MAX({$dataAttr})",
                "stddev_{$nameAttr}" => "STDDEV_POP({$dataAttr})",
                "{$nameAttr}_per_min" => sprintf(
                    '(%s / NULLIF(%s, 0))',
                    $sumWhereTimeAvailable,
                    $sumBattleTimeMin,
                ),
            ];
        };
        $select = (new Query())
            ->from('battle2')
            ->joinLeft(['rank2b' => 'rank2'], 'battle2.rank_id = rank2b.id')
            ->joinLeft(['rank2a' => 'rank2'], 'battle2.rank_after_id = rank2a.id')
            ->andWhere(['and',
                ['not', ['battle2.mode_id' => null]],
                ['not', ['battle2.rule_id' => null]],
            ])
            ->groupBy([
                'battle2.user_id',
                'battle2.mode_id',
                'battle2.rule_id',
            ])
            ->select(array_merge(
                [
                    'user_id' => 'battle2.user_id',
                    'mode_id' => 'battle2.mode_id',
                    'rule_id' => 'battle2.rule_id',
                    'battles' => 'COUNT(*)',
                    'have_win_lose' => 'SUM(CASE WHEN battle2.is_win IS NULL THEN 0 ELSE 1 END)',
                    'battles_win' => 'SUM(CASE WHEN battle2.is_win THEN 1 ELSE 0 END)',
                ],
                $stats('kill', 'battle2.kill'),
                $stats('death', 'battle2.death'),
                $stats('assist', '(battle2.kill_or_assist - battle2.kill)'),
                $stats('inked', sprintf('(CASE %s END)', implode(' ', [
                    sprintf('WHEN battle2.rule_id <> %d THEN NULL', $turfWar->id),
                    'WHEN battle2.is_win IS NULL THEN NULL',
                    'WHEN battle2.is_win AND battle2.my_point < 1000 THEN NULL',
                    'WHEN battle2.is_win THEN battle2.my_point - 1000',
                    'ELSE battle2.my_point',
                ]))),
                $stats('rank', sprintf('(CASE %s END)', implode(' ', [
                    sprintf('WHEN battle2.rule_id = %d THEN NULL', $turfWar->id),
                    sprintf(
                        "WHEN rule2b.key === 'x' THEN rule2b.int_base + 
                        'WHEN %1$s IS NOT NULL THEN LEAST(%1$s, 1100)',

                    '(rule2b.int_base + battle2.rank_exp)'
                ]))),
                $stats('power', 'NULL'),
                [
                    'rank_current' => '(NULL)',
                    'power_current' => '(NULL)',
                    'updated_at' => new Now(),
                ],
            ));
        if ($user) {
            $select->andWhere(['battle2.user_id' => $user->id]);
        }
        echo $select->createCommand()->rawSql . "\n";
        return false;
    }
}
