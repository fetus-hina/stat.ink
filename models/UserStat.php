<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "user_stat".
 *
 * @property int $user_id
 * @property int $battle_count
 * @property string $wp
 * @property string $wp_short
 * @property int $total_kill
 * @property int $total_death
 * @property int $nawabari_count
 * @property string $nawabari_wp
 * @property int $nawabari_kill
 * @property int $nawabari_death
 * @property int $gachi_count
 * @property string $gachi_wp
 * @property int $gachi_kill
 * @property int $gachi_death
 * @property int $total_kd_battle_count
 *
 * @property User $user
 */
class UserStat extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_stat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'battle_count', 'total_kill', 'total_death', 'total_kd_battle_count'], 'integer'],
            [['nawabari_count', 'nawabari_kill', 'nawabari_death'], 'integer'],
            [['gachi_count', 'gachi_kill', 'gachi_death'], 'integer'],
            [['wp', 'wp_short', 'nawabari_wp', 'gachi_wp'], 'number'],
            [['nawabari_inked', 'nawabari_inked_max', 'nawabari_inked_battle'], 'integer'],
            [['gachi_kd_battle', 'gachi_kill2', 'gachi_death2'], 'integer'],
            [['gachi_total_time'], 'safe'],
            [['gachi_rank_peak'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'battle_count' => 'Battle Count',
            'wp' => 'Wp',
            'wp_short' => 'Wp Short',
            'total_kill' => 'Total Kill',
            'total_death' => 'Total Death',
            'nawabari_count' => 'Nawabari Count',
            'nawabari_wp' => 'Nawabari Wp',
            'nawabari_kill' => 'Nawabari Kill',
            'nawabari_death' => 'Nawabari Death',
            'gachi_count' => 'Gachi Count',
            'gachi_wp' => 'Gachi Wp',
            'gachi_kill' => 'Gachi Kill',
            'gachi_death' => 'Gachi Death',
            'total_kd_battle_count' => 'Total KD Battle Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function createCurrentData()
    {
        $db = Yii::$app->db;
        static $rules = null;
        if ($rules === null) {
            $rules = [];
            foreach (Rule::find()->asArray()->all() as $rule) {
                $rules[$rule['key']] = (int)$rule['id'];
            }
        }

        $condIsNawabari = sprintf(
            '({{battle}}.[[rule_id]] = %s)',
            $db->quoteValue($rules['nawabari'])
        );
        $condIsArea = sprintf(
            '({{battle}}.[[rule_id]] = %s)',
            $db->quoteValue($rules['area'])
        );
        $condIsYagura = sprintf(
            '({{battle}}.[[rule_id]] = %s)',
            $db->quoteValue($rules['yagura'])
        );
        $condIsHoko = sprintf(
            '({{battle}}.[[rule_id]] = %s)',
            $db->quoteValue($rules['hoko'])
        );
        $condIsGachi = sprintf('(%s)', implode(' OR ', [
            $condIsArea,
            $condIsYagura,
            $condIsHoko,
        ]));

        static $private = null;
        if ($private === null) {
            $private = Lobby::findOne(['key' => 'private'])->id;
        }
        $condIsNotPrivate = sprintf(
            '({{battle}}.[[lobby_id]] IS NULL OR {{battle}}.[[lobby_id]] <> %s)',
            $db->quoteValue($private)
        );

        $now = $_SERVER['REQUEST_TIME'] ?? time();
        $cond24Hours = sprintf(
            '(({{battle}}.[[end_at]] IS NOT NULL) AND ({{battle}}.[[end_at]] BETWEEN %s AND %s))',
            $db->quoteValue(gmdate('Y-m-d H:i:sO', $now - 86400 + 1)),
            $db->quoteValue(gmdate('Y-m-d H:i:sO', $now))
        );

        $condKDPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle}}.[[kill]] IS NOT NULL',
            '{{battle}}.[[death]] IS NOT NULL',
        ]));

        $condBonusPointPresent = '({{turfwar_win_bonus}}.[[bonus]] IS NOT NULL)';

        $condTurfInkedPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle}}.[[my_point]] IS NOT NULL',
            '{{battle}}.[[my_point]] > 0',
            sprintf(
                '{{battle}}.[[my_point]] - (%s) > 0',
                'CASE {{battle}}.[[is_win]] WHEN TRUE THEN {{turfwar_win_bonus}}.[[bonus]] ELSE 0 END'
            ),
        ]));

        $condTimePresent = sprintf('(%s)', implode(' AND ', [
            '{{battle}}.[[start_at]] IS NOT NULL',
            '{{battle}}.[[end_at]] IS NOT NULL',
            '{{battle}}.[[start_at]] < {{battle}}.[[end_at]]',
            "({{battle}}.[[end_at]] - {{battle}}.[[start_at]]) < '10 minutes'::interval",
        ]));

        $column_battle_count = 'COUNT(*)';
        $column_wp = sprintf(
            '(%s * 100.0 / NULLIF(%s, 0))',
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    '{{battle}}.[[is_win]] = TRUE',
                ])
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    '{{battle}}.[[is_win]] IS NOT NULL',
                ])
            )
        );
        $column_wp_short = sprintf(
            '(%s * 100.0 / NULLIF(%s, 0))',
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $cond24Hours,
                    '{{battle}}.[[is_win]] = TRUE',
                ])
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $cond24Hours,
                    '{{battle}}.[[is_win]] IS NOT NULL',
                ])
            )
        );
        $column_total_kd_battle_count = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condKDPresent,
            ])
        );
        $column_total_kill = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condKDPresent,
            ])
        );
        $column_total_death = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[death]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condKDPresent,
            ])
        );
        $column_nawabari_count = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsNawabari,
            ])
        );
        $column_nawabari_wp = sprintf(
            '(%s * 100.0 / NULLIF(%s, 0))',
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $condIsNawabari,
                    '{{battle}}.[[is_win]] = TRUE',
                ])
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $condIsNawabari,
                    '{{battle}}.[[is_win]] IS NOT NULL',
                ])
            )
        );
        $column_nawabari_kill = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsNawabari,
                $condKDPresent,
            ])
        );
        $column_nawabari_death = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[death]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsNawabari,
                $condKDPresent,
            ])
        );
        $column_nawabari_inked = sprintf(
            'SUM(CASE WHEN (%1$s AND %2$s) THEN (%4$s - %5$s) WHEN (%1$s AND %3$s) THEN %4$s ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsNawabari,
                $condBonusPointPresent,
                $condTurfInkedPresent,
            ]),
            '{{battle}}.[[is_win]] = TRUE',
            '{{battle}}.[[is_win]] = FALSE',
            '{{battle}}.[[my_point]]',
            '{{turfwar_win_bonus}}.[[bonus]]'
        );
        $column_nawabari_inked_max = sprintf(
            'MAX(CASE WHEN (%1$s AND %2$s) THEN (%4$s - %5$s) WHEN (%1$s AND %3$s) THEN %4$s ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsNawabari,
                $condBonusPointPresent,
                $condTurfInkedPresent,
            ]),
            '{{battle}}.[[is_win]] = TRUE',
            '{{battle}}.[[is_win]] = FALSE',
            '{{battle}}.[[my_point]]',
            '{{turfwar_win_bonus}}.[[bonus]]'
        );

        $column_nawabari_inked_battle = sprintf(
            'SUM(CASE WHEN (%1$s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsNawabari,
                $condBonusPointPresent,
                $condTurfInkedPresent,
                '{{battle}}.[[is_win]] IS NOT NULL',
            ])
        );

        $column_gachi_count = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsGachi,
            ])
        );
        $column_gachi_wp = sprintf(
            '(%s * 100.0 / NULLIF(%s, 0))',
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $condIsGachi,
                    '{{battle}}.[[is_win]] = TRUE',
                ])
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $condIsGachi,
                    '{{battle}}.[[is_win]] IS NOT NULL',
                ])
            )
        );
        $column_gachi_kill = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsGachi,
                $condKDPresent,
            ])
        );
        $column_gachi_death = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[death]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsGachi,
                $condKDPresent,
            ])
        );
        $column_gachi_kd_battle = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsGachi,
                $condKDPresent,
            ])
        );
        $column_gachi_kill2 = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsGachi,
                $condKDPresent,
                $condTimePresent,
            ])
        );
        $column_gachi_death2 = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[death]] ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsGachi,
                $condKDPresent,
                $condTimePresent,
            ])
        );
        $column_gachi_total_time = sprintf(
            'SUM(CASE WHEN (%s) THEN (%s) ELSE 0 END)',
            implode(' AND ', [
                $condIsNotPrivate,
                $condIsGachi,
                $condKDPresent,
                $condTimePresent,
            ]),
            'EXTRACT(EPOCH FROM ({{battle}}.[[end_at]] - {{battle}}.[[start_at]]))'
        );

        $column_gachi_rank_peak = sprintf(
            'GREATEST(%s, %s)',
            sprintf(
                'MAX(CASE WHEN (%s) THEN (%s) ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $condIsGachi,
                    '{{battle}}.[[rank_id]] IS NOT NULL',
                    '{{battle}}.[[rank_exp]] IS NOT NULL',
                ]),
                '{{rank_before}}.[[int_base]] + {{battle}}.[[rank_exp]]'
            ),
            sprintf(
                'MAX(CASE WHEN (%s) THEN (%s) ELSE 0 END)',
                implode(' AND ', [
                    $condIsNotPrivate,
                    $condIsGachi,
                    '{{battle}}.[[rank_after_id]] IS NOT NULL',
                    '{{battle}}.[[rank_exp_after]] IS NOT NULL',
                ]),
                '{{rank_after}}.[[int_base]] + {{battle}}.[[rank_exp_after]]'
            )
        );

        $query = (new Query())
            ->select([
                'battle_count'      => $column_battle_count,
                'wp'                => $column_wp,
                'wp_short'          => $column_wp_short,
                'total_kill'        => $column_total_kill,
                'total_death'       => $column_total_death,
                'total_kd_battle_count' => $column_total_kd_battle_count,
                'nawabari_count'    => $column_nawabari_count,
                'nawabari_wp'       => $column_nawabari_wp,
                'nawabari_kill'     => $column_nawabari_kill,
                'nawabari_death'    => $column_nawabari_death,
                'nawabari_inked'    => $column_nawabari_inked,
                'nawabari_inked_max' => $column_nawabari_inked_max,
                'nawabari_inked_battle' => $column_nawabari_inked_battle,
                'gachi_count'       => $column_gachi_count,
                'gachi_wp'          => $column_gachi_wp,
                'gachi_kill'        => $column_gachi_kill,
                'gachi_death'       => $column_gachi_death,
                'gachi_kd_battle'   => $column_gachi_kd_battle,
                'gachi_kill2'       => $column_gachi_kill2,
                'gachi_death2'      => $column_gachi_death2,
                'gachi_total_time'  => $column_gachi_total_time,
                'gachi_rank_peak'   => $column_gachi_rank_peak,
            ])
            ->from('battle')
            ->leftJoin('turfwar_win_bonus', '{{battle}}.[[bonus_id]] = {{turfwar_win_bonus}}.[[id]]')
            ->leftJoin('rank rank_before', '{{battle}}.[[rank_id]] = {{rank_before}}.[[id]]')
            ->leftJoin('rank rank_after', '{{battle}}.[[rank_after_id]] = {{rank_after}}.[[id]]')
            ->andWhere(['{{battle}}.[[user_id]]' => $this->user_id]);

        $this->attributes = $query->createCommand()->queryOne();

        $keys = [
            'battle_count', 'total_kill', 'total_death', 'total_kd_battle_count',
            'nawabari_count', 'nawabari_kill', 'nawabari_death',
            'nawabari_inked', 'nawabari_inked_battle', 'nawabari_inked_max',
            'gachi_count', 'gachi_kill', 'gachi_death',
            'gachi_kd_battle', 'gachi_kill2', 'gachi_death2', 'gachi_total_time', 'gachi_rank_peak',
        ];
        foreach ($keys as $key) {
            $this->$key = (int)$this->$key;
        }
        return $this;
    }

    public function toJsonArray()
    {
        return [
            'entire' => [
                'battle_count'  => $this->battle_count,
                'wp'            => $this->wp === null ? null : (float)$this->wp,
                'wp_24h'        => $this->wp_short === null ? null : (float)$this->wp_short,
                'kill'          => $this->total_kill,
                'death'         => $this->total_death,
                'kd_available_battle' => $this->total_kd_battle_count,
            ],
            'nawabari' => [
                'battle_count'  => $this->nawabari_count,
                'wp'            => $this->nawabari_wp === null ? null : (float)$this->nawabari_wp,
                'kill'          => $this->nawabari_kill,
                'death'         => $this->nawabari_death,
            ],
            'gachi' => [
                'battle_count'  => $this->gachi_count,
                'wp'            => $this->gachi_wp === null ? null : (float)$this->gachi_wp,
                'kill'          => $this->gachi_kill,
                'death'         => $this->gachi_death,
            ],
        ];
    }
}
