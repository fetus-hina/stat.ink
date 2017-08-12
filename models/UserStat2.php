<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Query;

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
 * @property integer $gachi_total_seconds
 * @property integer $area_rank_peak
 * @property integer $yagura_rank_peak
 * @property integer $hoko_rank_peak
 * @property string $updated_at
 *
 * @property User $user
 */
class UserStat2 extends ActiveRecord
{
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
            [['user_id', 'battles', 'have_win_lose', 'win_battles', 'have_kill_death', 'kill', 'death'], 'integer'],
            [['have_kill_death_time', 'total_seconds', 'turf_battles', 'turf_have_win_lose'], 'integer'],
            [['turf_win_battles', 'turf_have_kill_death', 'turf_kill', 'turf_death'], 'integer'],
            [['turf_have_inked', 'turf_total_inked'], 'integer'],
            [['turf_max_inked', 'gachi_battles', 'gachi_have_win_lose', 'gachi_win_battles'], 'integer'],
            [['gachi_have_kill_death', 'gachi_kill', 'gachi_death', 'gachi_kill_death_time'], 'integer'],
            [['gachi_total_seconds', 'area_rank_peak', 'yagura_rank_peak', 'hoko_rank_peak'], 'integer'],
            [['updated_at'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true,
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
            'gachi_total_seconds' => 'Gachi Total Seconds',
            'area_rank_peak' => 'Area Rank Peak',
            'yagura_rank_peak' => 'Yagura Rank Peak',
            'hoko_rank_peak' => 'Hoko Rank Peak',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => false,
                'value' => function () {
                    return date(\DateTime::ATOM, time());
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

    public function makeUpdate() : self
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
            "WHEN {{rule2}}.[[key]] NOT IN ('area', 'yagura', 'hoko') THEN 0",
        ];
        $timestamp = function (string $column) : string {
            return sprintf('EXTRACT(EPOCH FROM %s)', $column);
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
                    $excludeHaventWinLose, [
                    'WHEN {{battle2}}.[[is_win]] = TRUE THEN 1',
                    'ELSE 0',
                ]))),
                'have_kill_death' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath, [
                    'ELSE 1',
                ]))),
                'kill' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath, [
                    'ELSE {{battle2}}.[[kill]]',
                ]))),
                'death' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath, [
                    'ELSE {{battle2}}.[[death]]',
                ]))),
                'have_kill_death_time' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath,
                    $excludeHaventTimes, [
                    'ELSE 1',
                ]))),
                'kill_with_time' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath,
                    $excludeHaventTimes, [
                    'ELSE {{battle2}}.[[kill]]',
                ]))),
                'death_with_time' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath,
                    $excludeHaventTimes, [
                    'ELSE {{battle2}}.[[death]]',
                ]))),
                'total_seconds' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludeHaventKillDeath,
                    $excludeHaventTimes, [
                    sprintf(
                        'ELSE (%s - %s)',
                        $timestamp('{{battle2}}.[[end_at]]'),
                        $timestamp('{{battle2}}.[[start_at]]')
                    ),
                ]))),
                // }}}
                // ナワバリ {{{
                'turf_battles' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar, [
                    'ELSE 1',
                ]))),
                'turf_have_win_lose' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventWinLose, [
                    'ELSE 1',
                ]))),
                'turf_win_battles' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventWinLose, [
                    'WHEN {{battle2}}.[[is_win]] = TRUE THEN 1',
                    'ELSE 0',
                ]))),
                'turf_have_kill_death' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventKillDeath, [
                    'ELSE 1',
                ]))),
                'turf_kill' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventKillDeath, [
                    'ELSE {{battle2}}.[[kill]]',
                ]))),
                'turf_death' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventKillDeath, [
                    'ELSE {{battle2}}.[[death]]',
                ]))),
                'turf_have_inked'  => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventInked, [
                    'ELSE 1',
                ]))),
                'turf_total_inked'  => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventInked, [
                    'WHEN {{battle2}}.[[is_win]] THEN {{battle2}}.[[my_point]] - 1000',
                    'ELSE {{battle2}}.[[my_point]]',
                ]))),
                'turf_max_inked'  => sprintf('MAX(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonTurfWar,
                    $excludeHaventInked, [
                    'WHEN {{battle2}}.[[is_win]] THEN {{battle2}}.[[my_point]] - 1000',
                    'ELSE {{battle2}}.[[my_point]]',
                ]))),
                // }}}
                // ガチ {{{
                'gachi_battles' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi, [
                    'ELSE 1',
                ]))),
                'gachi_have_win_lose' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventWinLose, [
                    'ELSE 1',
                ]))),
                'gachi_win_battles' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventWinLose, [
                    'WHEN {{battle2}}.[[is_win]] = TRUE THEN 1',
                    'ELSE 0',
                ]))),
                'gachi_have_kill_death' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath, [
                    'ELSE 1',
                ]))),
                'gachi_kill' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath, [
                    'ELSE {{battle2}}.[[kill]]',
                ]))),
                'gachi_death' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath, [
                    'ELSE {{battle2}}.[[death]]',
                ]))),
                'gachi_kill_death_time' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath,
                    $excludeHaventTimes, [
                    'ELSE 1',
                ]))),
                'gachi_kill_with_time' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath,
                    $excludeHaventTimes, [
                    'ELSE {{battle2}}.[[kill]]',
                ]))),
                'gachi_death_with_time' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath,
                    $excludeHaventTimes, [
                    'ELSE {{battle2}}.[[death]]',
                ]))),
                'gachi_total_seconds' => sprintf('SUM(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate,
                    $excludeNonGachi,
                    $excludeHaventKillDeath,
                    $excludeHaventTimes, [
                    sprintf(
                        'ELSE (%s - %s)',
                        $timestamp('{{battle2}}.[[end_at]]'),
                        $timestamp('{{battle2}}.[[start_at]]')
                    ),
                ]))),
                'area_rank_peak' => sprintf('MAX(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate, [
                    "WHEN {{rule2}}.[[key]] <> 'area' THEN 0",
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
                ]))),
                'yagura_rank_peak' => sprintf('MAX(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate, [
                    "WHEN {{rule2}}.[[key]] <> 'yagura' THEN 0",
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
                ]))),
                'hoko_rank_peak' => sprintf('MAX(CASE %s END)', implode(' ', array_merge(
                    $excludePrivate, [
                    "WHEN {{rule2}}.[[key]] <> 'hoko' THEN 0",
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
                ]))),
                // }}}
            ])
            ->from('battle2')
            ->leftJoin('lobby2', '{{battle2}}.[[lobby_id]] = {{lobby2}}.[[id]]')
            ->leftJoin('mode2', '{{battle2}}.[[mode_id]] = {{mode2}}.[[id]]')
            ->leftJoin('rule2', '{{battle2}}.[[rule_id]] = {{rule2}}.[[id]]')
            ->leftJoin(['rank2a' => 'rank2'], '{{battle2}}.[[rank_id]] = {{rank2a}}.[[id]]')
            ->leftJoin(['rank2b' => 'rank2'], '{{battle2}}.[[rank_after_id]] = {{rank2b}}.[[id]]')
            ->where(['{{battle2}}.[[user_id]]' => $this->user_id]);
        if (!$row = $query->one()) {
            foreach (array_keys($this->attributes) as $k) {
                if ($k === 'user_id' || $k === 'updated_at') {
                    continue;
                }
                $this->$k = 0;
            }
        } else {
            foreach ($row as $k => $v) {
                $this->$k = (int)$v;
            }
        }
        return $this;
    }
}
