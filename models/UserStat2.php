<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\Resource;
use app\components\helpers\db\Now;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/**
 * This is the model class for table "user_stat2".
 *
 * @property integer $user_id
 * @property integer $battles
 * @property integer $have_win_lose
 * @property integer $win_battles
 * @property integer $have_kill_death
 * @property integer $kill
 * @property integer $death
 * @property integer $have_kill_death_time
 * @property integer $kill_with_time
 * @property integer $death_with_time
 * @property integer $total_seconds
 * @property integer $turf_battles
 * @property integer $turf_have_win_lose
 * @property integer $turf_win_battles
 * @property integer $turf_have_kill_death
 * @property integer $turf_kill
 * @property integer $turf_death
 * @property integer $turf_have_inked
 * @property integer $turf_total_inked
 * @property integer $turf_max_inked
 * @property integer $gachi_battles
 * @property integer $gachi_have_win_lose
 * @property integer $gachi_win_battles
 * @property integer $gachi_have_kill_death
 * @property integer $gachi_kill
 * @property integer $gachi_death
 * @property integer $gachi_kill_death_time
 * @property integer $gachi_kill_with_time
 * @property integer $gachi_death_with_time
 * @property integer $gachi_total_seconds
 * @property integer $area_rank_peak
 * @property integer $yagura_rank_peak
 * @property integer $hoko_rank_peak
 * @property string $updated_at
 * @property integer $asari_rank_peak
 * @property integer $area_current_rank
 * @property integer $yagura_current_rank
 * @property integer $hoko_current_rank
 * @property integer $asari_current_rank
 * @property string $area_current_x_power
 * @property string $yagura_current_x_power
 * @property string $hoko_current_x_power
 * @property string $asari_current_x_power
 * @property string $area_x_power_peak
 * @property string $yagura_x_power_peak
 * @property string $hoko_x_power_peak
 * @property string $asari_x_power_peak
 *
 * @property User $user
 */
class UserStat2 extends ActiveRecord
{
    use openapi\Util;

    public static function getLock(int $userId, float $timeout = 10.0, bool $autoRelease = true)
    {
        // {{{
        $mutex = Yii::$app->pgMutex;
        $lockName = http_build_query([
            'method' => __METHOD__,
            'id' => (string)$userId,
        ], '', '&');
        Yii::trace(sprintf(
            'Try to get UserStat2 lock for user #%d (%s)',
            $userId,
            (Yii::$app instanceof \yii\web\Application) ? 'webapp' : 'console'
        ));
        $time = microtime(true);
        do {
            if ($mutex->acquire($lockName)) {
                Yii::trace(sprintf(
                    'Got a UserStat2 lock for user #%d (%s)',
                    $userId,
                    (Yii::$app instanceof \yii\web\Application) ? 'webapp' : 'console'
                ));
                if ($autoRelease) {
                    return true;
                } else {
                    return new Resource($lockName, function ($lockName) {
                        $mutex = Yii::$app->pgMutex;
                        $mutex->release($lockName);
                    });
                }
            }
            usleep(50000);
        } while (microtime(true) - $time < $timeout);
        Yii::trace(sprintf(
            'Failed to get a lock for user #%d (%s)',
            $userId,
            (Yii::$app instanceof \yii\web\Application) ? 'webapp' : 'console'
        ));
        return false;
        // }}}
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_stat2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [
                [
                    'user_id', 'battles', 'have_win_lose', 'win_battles', 'have_kill_death',
                    'kill', 'death', 'have_kill_death_time', 'kill_with_time', 'death_with_time',
                    'total_seconds', 'turf_battles', 'turf_have_win_lose', 'turf_win_battles',
                    'turf_have_kill_death', 'turf_kill', 'turf_death', 'turf_have_inked',
                    'turf_total_inked', 'turf_max_inked', 'gachi_battles', 'gachi_have_win_lose',
                    'gachi_win_battles', 'gachi_have_kill_death', 'gachi_kill', 'gachi_death',
                    'gachi_kill_death_time', 'gachi_kill_with_time', 'gachi_death_with_time',
                    'gachi_total_seconds', 'area_rank_peak', 'yagura_rank_peak', 'hoko_rank_peak',
                    'asari_rank_peak', 'area_current_rank', 'yagura_current_rank',
                    'hoko_current_rank', 'asari_current_rank',
                ],
                'default',
                'value' => null,
            ],
            [
                [
                    'user_id', 'battles', 'have_win_lose', 'win_battles', 'have_kill_death',
                    'kill', 'death', 'have_kill_death_time', 'kill_with_time', 'death_with_time',
                    'total_seconds', 'turf_battles', 'turf_have_win_lose', 'turf_win_battles',
                    'turf_have_kill_death', 'turf_kill', 'turf_death', 'turf_have_inked',
                    'turf_total_inked', 'turf_max_inked', 'gachi_battles', 'gachi_have_win_lose',
                    'gachi_win_battles', 'gachi_have_kill_death', 'gachi_kill', 'gachi_death',
                    'gachi_kill_death_time', 'gachi_kill_with_time', 'gachi_death_with_time',
                    'gachi_total_seconds', 'area_rank_peak', 'yagura_rank_peak', 'hoko_rank_peak',
                    'asari_rank_peak', 'area_current_rank', 'yagura_current_rank',
                    'hoko_current_rank', 'asari_current_rank',
                ],
                'integer',
            ],
            [['updated_at'], 'safe'],
            [
                [
                    'area_current_x_power', 'yagura_current_x_power', 'hoko_current_x_power',
                    'asari_current_x_power', 'area_x_power_peak', 'yagura_x_power_peak',
                    'hoko_x_power_peak', 'asari_x_power_peak',
                ],
                'number',
            ],
            [['user_id'], 'unique'],
            [['user_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'battles' => 'Battles',
            'have_win_lose' => 'Have Win Lose',
            'win_battles' => 'Win Battles',
            'have_kill_death' => 'Have Kill Death',
            'kill' => 'Kill',
            'death' => 'Death',
            'have_kill_death_time' => 'Have Kill Death Time',
            'kill_with_time' => 'Kill With Time',
            'death_with_time' => 'Death With Time',
            'total_seconds' => 'Total Seconds',
            'turf_battles' => 'Turf Battles',
            'turf_have_win_lose' => 'Turf Have Win Lose',
            'turf_win_battles' => 'Turf Win Battles',
            'turf_have_kill_death' => 'Turf Have Kill Death',
            'turf_kill' => 'Turf Kill',
            'turf_death' => 'Turf Death',
            'turf_have_inked' => 'Turf Have Inked',
            'turf_total_inked' => 'Turf Total Inked',
            'turf_max_inked' => 'Turf Max Inked',
            'gachi_battles' => 'Gachi Battles',
            'gachi_have_win_lose' => 'Gachi Have Win Lose',
            'gachi_win_battles' => 'Gachi Win Battles',
            'gachi_have_kill_death' => 'Gachi Have Kill Death',
            'gachi_kill' => 'Gachi Kill',
            'gachi_death' => 'Gachi Death',
            'gachi_kill_death_time' => 'Gachi Kill Death Time',
            'gachi_kill_with_time' => 'Gachi Kill With Time',
            'gachi_death_with_time' => 'Gachi Death With Time',
            'gachi_total_seconds' => 'Gachi Total Seconds',
            'area_rank_peak' => 'Area Rank Peak',
            'yagura_rank_peak' => 'Yagura Rank Peak',
            'hoko_rank_peak' => 'Hoko Rank Peak',
            'updated_at' => 'Updated At',
            'asari_rank_peak' => 'Asari Rank Peak',
            'area_current_rank' => 'Area Current Rank',
            'yagura_current_rank' => 'Yagura Current Rank',
            'hoko_current_rank' => 'Hoko Current Rank',
            'asari_current_rank' => 'Asari Current Rank',
            'area_current_x_power' => 'Area Current X Power',
            'yagura_current_x_power' => 'Yagura Current X Power',
            'hoko_current_x_power' => 'Hoko Current X Power',
            'asari_current_x_power' => 'Asari Current X Power',
            'area_x_power_peak' => 'Area X Power Peak',
            'yagura_x_power_peak' => 'Yagura X Power Peak',
            'hoko_x_power_peak' => 'Hoko X Power Peak',
            'asari_x_power_peak' => 'Asari X Power Peak',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => false,
                'value' => function () {
                    return new Now();
                },
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function makeUpdate(): self
    {
        $excludeHaventWinLose = ['WHEN {{battle2}}.[[is_win]] IS NULL THEN 0'];
        $excludeHaventKillDeath = [
            'WHEN {{battle2}}.[[kill]] IS NULL THEN 0',
            'WHEN {{battle2}}.[[death]] IS NULL THEN 0',
        ];
        $excludeHaventTimes = [
            'WHEN {{battle2}}.[[start_at]] IS NULL THEN 0',
            'WHEN {{battle2}}.[[end_at]] IS NULL THEN 0',
        ];
        $excludeHaventInked = array_merge(
            $excludeHaventWinLose,
            ['WHEN {{battle2}}.[[my_point]] IS NULL THEN 0']
        );
        $excludePrivate = [
            "WHEN {{lobby2}}.[[key]] = 'private' THEN 0",
            "WHEN {{mode2}}.[[key]] = 'private' THEN 0",
        ];
        $excludeNonTurfWar = [
            "WHEN {{rule2}}.[[key]] <> 'nawabari' THEN 0",
        ];
        $excludeNonGachi = [
            "WHEN {{rule2}}.[[key]] NOT IN ('area', 'yagura', 'hoko', 'asari') THEN 0",
        ];
        $timestamp = function (string $column): string {
            return sprintf('EXTRACT(EPOCH FROM %s)', $column);
        };
        $gachiRankPeak = function (string $ruleKey) use ($excludePrivate): string {
            // {{{
            $db = $this->getDb();
            return sprintf('MAX(CASE %s END)', implode(' ', array_merge(
                $excludePrivate,
                [
                    sprintf('WHEN {{rule2}}.[[key]] <> %s THEN 0', $db->quoteValue($ruleKey)),
                    "WHEN {{rank2a}}.[[int_base]] IS NULL AND {{rank2b}}.[[int_base]] IS NULL THEN 0",
                    sprintf(
                        'ELSE GREATEST(%s + %s, %s + %s)',
                        sprintf('(CASE %s END)', implode(' ', [
                            'WHEN {{rank2a}}.[[int_base]] IS NULL THEN 0',
                            'ELSE {{rank2a}}.[[int_base]]',
                        ])),
                        sprintf('(CASE %s END)', implode(' ', [
                            'WHEN {{rank2a}}.[[int_base]] IS NULL THEN 0',
                            'WHEN {{battle2}}.[[rank_exp]] IS NULL THEN 0',
                            'ELSE {{battle2}}.[[rank_exp]]',
                        ])),
                        sprintf('(CASE %s END)', implode(' ', [
                            'WHEN {{rank2b}}.[[int_base]] IS NULL THEN 0',
                            'ELSE {{rank2b}}.[[int_base]]',
                        ])),
                        sprintf('(CASE %s END)', implode(' ', [
                            'WHEN {{rank2b}}.[[int_base]] IS NULL THEN 0',
                            'WHEN {{battle2}}.[[rank_after_exp]] IS NULL THEN 0',
                            'ELSE {{battle2}}.[[rank_after_exp]]',
                        ]))
                    ),
                ]
            )));
            // }}}
        };
        $xPowerPeak = function (string $ruleKey) use ($excludePrivate): string {
            // {{{
            $db = $this->getDb();
            return sprintf('NULLIF(MAX(CASE %s END), 0.0)', implode(' ', array_merge(
                $excludePrivate,
                [
                    sprintf('WHEN {{rule2}}.[[key]] <> %s THEN NULL', $db->quoteValue($ruleKey)),
                    sprintf(
                        'WHEN {{rank2a}}.[[key]] <> %1$s AND {{rank2b}}.[[key]] <> %1$s THEN NULL',
                        $db->quoteValue('x')
                    ),
                    vsprintf('ELSE GREATEST(%s, %s)', [
                        '{{battle2}}.[[x_power]]',
                        '{{battle2}}.[[x_power_after]]',
                    ]),
                ]
            )));
            // }}}
        };
        $query = (new Query())
            ->select([
                // 総合 {{{
                'battles' => 'COUNT(*)',
                'have_win_lose' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventWinLose,
                    ['ELSE 1']
                ))),
                'win_battles' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventWinLose,
                    [
                        'WHEN {{battle2}}.[[is_win]] = TRUE THEN 1',
                        'ELSE 0',
                    ]
                ))),
                'have_kill_death' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath,
                    [
                        'ELSE 1',
                    ]
                ))),
                'kill' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath,
                    [
                        'ELSE {{battle2}}.[[kill]]',
                    ]
                ))),
                'death' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath,
                    [
                    'ELSE {{battle2}}.[[death]]',
                    ]
                ))),
                'have_kill_death_time' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath,
                    $excludeHaventTimes,
                    [
                    'ELSE 1',
                    ]
                ))),
                'kill_with_time' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath,
                    $excludeHaventTimes,
                    [
                    'ELSE {{battle2}}.[[kill]]',
                    ]
                ))),
                'death_with_time' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath,
                    $excludeHaventTimes,
                    [
                    'ELSE {{battle2}}.[[death]]',
                    ]
                ))),
                'total_seconds' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath,
                    $excludeHaventTimes,
                    [
                    sprintf(
                        'ELSE (%s - %s)',
                        $timestamp('{{battle2}}.[[end_at]]'),
                        $timestamp('{{battle2}}.[[start_at]]')
                    ),
                    ]
                ))),
                // }}}
                // ナワバリ {{{
                'turf_battles' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    [
                    'ELSE 1',
                    ]
                ))),
                'turf_have_win_lose' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventWinLose,
                    [
                    'ELSE 1',
                    ]
                ))),
                'turf_win_battles' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventWinLose,
                    [
                    'WHEN {{battle2}}.[[is_win]] = TRUE THEN 1',
                    'ELSE 0',
                    ]
                ))),
                'turf_have_kill_death' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventKillDeath,
                    [
                    'ELSE 1',
                    ]
                ))),
                'turf_kill' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventKillDeath,
                    [
                    'ELSE {{battle2}}.[[kill]]',
                    ]
                ))),
                'turf_death' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventKillDeath,
                    [
                    'ELSE {{battle2}}.[[death]]',
                    ]
                ))),
                'turf_have_inked'  => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventInked,
                    [
                    'ELSE 1',
                    ]
                ))),
                'turf_total_inked'  => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventInked,
                    [
                    'WHEN {{battle2}}.[[is_win]] THEN {{battle2}}.[[my_point]] - 1000',
                    'ELSE {{battle2}}.[[my_point]]',
                    ]
                ))),
                'turf_max_inked'  => sprintf('MAX(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventInked,
                    [
                    'WHEN {{battle2}}.[[is_win]] THEN {{battle2}}.[[my_point]] - 1000',
                    'ELSE {{battle2}}.[[my_point]]',
                    ]
                ))),
                // }}}
                // ガチ {{{
                'gachi_battles' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    [
                    'ELSE 1',
                    ]
                ))),
                'gachi_have_win_lose' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventWinLose,
                    [
                    'ELSE 1',
                    ]
                ))),
                'gachi_win_battles' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventWinLose,
                    [
                    'WHEN {{battle2}}.[[is_win]] = TRUE THEN 1',
                    'ELSE 0',
                    ]
                ))),
                'gachi_have_kill_death' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath,
                    [
                    'ELSE 1',
                    ]
                ))),
                'gachi_kill' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath,
                    [
                    'ELSE {{battle2}}.[[kill]]',
                    ]
                ))),
                'gachi_death' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath,
                    [
                    'ELSE {{battle2}}.[[death]]',
                    ]
                ))),
                'gachi_kill_death_time' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath,
                    $excludeHaventTimes,
                    [
                    'ELSE 1',
                    ]
                ))),
                'gachi_kill_with_time' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath,
                    $excludeHaventTimes,
                    [
                    'ELSE {{battle2}}.[[kill]]',
                    ]
                ))),
                'gachi_death_with_time' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath,
                    $excludeHaventTimes,
                    [
                    'ELSE {{battle2}}.[[death]]',
                    ]
                ))),
                'gachi_total_seconds' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath,
                    $excludeHaventTimes,
                    [
                    sprintf(
                        'ELSE (%s - %s)',
                        $timestamp('{{battle2}}.[[end_at]]'),
                        $timestamp('{{battle2}}.[[start_at]]')
                    ),
                    ]
                ))),
                'area_rank_peak' => $gachiRankPeak('area'),
                'yagura_rank_peak' => $gachiRankPeak('yagura'),
                'hoko_rank_peak' => $gachiRankPeak('hoko'),
                'asari_rank_peak' => $gachiRankPeak('asari'),
                'area_x_power_peak' => $xPowerPeak('area'),
                'yagura_x_power_peak' => $xPowerPeak('yagura'),
                'hoko_x_power_peak' => $xPowerPeak('hoko'),
                'asari_x_power_peak' => $xPowerPeak('asari'),
                // }}}
            ])
            ->from('battle2')
            ->leftJoin('lobby2', '{{battle2}}.[[lobby_id]] = {{lobby2}}.[[id]]')
            ->leftJoin('mode2', '{{battle2}}.[[mode_id]] = {{mode2}}.[[id]]')
            ->leftJoin('rule2', '{{battle2}}.[[rule_id]] = {{rule2}}.[[id]]')
            ->leftJoin(['rank2a' => 'rank2'], '{{battle2}}.[[rank_id]] = {{rank2a}}.[[id]]')
            ->leftJoin(['rank2b' => 'rank2'], '{{battle2}}.[[rank_after_id]] = {{rank2b}}.[[id]]')
            ->where(['{{battle2}}.[[user_id]]' => $this->user_id]);

        // echo $query->createCommand()->rawSql . "\n";

        if (!$row = $query->one()) {
            foreach (array_keys($this->attributes) as $k) {
                if ($k === 'user_id' || $k === 'updated_at') {
                    continue;
                }
                $this->$k = 0;
            }
        } else {
            foreach ($row as $k => $v) {
                if (strpos($k, 'x_power') !== false) {
                    $this->$k = (float)$v;
                } else {
                    $this->$k = (int)$v;
                }
            }
            $rules = ['area', 'yagura', 'hoko', 'asari'];
            foreach ($rules as $rule) {
                $_ = $this->getLastBattle($rule);
                $this->{$rule . '_current_rank'} = $_['rank'] ?? null;
                $this->{$rule . '_current_x_power'} = $_['xpower'] ?? null;
            }
        }
        return $this;
    }

    private function getLastBattle(string $ruleKey): array
    {
        $query = (new Query())
            ->from('battle2')
            ->innerJoin('lobby2', '{{battle2}}.[[lobby_id]] = {{lobby2}}.[[id]]')
            ->innerJoin('mode2', '{{battle2}}.[[mode_id]] = {{mode2}}.[[id]]')
            ->innerJoin('rule2', '{{battle2}}.[[rule_id]] = {{rule2}}.[[id]]')
            ->leftJoin(['rank2a' => 'rank2'], '{{battle2}}.[[rank_id]] = {{rank2a}}.[[id]]')
            ->leftJoin(['rank2b' => 'rank2'], '{{battle2}}.[[rank_after_id]] = {{rank2b}}.[[id]]')
            ->andWhere(['and',
                ['{{battle2}}.[[user_id]]' => $this->user_id],
                // ['<>', '{{lobby2}}.[[key]]', 'private'],
                ['{{lobby2}}.[[key]]' => 'standard'],
                ['<>', '{{mode2}}.[[key]]', 'private'],
                ['{{rule2}}.[[key]]' => $ruleKey],
            ])
            ->orderBy(['{{battle2}}.[[id]]' => SORT_DESC])
            ->limit(1)
            ->select([
                'rank' => vsprintf('GREATEST(%s + %s, %s + %s)', [
                    sprintf('(CASE %s END)', implode(' ', [
                        'WHEN {{rank2a}}.[[int_base]] IS NULL THEN 0',
                        'ELSE {{rank2a}}.[[int_base]]',
                    ])),
                    sprintf('(CASE %s END)', implode(' ', [
                        'WHEN {{rank2a}}.[[int_base]] IS NULL THEN 0',
                        'WHEN {{battle2}}.[[rank_exp]] IS NULL THEN 0',
                        'ELSE {{battle2}}.[[rank_exp]]',
                    ])),
                    sprintf('(CASE %s END)', implode(' ', [
                        'WHEN {{rank2b}}.[[int_base]] IS NULL THEN 0',
                        'ELSE {{rank2b}}.[[int_base]]',
                    ])),
                    sprintf('(CASE %s END)', implode(' ', [
                        'WHEN {{rank2b}}.[[int_base]] IS NULL THEN 0',
                        'WHEN {{battle2}}.[[rank_after_exp]] IS NULL THEN 0',
                        'ELSE {{battle2}}.[[rank_after_exp]]',
                    ])),
                ]),
                'xpower' => vsprintf('(CASE %s END)', [
                    'WHEN {{battle2}}.[[x_power_after]] IS NOT NULL THEN {{battle2}}.[[x_power_after]]',
                    'WHEN {{battle2}}.[[x_power]] IS NOT NULL THEN {{battle2}}.[[x_power]]',
                ]),
            ]);
        return $query->one() ?: [];
    }

    public function toJsonArray(): array
    {
        return [
            'updated_at' => DateTimeFormatter::unixTimeToJsonArray(strtotime($this->updated_at)),
            'entire' => [
                'battles' => $this->battles,
                'win_pct' => ($this->have_win_lose > 0)
                    ? ($this->win_battles * 100 / $this->have_win_lose)
                    : null,
                'kill_ratio' => ($this->have_kill_death > 0)
                    ? new JsExpression(
                        // 小数点以下の精度を保証するために
                        // 浮動小数点数をそのまま渡すのではなく、
                        // あらかじめ規定の精度でフォーマットしたものをそのまま出力させる
                        ($this->death > 0)
                            ? sprintf('%.2f', $this->kill / $this->death)
                            : (($this->kill > 0)
                                ? '99.99'
                                : 'null'
                            )
                    )
                    : null,
                'kill_total' => $this->kill,
                'kill_avg' => ($this->have_kill_death > 0)
                    ? $this->kill / $this->have_kill_death
                    : null,
                'kill_per_min' => ($this->have_kill_death_time > 0 && $this->total_seconds > 0)
                    ? ($this->kill_with_time * 60 / $this->total_seconds)
                    : null,
                'death_total' => $this->death,
                'death_avg' => ($this->have_kill_death > 0)
                    ? $this->death / $this->have_kill_death
                    : null,
                'death_per_min' => ($this->have_kill_death_time > 0 && $this->total_seconds > 0)
                    ? ($this->death_with_time * 60 / $this->total_seconds)
                    : null,
            ],
            'nawabari' => [
                'battles' => $this->turf_battles,
                'win_pct' => ($this->turf_have_win_lose > 0)
                    ? $this->turf_win_battles * 100 / $this->turf_have_win_lose
                    : null,
                'kill_ratio' => ($this->turf_have_kill_death > 0)
                    ? new JsExpression(
                        // 小数点以下の精度を保証するために
                        // 浮動小数点数をそのまま渡すのではなく、
                        // あらかじめ規定の精度でフォーマットしたものをそのまま出力させる
                        ($this->turf_death > 0)
                            ? sprintf('%.2f', $this->turf_kill / $this->turf_death)
                            : (($this->turf_kill > 0)
                                ? '99.99' // infinity が返したい
                                : 'null'
                            )
                    )
                    : null,
                'kill_total' => $this->turf_kill,
                'kill_avg' => ($this->turf_have_kill_death > 0)
                    ? $this->turf_kill / $this->turf_have_kill_death
                    : null,
                'kill_per_min' => ($this->turf_have_kill_death > 0)
                    ? $this->turf_kill / $this->turf_have_kill_death / 3.0
                    : null,
                'death_total' => $this->turf_death,
                'death_avg' => ($this->turf_have_kill_death > 0)
                    ? $this->turf_death / $this->turf_have_kill_death
                    : null,
                'death_per_min' => ($this->turf_have_kill_death > 0)
                    ? $this->turf_death / $this->turf_have_kill_death / 3.0
                    : null,
                'total_inked' => $this->turf_total_inked,
                'max_inked' => $this->turf_max_inked,
                'avg_inked' => ($this->turf_have_inked > 0)
                    ? $this->turf_total_inked / $this->turf_have_inked
                    : null,
            ],
            'gachi' => [
                'battles' => $this->gachi_battles,
                'win_pct' => ($this->gachi_have_win_lose > 0)
                    ? $this->gachi_win_battles * 100 / $this->gachi_have_win_lose
                    : null,
                'kill_ratio' => ($this->gachi_have_kill_death > 0)
                    ? new JsExpression(
                        // 小数点以下の精度を保証するために
                        // 浮動小数点数をそのまま渡すのではなく、
                        // あらかじめ規定の精度でフォーマットしたものをそのまま出力させる
                        ($this->gachi_death > 0)
                            ? sprintf('%.2f', $this->gachi_kill / $this->gachi_death)
                            : (($this->gachi_kill > 0)
                                ? '99.99' // infinity が返したい
                                : 'null'
                            )
                    )
                    : null,
                'kill_total' => $this->gachi_kill,
                'kill_avg' => ($this->gachi_have_kill_death > 0)
                    ? $this->gachi_kill / $this->gachi_have_kill_death
                    : null,
                'kill_per_min' => ($this->gachi_kill_death_time > 0 && $this->gachi_total_seconds > 0)
                    ? ($this->gachi_kill_with_time * 60 / $this->gachi_total_seconds)
                    : null,
                'death_total' => $this->gachi_death,
                'death_avg' => ($this->gachi_have_kill_death > 0)
                    ? $this->gachi_death / $this->gachi_have_kill_death
                    : null,
                'death_per_min' => ($this->gachi_kill_death_time > 0 && $this->gachi_total_seconds > 0)
                    ? ($this->gachi_death_with_time * 60 / $this->gachi_total_seconds)
                    : null,
                'rules' => ArrayHelper::map(
                    ['area', 'yagura', 'hoko', 'asari'],
                    function (string $key): string {
                        return $key;
                    },
                    function (string $key): array {
                        return [
                            'rank_peak' => $this->rankFormatForJson(
                                $this->{$key . '_rank_peak'}
                            ),
                            'rank_current' => $this->rankFormatForJson(
                                $this->{$key . '_current_rank'}
                            ),
                            'x_power_peak' => $this->xPowerFormatForJson(
                                $this->{$key . '_x_power_peak'}
                            ),
                            'x_power_current' => $this->xPowerFormatForJson(
                                $this->{$key . '_current_x_power'}
                            ),
                        ];
                    }
                ),
            ],
        ];
    }

    private function rankFormatForJson(?int $value): ?string
    {
        static $cache = [];

        if ($value === null) {
            return null;
        }

        if (!array_key_exists($value, $cache)) {
            $cache[$value] = (function () use ($value) {
                if (!$info = Rank2::parseRankNumber($value)) {
                    return null;
                }
                return ($info[1] === null)
                    ? $info[0] // S+ 以外
                    : implode(' ', [$info[0], (string)$info[1]]); // e.g., "S+ 5"
            })();
        }

        return $cache[$value];
    }

    private function xPowerFormatForJson($value): JsExpression
    {
        $value = filter_var($value, FILTER_VALIDATE_FLOAT);
        return (is_float($value) && $value > 0.0)
            ? new JsExpression(sprintf('%.1f', $value))
            : new JsExpression('null');
    }

    public static function openApiSchema(): array
    {
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'User stats'),
            'properties' => [
                'entire' => [
                    'type' => 'object',
                    'description' => Yii::t('app-apidoc2', 'All battle statistics'),
                    'properties' => [
                        'battles' => [
                            'type' => 'integer',
                            'format' => 'int64',
                            'minimum' => 0,
                            'description' => Yii::t('app-apidoc2', 'Number of battles'),
                        ],
                        'win_pct' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'description' => Yii::t('app-apidoc2', 'Winning percentage'),
                        ],
                        'kill_ratio' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Kill ratio'),
                        ],
                        'kill_total' => [
                            'nullable' => true,
                            'type' => 'integer',
                            'format' => 'int64',
                            'minimum' => 0,
                            'description' => Yii::t('app-apidoc2', 'Total kills'),
                        ],
                        'kill_avg' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Kills per battle'),
                        ],
                        'kill_per_min' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Kills per minute'),
                        ],
                        'death_total' => [
                            'nullable' => true,
                            'type' => 'integer',
                            'format' => 'int64',
                            'minimum' => 0,
                            'description' => Yii::t('app-apidoc2', 'Total deaths'),
                        ],
                        'death_avg' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Deaths per battle'),
                        ],
                        'death_per_min' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Deaths per minute'),
                        ],
                    ],
                ],
                'nawabari' => [
                    'type' => 'object',
                    'description' => Yii::t('app-apidoc2', 'Turf War statistics'),
                    'properties' => [
                        'battles' => [
                            'type' => 'integer',
                            'format' => 'int64',
                            'minimum' => 0,
                            'description' => Yii::t('app-apidoc2', 'Number of battles'),
                        ],
                        'win_pct' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'description' => Yii::t('app-apidoc2', 'Winning percentage'),
                        ],
                        'kill_ratio' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Kill ratio'),
                        ],
                        'kill_total' => [
                            'nullable' => true,
                            'type' => 'integer',
                            'format' => 'int64',
                            'minimum' => 0,
                            'description' => Yii::t('app-apidoc2', 'Total kills'),
                        ],
                        'kill_avg' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Kills per battle'),
                        ],
                        'kill_per_min' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Kills per minute'),
                        ],
                        'death_total' => [
                            'nullable' => true,
                            'type' => 'integer',
                            'format' => 'int64',
                            'minimum' => 0,
                            'description' => Yii::t('app-apidoc2', 'Total deaths'),
                        ],
                        'death_avg' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Deaths per battle'),
                        ],
                        'death_per_min' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Deaths per minute'),
                        ],
                        'total_inked' => [
                            'nullable' => true,
                            'type' => 'integer',
                            'format' => 'int64',
                            'minimum' => 0,
                            'description' => Yii::t('app-apidoc2', 'Total inked'),
                        ],
                        'max_inked' => [
                            'nullable' => true,
                            'type' => 'integer',
                            'format' => 'int64',
                            'minimum' => 0,
                            'description' => Yii::t('app-apidoc2', 'Maximum inked'),
                        ],
                        'avg_inked' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'description' => Yii::t('app-apidoc2', 'Average inked per battle'),
                        ],
                    ],
                ],
                'gachi' => [
                    'type' => 'object',
                    'description' => Yii::t('app-apidoc2', 'Ranked + league battle statistics'),
                    'properties' => [
                        'battles' => [
                            'type' => 'integer',
                            'format' => 'int64',
                            'minimum' => 0,
                            'description' => Yii::t('app-apidoc2', 'Number of battles'),
                        ],
                        'win_pct' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'description' => Yii::t('app-apidoc2', 'Winning percentage'),
                        ],
                        'kill_ratio' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Kill ratio'),
                        ],
                        'kill_total' => [
                            'nullable' => true,
                            'type' => 'integer',
                            'format' => 'int64',
                            'minimum' => 0,
                            'description' => Yii::t('app-apidoc2', 'Total kills'),
                        ],
                        'kill_avg' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Kills per battle'),
                        ],
                        'kill_per_min' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Kills per minute'),
                        ],
                        'death_total' => [
                            'nullable' => true,
                            'type' => 'integer',
                            'format' => 'int64',
                            'minimum' => 0,
                            'description' => Yii::t('app-apidoc2', 'Total deaths'),
                        ],
                        'death_avg' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Deaths per battle'),
                        ],
                        'death_per_min' => [
                            'nullable' => true,
                            'type' => 'number',
                            'format' => 'float',
                            'minimum' => 0.0,
                            'maximum' => 100.0,
                            'exclusiveMaximum' => true,
                            'description' => Yii::t('app-apidoc2', 'Deaths per minute'),
                        ],
                        'rule' => [
                            'type' => 'object',
                            'description' => Yii::t('app-apidoc2', 'Per rule information'),
                            'properties' => [
                                'area' => static::openApiSchemaRule('Splat Zones'),
                                'yagura' => static::openApiSchemaRule('Tower Control'),
                                'hoko' => static::openApiSchemaRule('Rainmaker'),
                                'asari' => static::openApiSchemaRule('Clam Blitz'),
                            ],
                        ],
                    ],
                ],
                'updated_at' => array_merge(openapi\DateTime::openApiSchema(), [
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Last updated at'),
                ]),
            ],
            'example' => static::openapiExample(),
        ];
    }

    private static function openApiSchemaRule(string $rule): array
    {
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Statistics for {rule}', [
                'rule' => Yii::t('app-rule2', $rule),
            ]),
            'properties' => [
                'rank_peak' => [
                    'nullable' => true,
                    'type' => 'string',
                    'description' => Yii::t('app-apidoc2', 'Highest rank ever'),
                ],
                'rank_current' => [
                    'nullable' => true,
                    'type' => 'string',
                    'description' => Yii::t('app-apidoc2', 'Current rank'),
                ],
                'x_power_peak' => [
                    'nullable' => true,
                    'type' => 'number',
                    'format' => 'float',
                    'description' => Yii::t('app-apidoc2', 'Highest X-power ever'),
                ],
                'x_power_current' => [
                    'nullable' => true,
                    'type' => 'number',
                    'format' => 'float',
                    'description' => Yii::t('app-apidoc2', 'Current X-power'),
                ],
            ],
        ];
    }

    public static function openApiDepends(): array
    {
        return [
        ];
    }

    public static function openapiExample(): array
    {
        $model = Yii::createObject([
            '__class' => static::class,
            'battles' => 9432,
            'have_win_lose' => 9432,
            'win_battles' => 5264,
            'have_kill_death' => 9432,
            'kill' => 25465,
            'death' => 31435,
            'have_kill_death_time' => 9432,
            'kill_with_time' => 25465,
            'death_with_time' => 31435,
            'total_seconds' => 1711900,
            'turf_battles' => 9201,
            'turf_have_win_lose' => 9201,
            'turf_win_battles' => 5151,
            'turf_have_kill_death' => 9201,
            'turf_kill' => 24551,
            'turf_death' => 30080,
            'turf_have_inked' => 9201,
            'turf_total_inked' => 9640486,
            'turf_max_inked' => 1890,
            'gachi_battles' => 178,
            'gachi_have_win_lose' => 178,
            'gachi_win_battles' => 83,
            'gachi_have_kill_death' => 178,
            'gachi_kill' => 785,
            'gachi_death' => 1136,
            'gachi_kill_death_time' => 178,
            'gachi_kill_with_time' => 785,
            'gachi_death_with_time' => 1136,
            'gachi_total_seconds' => 44941,
            'area_rank_peak' => 400,
            'yagura_rank_peak' => 400,
            'hoko_rank_peak' => 300,
            'updated_at' => '2019-12-26T06:44:42+09:00',
            'asari_rank_peak' => 500,
            'area_current_rank' => 400,
            'yagura_current_rank' => 400,
            'hoko_current_rank' => null,
            'asari_current_rank' => 500,
            'area_current_x_power' => null,
            'yagura_current_x_power' => null,
            'hoko_current_x_power' => null,
            'asari_current_x_power' => null,
            'area_x_power_peak' => '0.0',
            'yagura_x_power_peak' => '0.0',
            'hoko_x_power_peak' => '0.0',
            'asari_x_power_peak' => '0.0',
        ]);
        return $model->toJsonArray();
    }
}
