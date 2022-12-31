<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 * @author Yoshiyuki Kawashima <ykawashi7@gmail.com>
 * @author li <nvblstr@gmail.com>
 */

namespace app\models;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Throwable;
use Yii;
use app\components\behaviors\RemoteAddrBehavior;
use app\components\behaviors\RemotePortBehavior;
use app\components\behaviors\TimestampBehavior;
use app\components\helpers\Battle as BattleHelper;
use app\components\helpers\BattleSummarizer;
use app\components\helpers\DateTimeFormatter;
use app\components\jobs\UserStatsJob;
use jp3cki\uuid\Uuid;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

/**
 * This is the model class for table "battle2".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $lobby_id
 * @property integer $mode_id
 * @property integer $rule_id
 * @property integer $map_id
 * @property integer $weapon_id
 * @property boolean $is_win
 * @property boolean $is_knockout
 * @property integer $level
 * @property integer $level_after
 * @property integer $rank_id
 * @property integer $rank_exp
 * @property integer $rank_after_id
 * @property integer $rank_after_exp
 * @property integer $rank_in_team
 * @property integer $kill
 * @property integer $death
 * @property string $kill_ratio
 * @property string $kill_rate
 * @property integer $max_kill_combo
 * @property integer $max_kill_streak
 * @property integer $my_point
 * @property integer $my_team_point
 * @property integer $his_team_point
 * @property string $my_team_percent
 * @property string $his_team_percent
 * @property integer $my_team_count
 * @property integer $his_team_count
 * @property integer $my_team_color_hue
 * @property integer $his_team_color_hue
 * @property string $my_team_color_rgb
 * @property string $his_team_color_rgb
 * @property integer $cash
 * @property integer $cash_after
 * @property string $note
 * @property string $private_note
 * @property string $link_url
 * @property integer $period
 * @property integer $version_id
 * @property integer $bonus_id
 * @property integer $env_id
 * @property string $client_uuid
 * @property array $ua_variables
 * @property string $ua_custom
 * @property integer $agent_game_version_id
 * @property string $agent_game_version_date
 * @property integer $agent_id
 * @property boolean $is_automated
 * @property boolean $use_for_entire
 * @property string $remote_addr
 * @property integer $remote_port
 * @property string $start_at
 * @property string $end_at
 * @property string $created_at
 * @property string $updated_at
 * @property integer $kill_or_assist
 * @property integer $special
 * @property integer $gender_id
 * @property integer $fest_title_id
 * @property integer $fest_exp
 * @property integer $fest_title_after_id
 * @property integer $fest_exp_after
 * @property integer $splatnet_number
 * @property string $my_team_id
 * @property string $his_team_id
 * @property integer $estimate_gachi_power
 * @property string $league_point
 * @property integer $my_team_estimate_league_point
 * @property integer $his_team_estimate_league_point
 * @property string $fest_power
 * @property integer $my_team_estimate_fest_power
 * @property integer $his_team_estimate_fest_power
 * @property integer $headgear_id
 * @property integer $clothing_id
 * @property integer $shoes_id
 * @property integer $star_rank
 * @property integer $my_team_fest_theme_id
 * @property integer $his_team_fest_theme_id
 * @property string $x_power
 * @property string $x_power_after
 * @property integer $estimate_x_power
 * @property integer $species_id
 * @property integer $special_battle_id
 * @property integer $my_team_nickname_id
 * @property integer $his_team_nickname_id
 * @property integer $clout
 * @property integer $total_clout
 * @property integer $total_clout_after
 * @property string $synergy_bonus
 * @property integer $my_team_win_streak
 * @property integer $his_team_win_streak
 * @property string $freshness
 * @property boolean $has_disconnect
 *
 * @property Agent $agent
 * @property SplatoonVersion2 $agentGameVersion
 * @property Battle2Splatnet $battle2Splatnet
 * @property BattleDeathReason2[] $battleDeathReason2s
 * @property BattleEvents2 $battleEvents2
 * @property BattleImage2[] $battleImage2s
 * @property BattlePlayer2[] $battlePlayer2s
 * @property TurfwarWinBonus2 $bonus
 * @property GearConfiguration2 $clothing
 * @property Environment $env
 * @property FestTitle $festTitle
 * @property FestTitle $festTitleAfter
 * @property Gender $gender
 * @property GearConfiguration2 $headgear
 * @property Splatfest2Theme $hisTeamFestTheme
 * @property TeamNickname2 $hisTeamNickname
 * @property Lobby2 $lobby
 * @property Map2 $map
 * @property Mode2 $mode
 * @property Splatfest2Theme $myTeamFestTheme
 * @property TeamNickname2 $myTeamNickname
 * @property Rank2 $rank
 * @property Rank2 $rankAfter
 * @property DeathReason2[] $reasons
 * @property Rule2 $rule
 * @property GearConfiguration2 $shoes
 * @property SpecialBattle2 $specialBattle
 * @property Species2 $species
 * @property BattleImageType[] $types
 * @property User $user
 * @property SplatoonVersion2 $version
 * @property Weapon2 $weapon
 *
 * @property-read BattleImage2|null $battleImageGear
 * @property-read BattleImage2|null $battleImageJudge
 * @property-read BattleImage2|null $battleImageResult
 */
class Battle2 extends ActiveRecord
{
    protected const CLIENT_UUID_NAMESPACE = '15de9082-1c7b-11e7-8f94-001b21a098c2';

    public $freshness_id;

    public static function getRoughCount(): ?int
    {
        try {
            $count = filter_var(
                (new Query())
                    ->select('[[last_value]]')
                    ->from('{{battle2_id_seq}}')
                    ->scalar(),
                FILTER_VALIDATE_INT,
            );
            if (is_int($count)) {
                return $count;
            }
        } catch (Throwable $e) {
        }

        return null;
    }

    public static function find()
    {
        return new class (static::class) extends ActiveQuery {
            public function withFreshness(): self
            {
                if (!$this->select) {
                    list(, $alias) = $this->getTableNameAndAlias();
                    $this->select = ["{$alias}.*"];
                }
                $this->select['freshness_id'] = 'freshness2.id';
                $this->join[] = [
                    'LEFT JOIN',
                    'freshness2',
                    "{$alias}.freshness <@ freshness2.range",
                ];
                return $this;
            }

            public function applyFilter(Battle2FilterForm $form): self
            {
                $and = ['and'];
                if ($form->screen_name != '') {
                    $this->innerJoinWith('user');
                    $and[] = ['{{user}}.[[screen_name]]' => $form->screen_name];
                }
                if ($form->rule != '') {
                    // {{{
                    $parts = explode('-', $form->rule);
                    if (count($parts) === 3) {
                        $this->innerJoinWith(['lobby', 'mode', 'rule']);
                        switch ($parts[0]) {
                            case 'any':
                                break;

                            case 'any_squad':
                                $and[] = ['lobby2.key' => ['squad_2', 'squad_4']];
                                break;

                            default:
                                $and[] = ['lobby2.key' => $parts[0]];
                                break;
                        }
                        $and[] = ['mode2.key' => $parts[1]];
                        switch ($parts[2]) {
                            case 'any':
                                break;

                            case 'gachi':
                                $and[] = ['rule2.key' => ['area', 'yagura', 'hoko', 'asari']];
                                break;

                            default:
                                $and[] = ['rule2.key' => $parts[2]];
                                break;
                        }
                    }
                    // }}}
                }
                if ($form->map != '') {
                    if ($form->map !== 'mystery') {
                        $this->innerJoinWith(['map']);
                        $and[] = ['{{map2}}.[[key]]' => (string)$form->map];
                    } else {
                        $and[] = [
                            '{{battle2}}.[[map_id]]' => ArrayHelper::getColumn(
                                Map2::find()
                                    ->andWhere(['like', 'key', 'mystery%', false])
                                    ->asArray()
                                    ->all(),
                                'id',
                            ),
                        ];
                    }
                }
                if ($form->weapon != '') {
                    // {{{
                    switch (substr($form->weapon, 0, 1)) {
                        default:
                            $weapon = Weapon2::findOne(['key' => $form->weapon]);
                            $and[] = ['battle2.weapon_id' => $weapon->id];
                            break;

                        case '@': // type
                            $this->innerJoinWith('weapon');
                            $type = WeaponType2::findOne(['key' => substr($form->weapon, 1)]);
                            $and[] = ['weapon2.type_id' => $type->id];
                            break;

                        case '~': // main weapon
                            $this->innerJoinWith('weapon');
                            $main = Weapon2::findOne(['key' => substr($form->weapon, 1)]);
                            $and[] = ['weapon2.main_group_id' => $main->id];
                            break;

                        case '+': // sub weapon
                            $this->innerJoinWith('weapon');
                            $sub = SubWeapon2::findOne(['key' => substr($form->weapon, 1)]);
                            $and[] = ['weapon2.subweapon_id' => $sub->id];
                            break;

                        case '*': // special
                            $this->innerJoinWith('weapon');
                            $sp = Special2::findOne(['key' => substr($form->weapon, 1)]);
                            $and[] = ['weapon2.special_id' => $sp->id];
                            break;
                    }
                    // }}}
                }
                if ($form->rank != '') {
                    $this->innerJoinWith(['rank', 'rank.group']);
                    if (substr($form->rank, 0, 1) === '~') { // group
                        $and[] = ['rank_group2.key' => substr($form->rank, 1)];
                    } else {
                        $and[] = ['rank2.key' => $form->rank];
                    }
                }
                if ($form->result != '' || is_bool($form->result)) {
                    $and[] = [
                        'battle2.is_win' => ($form->result === 'win' || $form->result === true),
                    ];
                }
                if ($form->has_disconnect != '' || is_bool($form->has_disconnect)) {
                    $value = $form->has_disconnect === 'yes' || $form->has_disconnect === true;
                    $and[] = ['battle2.has_disconnect' => $value];
                }
                if ($form->id_from != '' && $form->id_from > 0) {
                    $and[] = ['>=', 'battle2.id', (int)$form->id_from];
                }
                if ($form->id_to != '' && $form->id_to > 0) {
                    $and[] = ['<=', 'battle2.id', (int)$form->id_to];
                }
                if ($form->filterTeam) {
                    $and[] = ['battle2.my_team_id' => $form->filterTeam];
                }
                if ($form->filterIdRange) {
                    $and[] = ['between',
                        'battle2.id',
                        (int)$form->filterIdRange[0],
                        (int)$form->filterIdRange[1],
                    ];
                }
                if ($form->filterPeriod) {
                    $and[] = ['between',
                        'battle2.period',
                        (int)$form->filterPeriod[0],
                        (int)$form->filterPeriod[1],
                    ];
                }
                if ($form->filterWithPrincipalId) {
                    $this->innerJoinWith('battlePlayersPure');
                    $and[] = ['{{battle_player2}}.[[splatnet_id]]' => $form->filterWithPrincipalId];

                    if (in_array((string)$form->with_team, ['good', 'bad'], true)) {
                        $and[] = [
                            '{{battle_player2}}.[[is_my_team]]' => $form->with_team === 'good',
                        ];
                    }
                }
                if (count($and) > 1) {
                    $this->andWhere($and);
                    $and = ['and'];
                }
                if ($form->term != '') {
                    $this->filterTerm($form->term, [
                        'from' => $form->term_from,
                        'to' => $form->term_to,
                        'filter' => $form,
                        'timeZone' => $form->timezone,
                    ]);
                }

                return $this;
            }

            public function filterTerm(string $term, array $options): self
            {
                // DateTimeZone
                $tz = (function (?string $tzIdent) {
                    if ($tzIdent && ($model = Timezone::findOne(['identifier' => (string)$tzIdent]))) {
                        return new DateTimeZone($model->identifier);
                    }
                    return new DateTimeZone(Yii::$app->timeZone);
                })($options['timeZone'] ?? null);

                // DateTimeImmutable
                $now = (new DateTimeImmutable())
                    ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time())
                    ->setTimezone($tz);
                $currentPeriod = BattleHelper::calcPeriod2($now->getTimestamp());
                $date = sprintf('(CASE %s END)::timestamp with time zone', implode(' ', [
                    'WHEN {{battle2}}.[[start_at]] IS NOT NULL THEN {{battle2}}.[[start_at]]',
                    "WHEN {{battle2}}.[[end_at]] IS NOT NULL THEN {{battle2}}.[[end_at]] - '3 minutes'::interval",
                    'WHEN {{battle2}}.[[period]] IS NOT NULL THEN PERIOD2_TO_TIMESTAMP({{battle2}}.[[period]])',
                    "ELSE {{battle2}}.[[created_at]] - '4 minutes'::interval",
                ]));

                switch ($term) {
                    case 'this-period':
                        $this->andWhere(['battle2.period' => $currentPeriod]);
                        break;

                    case 'last-period':
                        $this->andWhere(['battle2.period' => $currentPeriod - 1]);
                        break;

                    case '24h':
                        $t = $now->sub(new DateInterval('PT24H'));
                        $this->andWhere(
                            ['>', $date, $t->format(DateTime::ATOM)],
                        );
                        break;

                    case 'today':
                        $today = $now->setTime(0, 0, 0);
                        $tomorrow = $today->add(new DateInterval('P1D'));
                        $this->andWhere(['and',
                            ['>=', $date, $today->format(DateTime::ATOM)],
                            ['<', $date, $tomorrow->format(DateTime::ATOM)],
                        ]);
                        break;

                    case 'yesterday':
                        $today = $now->setTime(0, 0, 0);
                        $yesterday = $today->sub(new DateInterval('P1D'));
                        $this->andWhere(['and',
                            ['>=', $date, $yesterday->format(DateTime::ATOM)],
                            ['<', $date, $today->format(DateTime::ATOM)],
                        ]);
                        break;

                    case 'this-month-utc':
                        $utcNow = (new DateTimeImmutable())
                            ->setTimezone(new DateTimeZone('Etc/UTC'))
                            ->setTimestamp($now->getTimestamp());
                        $thisMonth = (new DateTimeImmutable())
                            ->setTimezone(new DateTimeZone('Etc/UTC'))
                            ->setDate($utcNow->format('Y'), $utcNow->format('n'), 1)
                            ->setTime(0, 0, 0);
                        $this->andWhere([
                            'between',
                            'battle2.period',
                            BattleHelper::calcPeriod2($thisMonth->getTimestamp()),
                            BattleHelper::calcPeriod2($now->getTimestamp()),
                        ]);
                        break;

                    case 'last-month-utc':
                        $utcNow = (new DateTimeImmutable())
                            ->setTimezone(new DateTimeZone('Etc/UTC'))
                            ->setTimestamp($now->getTimestamp());

                        $lastMonthPeriod = BattleHelper::calcPeriod2(
                            (new DateTimeImmutable())
                                ->setTimezone(new DateTimeZone('Etc/UTC'))
                                ->setDate($utcNow->format('Y'), $utcNow->format('n') - 1, 1)
                                ->setTime(0, 0, 0)
                                ->getTimestamp(),
                        );

                        $thisMonthPeriod = BattleHelper::calcPeriod2(
                            (new DateTimeImmutable())
                                ->setTimezone(new DateTimeZone('Etc/UTC'))
                                ->setDate($utcNow->format('Y'), $utcNow->format('n'), 1)
                                ->setTime(0, 0, 0)
                                ->getTimestamp(),
                        );

                        $this->andWhere(['and',
                            ['>=', 'battle2.period', $lastMonthPeriod],
                            ['<', 'battle2.period', $thisMonthPeriod],
                        ]);
                        break;

                    case 'this-fest':
                        try {
                            if (!$form = $options['filter']) {
                                throw new Exception();
                            }

                            if (!$user = User::findOne(['screen_name' => $form->screen_name])) {
                                throw new Exception();
                            }

                            if (!$range = BattleHelper::getLastPlayedSplatfestPeriodRange2($user)) {
                                throw new Exception();
                            }

                            $this->andWhere(['between', 'battle2.period', $range[0], $range[1]]);
                        } catch (Throwable $e) {
                            $this->andWhere('0 = 1');
                        }
                        break;

                    case 'term':
                        try {
                            $from = (($options['from'] ?? '') != '')
                                ? (new DateTimeImmutable($options['from']))->setTimezone($tz)
                                : null;
                            $to = (($options['to'] ?? '') != '')
                                ? (new DateTimeImmutable($options['to']))->setTimezone($tz)
                                : null;
                            if ($from) {
                                $this->andWhere(
                                    ['>=', $date, $from->format(DateTime::ATOM)],
                                );
                            }
                            if ($to) {
                                $this->andWhere(
                                    ['<', $date, $to->format(DateTime::ATOM)],
                                );
                            }
                        } catch (Throwable $e) {
                        }
                        break;

                    default:
                        if (
                            isset($options['filter']) &&
                            preg_match('/^last-(\d+)-battles$/', $term, $match)
                        ) {
                            $range = BattleHelper::getNBattlesRange2(
                                $options['filter'],
                                (int)$match[1],
                            );
                            if ($range && $range['min_id'] && $range['max_id']) {
                                $this->andWhere([
                                    'between',
                                    'battle2.id',
                                    (int)$range['min_id'],
                                    (int)$range['max_id'],
                                ]);
                            }
                        } elseif (preg_match('/^last-(\d+)-periods$/', $term, $match)) {
                            $currentPeriod = BattleHelper::calcPeriod2($now->getTimestamp());
                            $this->andWhere([
                                'between',
                                'battle2.period',
                                $currentPeriod - $match[1] + 1,
                                $currentPeriod,
                            ]);
                        } elseif (preg_match('/^~?v\d+/', $term)) {
                            $versions = (function () use ($term) {
                                $query = SplatoonVersion2::find()->asArray();
                                if (substr($term, 0, 1) === '~') {
                                    $query->innerJoinWith('group', false)
                                        ->andWhere([
                                            'splatoon_version_group2.tag' => substr($term, 2),
                                        ]);
                                } else {
                                    $query->andWhere(['tag' => substr($term, 1)]);
                                }
                                return array_map(
                                    fn (array $version): int => $version['id'],
                                    $query->all(),
                                );
                            })();
                            if (!$versions) {
                                $this->andWhere('1 <> 1'); // Always false
                            } else {
                                $this->andWhere(['battle2.version_id' => $versions]);
                            }
                        }
                        break;
                }
                return $this;
            }

            public function getSummary()
            {
                return BattleSummarizer::getSummary2($this);
            }
        };
    }

    public function init()
    {
        parent::init();
        foreach ($this->events() as $event => $handler) {
            $this->on($event, $handler);
        }
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            RemoteAddrBehavior::class,
            RemotePortBehavior::class,
            [
                // end_at の自動登録
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'end_at',
                ],
                'value' => fn ($event) => ($event->sender->end_at)
                        ? $event->sender->end_at
                        : new \app\components\helpers\db\Now(),
            ],
            [
                // client_uuid の格納形式を作成する
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'client_uuid',
                ],
                'value' => function ($event) {
                    $value = $event->sender->client_uuid;
                    return static::createClientUuid($value);
                },
            ],
            [
                // kill ratio
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'kill_ratio',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'kill_ratio',
                ],
                'value' => function ($event) {
                    $kill  = (string)$event->sender->kill;
                    $death = (string)$event->sender->death;
                    if ($kill === '' || $death === '') {
                        return null;
                    }
                    $kill = intval($kill, 10);
                    $death = intval($death, 10);
                    if ($death >= 1) {
                        return round($kill / $death, 2);
                    }
                    return $kill === 0 ? null : 99.99;
                },
            ],
            [
                // kill rate
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'kill_rate',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'kill_rate',
                ],
                'value' => function ($event) {
                    $kill  = (string)$event->sender->kill;
                    $death = (string)$event->sender->death;
                    if ($kill === '' || $death === '') {
                        return null;
                    }
                    $kill = intval($kill, 10);
                    $death = intval($death, 10);
                    return ($kill === 0 && $death === 0) ? null : ($kill * 100 / ($kill + $death));
                },
            ],
            [
                // splatoon version
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'version_id',
                ],
                'value' => function ($event): ?int {
                    $battle = $event->sender;
                    if ($battle->version_id) {
                        return (int)$battle->version_id;
                    }
                    $time = (function () use ($battle): ?int {
                        if (is_string($battle->end_at) && trim($battle->end_at) !== '') {
                            return strtotime($battle->end_at);
                        }
                        if (is_string($battle->created_at) && trim($battle->created_at) !== '') {
                            return strtotime($battle->created_at);
                        }
                        return null;
                    })();
                    if (!is_int($time)) {
                        $time = (int)($_SERVER['REQUEST_TIME'] ?? time());
                    }
                    $version = SplatoonVersion2::findCurrentVersion($time);
                    return $version ? $version->id : null;
                },
            ],
            [
                // Period の設定
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => [ 'period' ],
                ],
                'value' => function ($event) {
                    $datetime = $event->sender->getVirtualStartTime();
                    return BattleHelper::calcPeriod2($datetime->getTimestamp());
                },
            ],
            [
                // 更新時に統計利用フラグを落とす
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['is_automated', 'use_for_entire'],
                ],
                'value' => fn ($event) => false,
            ],
        ];
    }

    public function events()
    {
        return [
            static::EVENT_AFTER_INSERT => function ($event) {
                $this->adjustUserWeapon($this->weapon_id);
                $this->updateUserStats();
            },
            static::EVENT_AFTER_UPDATE => function ($event) {
                if (isset($event->changedAttributes['weapon_id'])) {
                    $this->adjustUserWeapon([
                        $event->changedAttributes['weapon_id'],
                        $this->weapon_id,
                    ]);
                }
                $this->updateUserStats();
            },
            static::EVENT_BEFORE_DELETE => function ($event) {
                $this->deleteRelated();
                $this->adjustUserWeapon($this->getOldAttribute('weapon_id'), $this->id);
                $this->updateUserStats();
            },
        ];
    }

    public static function createClientUuid($value): string
    {
        if (!is_scalar($value)) {
            return Uuid::v4()->formatAsString();
        }
        $value = trim((string)$value);
        if ($value === '') {
            return Uuid::v4()->formatAsString();
        }
        if (
            preg_match(
                '/^\{?[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\}?$/i',
                $value,
            )
        ) {
            return strtolower(trim($value, '{}'));
        }
        return Uuid::v5(static::CLIENT_UUID_NAMESPACE, $value);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'lobby_id', 'mode_id', 'rule_id', 'map_id', 'weapon_id', 'level', 'level_after'], 'integer'],
            [['rank_id', 'rank_exp', 'rank_after_id', 'rank_after_exp', 'rank_in_team', 'kill', 'death'], 'integer'],
            [['max_kill_combo', 'max_kill_streak', 'my_point', 'my_team_point', 'his_team_point'], 'integer'],
            [['my_team_count', 'his_team_count', 'cash', 'cash_after', 'period', 'version_id', 'bonus_id'], 'integer'],
            [['env_id', 'agent_game_version_id', 'agent_id', 'remote_port', 'star_rank'], 'integer'],
            [['kill_or_assist', 'special', 'gender_id', 'fest_title_id', 'fest_title_after_id'], 'integer'],
            [['my_team_fest_theme_id', 'his_team_fest_theme_id', 'species_id', 'special_battle_id'], 'integer'],
            [['my_team_nickname_id', 'his_team_nickname_id'], 'integer'],
            [['rank_exp', 'rank_after_exp'], 'integer', 'min' => 0, 'max' => 50],
            [['fest_exp', 'fest_exp_after'], 'integer', 'min' => 0, 'max' => 999],
            [['splatnet_number'], 'integer', 'min' => 1],
            [['my_team_id', 'his_team_id'], 'string', 'max' => 16],
            [['is_win', 'is_knockout', 'is_automated', 'use_for_entire'], 'boolean'],
            [['has_disconnect'], 'boolean'],
            [['kill_ratio', 'kill_rate', 'my_team_percent', 'his_team_percent'], 'number'],
            [['my_team_color_hue', 'his_team_color_hue', 'note', 'private_note', 'link_url'], 'string'],
            [['ua_variables', 'ua_custom', 'remote_addr'], 'string'],
            [['start_at', 'end_at', 'created_at', 'updated_at'], 'safe'],
            [['my_team_color_rgb', 'his_team_color_rgb'], 'string', 'max' => 6],
            [['agent_game_version_date'], 'string', 'max' => 32],
            [['estimate_gachi_power', 'my_team_estimate_league_point', 'his_team_estimate_league_point'], 'integer',
                'min' => 0,
            ],
            [['league_point'], 'number', 'min' => 0],
            [['fest_power'], 'number', 'min' => 0],
            [['my_team_estimate_fest_power', 'his_team_estimate_fest_power'], 'integer', 'min' => 0],
            [['x_power', 'x_power_after'], 'number', 'min' => 0],
            [['estimate_x_power'], 'integer', 'min' => 0],
            [['clout', 'total_clout', 'total_clout_after'], 'integer', 'min' => 0],
            [['my_team_win_streak', 'his_team_win_streak'], 'integer', 'min' => 0],
            [['synergy_bonus'], 'number', 'min' => 1.0, 'max' => 9.9],
            [['freshness'], 'number', 'min' => 0.0, 'max' => 99.9],
            [['client_uuid'], 'string'],
            [['client_uuid'], 'match',
                'pattern' => '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            ],
            [['agent_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Agent::class,
                'targetAttribute' => ['agent_id' => 'id'],
            ],
            [['env_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Environment::class,
                'targetAttribute' => ['env_id' => 'id'],
            ],
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
            [['rank_after_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rank2::class,
                'targetAttribute' => ['rank_after_id' => 'id'],
            ],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['version_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SplatoonVersion2::class,
                'targetAttribute' => ['version_id' => 'id'],
            ],
            [['agent_game_version_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SplatoonVersion2::class,
                'targetAttribute' => ['agent_game_version_id' => 'id'],
            ],
            [['bonus_id'], 'exist', 'skipOnError' => true,
                'targetClass' => TurfwarWinBonus2::class,
                'targetAttribute' => ['bonus_id' => 'id'],
            ],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
            [['species_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Species2::class,
                'targetAttribute' => ['species_id' => 'id'],
            ],
            [['gender_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Gender::class,
                'targetAttribute' => ['gender_id' => 'id'],
            ],
            [['fest_title_id', 'fest_title_after_id'], 'exist', 'skipOnError' => true,
                'targetClass' => FestTitle::class,
                'targetAttribute' => 'id',
            ],
            [['my_team_fest_theme_id', 'his_team_fest_theme_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Splatfest2Theme::class,
                'targetAttribute' => 'id',
            ],
            [['special_battle_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SpecialBattle2::class,
                'targetAttribute' => 'id',
            ],
            [['my_team_nickname_id', 'his_team_nickname_id'], 'exist', 'skipOnError' => true,
                'targetClass' => TeamNickname2::class,
                'targetAttribute' => 'id',
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
            'user_id' => 'User ID',
            'lobby_id' => 'Lobby ID',
            'mode_id' => 'Mode ID',
            'rule_id' => 'Rule ID',
            'map_id' => Yii::t('app', 'Stage'),
            'weapon_id' => Yii::t('app', 'Weapon'),
            'is_win' => 'Is Win',
            'is_knockout' => 'Is Knockout',
            'level' => Yii::t('app', 'Level'),
            'level_after' => Yii::t('app', 'Level (after the battle)'),
            'star_rank' => 'Star Rank',
            'rank_id' => Yii::t('app', 'Rank'),
            'rank_exp' => 'Rank Exp',
            'rank_after_id' => Yii::t('app', 'Rank (after the battle)'),
            'rank_after_exp' => 'Rank Exp After',
            'rank_in_team' => Yii::t('app', 'Rank in Team'),
            'kill' => Yii::t('app', 'Kills'),
            'death' => Yii::t('app', 'Deaths'),
            'kill_or_assist' => 'Kill or Assist',
            'special' => Yii::t('app', 'Specials'),
            'kill_ratio' => Yii::t('app', 'Kill Ratio'),
            'kill_rate' => Yii::t('app', 'Kill Rate'),
            'max_kill_combo' => Yii::t('app', 'Max Kill Combo'),
            'max_kill_streak' => Yii::t('app', 'Max Kill Streak'),
            'my_point' => 'My Point',
            'my_team_point' => 'My Team Point',
            'his_team_point' => 'His Team Point',
            'my_team_percent' => 'My Team Percent',
            'his_team_percent' => 'His Team Percent',
            'my_team_count' => 'My Team Count',
            'his_team_count' => 'His Team Count',
            'my_team_color_hue' => 'My Team Color Hue',
            'his_team_color_hue' => 'His Team Color Hue',
            'my_team_color_rgb' => 'My Team Color Rgb',
            'his_team_color_rgb' => 'His Team Color Rgb',
            'cash' => 'Cash',
            'cash_after' => 'Cash After',
            'note' => Yii::t('app', 'Note'),
            'private_note' => Yii::t('app', 'Note (private)'),
            'link_url' => Yii::t('app', 'URL related to this battle'),
            'period' => 'Period',
            'version_id' => 'Version ID',
            'bonus_id' => 'Bonus ID',
            'env_id' => 'Env ID',
            'client_uuid' => 'Client Uuid',
            'ua_variables' => Yii::t('app', 'Extra Data'),
            'ua_custom' => 'Ua Custom',
            'agent_game_version_id' => 'Agent Game Version ID',
            'agent_game_version_date' => 'Agent Game Version Date',
            'agent_id' => 'Agent ID',
            'is_automated' => 'Is Automated',
            'use_for_entire' => 'Use For Entire',
            'species_id' => Yii::t('app', 'Species'),
            'gender_id' => 'Gender',
            'fest_title_id' => Yii::t('app', 'Splatfest Title'),
            'fest_exp' => 'Fest Exp',
            'fest_title_after_id' => 'Fest Title (After the battle)',
            'fest_exp_after' => 'Fest Exp (After the battle)',
            'fest_power' => Yii::t('app', 'Splatfest Power'),
            'my_team_estimate_fest_power' => Yii::t('app', 'My team\'s splatfest power'),
            'his_team_estimate_fest_power' => Yii::t('app', 'Their team\'s splatfest power'),
            'remote_addr' => 'Remote Addr',
            'remote_port' => 'Remote Port',
            'start_at' => Yii::t('app', 'Battle Start'),
            'end_at' => Yii::t('app', 'Battle End'),
            'created_at' => Yii::t('app', 'Data Sent'),
            'updated_at' => 'Updated At',
            'estimate_gachi_power' => Yii::t('app', 'Power Level'),
            'league_point' => Yii::t('app', 'League Power'),
            'my_team_estimate_league_point' => Yii::t('app', 'My team\'s league power'),
            'his_team_estimate_league_point' => Yii::t('app', 'Their team\'s league power'),
            'x_power' => Yii::t('app', 'X Power'),
            'x_power_after' => Yii::t('app', 'X Power (After the battle)'),
            'estimate_x_power' => Yii::t('app', 'Estimated X Power'),
            'special_battle_id' => Yii::t('app', 'Special Battle'),
            'my_team_nickname_id' => Yii::t('app', 'My team\'s nickname'),
            'his_team_nickname_id' => Yii::t('app', 'Their team\'s nickname'),
            'clout' => Yii::t('app', 'Clout'),
            'total_clout' => Yii::t('app', 'Total Clout'),
            'total_clout_after' => Yii::t('app', 'Total Clout (After the battle)'),
            'my_team_win_streak' => Yii::t('app', 'Win streak (Good guys)'),
            'his_team_win_streak' => Yii::t('app', 'Win streak (Bad guys)'),
            'synergy_bonus' => Yii::t('app', 'Synergy Bonus'),
            'freshness' => Yii::t('app', 'Freshness'),
            'freshness_id' => Yii::t('app', 'Freshness'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgent()
    {
        return $this->hasOne(Agent::class, ['id' => 'agent_id']);
    }

    public function getBattleDeathReasons(): ActiveQuery
    {
        return $this->hasMany(BattleDeathReason2::class, ['battle_id' => 'id'])
            ->orderBy([
                'battle_id' => SORT_ASC,
                'reason_id' => SORT_ASC,
            ]);
    }

    public function getBattleImageJudge()
    {
        return $this->hasOne(BattleImage2::class, ['battle_id' => 'id'])
            ->andWhere(['type_id' => BattleImageType::ID_JUDGE]);
    }

    public function getBattleImageResult()
    {
        return $this->hasOne(BattleImage2::class, ['battle_id' => 'id'])
            ->andWhere(['type_id' => BattleImageType::ID_RESULT]);
    }

    public function getBattleImageGear()
    {
        return $this->hasOne(BattleImage2::class, ['battle_id' => 'id'])
            ->andWhere(['type_id' => BattleImageType::ID_GEAR]);
    }

    public function getBattlePlayers(): ActiveQuery
    {
        $query = $this->hasMany(BattlePlayer2::class, ['battle_id' => 'id'])
            ->with(['species', 'weapon', 'weapon.type', 'weapon.subweapon', 'weapon.special'])
            ->orderBy('id');
        if (($this->rule->key ?? '') === 'nawabari') {
            $query->orderBy('[[point]] DESC NULLS LAST, [[id]] ASC');
        }
        return $query;
    }

    public function getBattlePlayersPure(): ActiveQuery
    {
        return $this->hasMany(BattlePlayer2::class, ['battle_id' => 'id']);
    }

    public function getMyTeamPlayers(): ActiveQuery
    {
        return $this->getBattlePlayers()
            ->andWhere(['{{battle_player2}}.[[is_my_team]]' => true]);
    }

    public function getHisTeamPlayers(): ActiveQuery
    {
        return $this->getBattlePlayers()
            ->andWhere(['{{battle_player2}}.[[is_my_team]]' => false]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnv(): ActiveQuery
    {
        return $this->hasOne(Environment::class, ['id' => 'env_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents(): ActiveQuery
    {
        return $this->hasOne(BattleEvents2::class, ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLobby(): ActiveQuery
    {
        return $this->hasOne(Lobby2::class, ['id' => 'lobby_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap(): ActiveQuery
    {
        return $this->hasOne(Map2::class, ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMode(): ActiveQuery
    {
        return $this->hasOne(Mode2::class, ['id' => 'mode_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRank(): ActiveQuery
    {
        return $this->hasOne(Rank2::class, ['id' => 'rank_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRankAfter(): ActiveQuery
    {
        return $this->hasOne(Rank2::class, ['id' => 'rank_after_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVersion(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersion2::class, ['id' => 'version_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgentGameVersion(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersion2::class, ['id' => 'agent_game_version_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBonus(): ActiveQuery
    {
        return $this->hasOne(TurfwarWinBonus2::class, ['id' => 'bonus_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSplatnetJson(): ActiveQuery
    {
        return $this->hasOne(Battle2Splatnet::class, ['id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }

    public function getSpecies(): ActiveQuery
    {
        return $this->hasOne(Species2::class, ['id' => 'species_id']);
    }

    public function getGender(): ActiveQuery
    {
        return $this->hasOne(Gender::class, ['id' => 'gender_id']);
    }

    public function getFestTitle(): ActiveQuery
    {
        return $this->hasOne(FestTitle::class, ['id' => 'fest_title_id']);
    }

    public function getFestTitleAfter(): ActiveQuery
    {
        return $this->hasOne(FestTitle::class, ['id' => 'fest_title_after_id']);
    }

    public function getHeadgear(): ActiveQuery
    {
        return $this->hasOne(GearConfiguration2::class, ['id' => 'headgear_id']);
    }

    public function getClothing(): ActiveQuery
    {
        return $this->hasOne(GearConfiguration2::class, ['id' => 'clothing_id']);
    }

    public function getShoes(): ActiveQuery
    {
        return $this->hasOne(GearConfiguration2::class, ['id' => 'shoes_id']);
    }

    public function getMyTeamFestTheme(): ActiveQuery
    {
        return $this->hasOne(Splatfest2Theme::class, ['id' => 'my_team_fest_theme_id']);
    }

    public function getHisTeamFestTheme(): ActiveQuery
    {
        return $this->hasOne(Splatfest2Theme::class, ['id' => 'his_team_fest_theme_id']);
    }

    public function getSpecialBattle(): ActiveQuery
    {
        return $this->hasOne(SpecialBattle2::class, ['id' => 'special_battle_id']);
    }

    public function getMyTeamNickname(): ActiveQuery
    {
        return $this->hasOne(TeamNickname2::class, ['id' => 'my_team_nickname_id']);
    }

    public function getHisTeamNickname(): ActiveQuery
    {
        return $this->hasOne(TeamNickname2::class, ['id' => 'his_team_nickname_id']);
    }

    // Call $query->withFreshness() to use this
    public function getFreshnessModel(): ActiveQuery
    {
        return $this->hasOne(Freshness2::class, ['id' => 'freshness_id']);
    }

    public function getIsMeaningful(): bool
    {
        $props = [
            'rule_id', 'map_id', 'weapon_id', 'is_win', 'rank_in_team',
            'kill', 'death', 'kill_or_assist', 'special',
        ];
        foreach ($props as $prop) {
            if ($this->$prop !== null && $this->$prop !== '') {
                return true;
            }
        }
        return false;
    }

    public function getPreviousBattle(): ActiveQuery
    {
        return $this->hasOne(static::class, ['user_id' => 'user_id'])
            ->andWhere(['<', 'id', $this->id])
            ->orderBy('id DESC')
            ->limit(1);
    }

    public function getNextBattle(): ActiveQuery
    {
        return $this->hasOne(static::class, ['user_id' => 'user_id'])
            ->andWhere(['>', 'id', $this->id])
            ->orderBy('id ASC')
            ->limit(1);
    }

    public function getExtraData(): array
    {
        $json = $this->ua_variables;
        if ($json == '') {
            return [];
        }
        try {
            return (function () use ($json): array {
                $decoded = Json::decode($json);
                if (!$decoded) {
                    return [];
                }
                $ret = [];
                foreach ($decoded as $key => $value) {
                    $key = str_replace('_', ' ', $key);
                    $key = ucwords($key);
                    $ret[$key] = $value;
                }
                ksort($ret, SORT_STRING);
                return $ret;
            })();
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getInked(): ?int
    {
        if ($this->is_win === null || $this->my_point === null || $this->rule === null) {
            return null;
        }
        if (!$this->is_win) {
            return $this->my_point;
        }
        if ($this->rule->key === 'nawabari') {
            return ($this->my_point < 1000) ? null : ($this->my_point - 1000);
        }
        return $this->my_point;
    }

    public function getElapsedTime(): ?int
    {
        if ($this->rule && $this->rule->key === 'nawabari') {
            return 180;
        }
        if ($this->start_at === null || $this->end_at === null) {
            return null;
        }
        $s = @strtotime($this->start_at);
        $e = @strtotime($this->end_at);
        if ($s === false || $e === false || $e - $s < 1) {
            return null;
        }
        return $e - $s;
    }

    public function getCreatedAt(): int
    {
        return strtotime($this->created_at);
    }

    public function getIsNawabari(): bool
    {
        return $this->getIsThisGameMode('regular');
    }

    public function getIsGachi(): bool
    {
        return $this->getIsThisGameMode('gachi') ||
            in_array($this->rule->key ?? null, ['area', 'yagura', 'hoko', 'asari'], true);
    }

    private function getIsThisGameMode(string $key): bool
    {
        return ($this->mode->key ?? null) === $key;
    }

    public function getVirtualStartTime(): DateTimeImmutable
    {
        if ($this->start_at) {
            return new DateTimeImmutable($this->start_at);
        }
        if ($this->end_at) {
            return (new DateTimeImmutable($this->end_at))
                ->sub(new DateInterval('PT3M'));
        }
        return (new DateTimeImmutable($this->created_at))
            ->sub(new DateInterval('PT3M15S'));
    }

    public function getMyTeamIcon(string $ext = 'svg'): ?string
    {
        return static::teamIcon($this->my_team_id, $ext);
    }

    public function getHisTeamIcon(string $ext = 'svg'): ?string
    {
        return static::teamIcon($this->his_team_id, $ext);
    }

    public static function teamIcon(?string $id, string $ext = 'svg'): ?string
    {
        $id = trim((string)$id);
        if ($id === '') {
            return null;
        }
        $hash = substr(
            hash('sha256', $id, false),
            0,
            40,
        );
        return Yii::getAlias('@jdenticon') . '/' . $hash . '.' . $ext;
    }

    public function toJsonArray(array $skips = []): array
    {
        $events = null;
        if ($this->events && !in_array('events', $skips, true)) {
            if ($tmp = $this->events->events ?? null) {
                if (is_array($tmp)) {
                    $events = $tmp;
                } elseif (is_string($tmp)) {
                    $events = Json::decode($tmp);
                }
            }

            if (is_array($events)) {
                usort($events, fn (array $a, array $b): int => ($a['at'] ?? null) <=> ($b['at'] ?? null));
            } else {
                $events = null;
            }
        }

        $splatnetJson = null;
        if ($this->splatnetJson && !in_array('splatnet_json', $skips, true)) {
            if ($tmp = $this->splatnetJson->json ?? null) {
                if (is_object($tmp)) {
                    $splatnetJson = $tmp;
                } elseif (is_array($tmp) && ArrayHelper::isAssociative($tmp)) {
                    $splatnetJson = (object)$tmp;
                } elseif (is_string($tmp)) {
                    $splatnetJson = Json::decode($tmp, false);
                }
                unset($tmp);
            }
        }

        return [
            'id' => $this->id,
            // 'uuid' => $this->client_uuid,
            'splatnet_number' => $this->splatnet_number,
            'url' => Url::to([
                'show-v2/battle',
                'screen_name' => $this->user->screen_name,
                'battle' => $this->id,
            ], true),
            'user' => !in_array('user', $skips, true) && $this->user
                ? $this->user->toJsonArray()
                : null,
            'lobby' => $this->lobby ? $this->lobby->toJsonArray() : null,
            'mode' => $this->mode ? $this->mode->toJsonArray(false) : null,
            'rule' => $this->rule ? $this->rule->toJsonArray() : null,
            'map' => $this->map ? $this->map->toJsonArray() : null,
            'weapon' => $this->weapon ? $this->weapon->toJsonArray() : null,
            'freshness' => $this->freshness
                ? [
                    'freshness' => floatval($this->freshness),
                    'title' => $this->freshnessModel
                        ? $this->freshnessModel->toJsonArray()
                        : null,
                ]
                : null,
            'rank' => $this->rank ? $this->rank->toJsonArray() : null,
            'rank_exp' => $this->rank_exp,
            'rank_after' => $this->rankAfter ? $this->rankAfter->toJsonArray() : null,
            'rank_exp_after' => $this->rank_after_exp,
            'x_power' => $this->x_power,
            'x_power_after' => $this->x_power_after,
            'estimate_x_power' => $this->estimate_x_power,
            'level' => $this->level,
            'level_after' => $this->level_after,
            'star_rank' => $this->star_rank,
            //'cash' => $this->cash,
            //'cash_after' => $this->cash_after,
            'result' => $this->is_win === true ? 'win' : ($this->is_win === false ? 'lose' : null),
            'knock_out' => $this->is_knockout,
            'rank_in_team' => $this->rank_in_team,
            'kill' => $this->kill,
            'death' => $this->death,
            'kill_or_assist' => $this->kill_or_assist,
            'special' => $this->special,
            'kill_ratio' => isset($this->kill_ratio) ? floatval($this->kill_ratio) : null,
            'kill_rate' => isset($this->kill_rate) ? floatval($this->kill_rate) / 100 : null,
            'max_kill_combo' => $this->max_kill_combo,
            'max_kill_streak' => $this->max_kill_streak,
            'death_reasons' => in_array('death_reasons', $skips, true)
                ? null
                : array_map(
                    fn ($model) => $model->toJsonArray(),
                    $this->battleDeathReasons,
                ),
            'my_point' => $this->my_point,
            'estimate_gachi_power' => $this->estimate_gachi_power,
            'league_point' => $this->league_point,
            'my_team_estimate_league_point' => $this->my_team_estimate_league_point,
            'his_team_estimate_league_point' => $this->his_team_estimate_league_point,
            'my_team_point' => $this->my_team_point,
            'his_team_point' => $this->his_team_point,
            'my_team_percent' => $this->my_team_percent,
            'his_team_percent' => $this->his_team_percent,
            'my_team_count' => $this->my_team_count,
            'his_team_count' => $this->his_team_count,
            // 'my_team_color' => [
            //     'hue' => $this->my_team_color_hue,
            //     'rgb' => $this->my_team_color_rgb,
            // ],
            // 'his_team_color' => [
            //     'hue' => $this->his_team_color_hue,
            //     'rgb' => $this->his_team_color_rgb,
            // ],
            'my_team_id' => $this->my_team_id,
            'his_team_id' => $this->his_team_id,
            'species' => $this->species ? $this->species->toJsonArray() : null,
            'gender' => $this->gender ? $this->gender->toJsonArray() : null,
            'fest_title' => $this->festTitle
                ? $this->festTitle->toJsonArray(
                    $this->gender,
                    $this->my_team_fest_theme_id ? $this->myTeamFestTheme->name : null,
                )
                : null,
            'fest_exp' => $this->fest_exp,
            'fest_title_after' => $this->festTitleAfter
                ? $this->festTitleAfter->toJsonArray(
                    $this->gender,
                    $this->my_team_fest_theme_id ? $this->myTeamFestTheme->name : null,
                )
                : null,
            'fest_exp_after' => $this->fest_exp_after,
            'fest_power' => $this->fest_power,
            'my_team_estimate_fest_power' => $this->my_team_estimate_fest_power,
            'his_team_my_team_estimate_fest_power' => $this->his_team_estimate_fest_power,
            'my_team_fest_theme' => $this->my_team_fest_theme_id
                ? $this->myTeamFestTheme->name
                : null,
            'his_team_fest_theme' => $this->his_team_fest_theme_id
                ? $this->hisTeamFestTheme->name
                : null,
            'my_team_nickname' => $this->my_team_nickname_id
                ? $this->myTeamNickname->name
                : null,
            'his_team_nickname' => $this->his_team_nickname_id
                ? $this->hisTeamNickname->name
                : null,
            'clout' => $this->clout,
            'total_clout' => $this->total_clout,
            'total_clout_after' => $this->total_clout_after,
            'my_team_win_streak' => $this->my_team_win_streak,
            'his_team_win_streak' => $this->his_team_win_streak,
            'synergy_bonus' => ($this->synergy_bonus === null)
                ? null
                : new JsExpression(sprintf('%.1f', $this->synergy_bonus)),
            'special_battle' => $this->special_battle_id
                ? $this->specialBattle->toJsonArray()
                : null,
            'image_judge' => $this->battleImageJudge
                ? Url::to(
                    Yii::getAlias('@imageurl') . '/' . $this->battleImageJudge->filename,
                    true,
                )
                : null,
            'image_result' => $this->battleImageResult
                ? Url::to(
                    Yii::getAlias('@imageurl') . '/' . $this->battleImageResult->filename,
                    true,
                )
                : null,
            'image_gear' => $this->battleImageGear
                ? Url::to(
                    Yii::getAlias('@imageurl') . '/' . $this->battleImageGear->filename,
                    true,
                )
                : null,
            'gears' => in_array('gears', $skips, true)
                ? null
                : [
                    'headgear' => $this->headgear_id ? $this->headgear->toJsonArray() : null,
                    'clothing' => $this->clothing_id ? $this->clothing->toJsonArray() : null,
                    'shoes'    => $this->shoes_id ? $this->shoes->toJsonArray() : null,
                ],
            'period' => $this->period,
            'period_range' => (function () {
                if (!$this->period) {
                    return null;
                }
                list($from, $to) = BattleHelper::periodToRange2($this->period);
                return sprintf(
                    '%s/%s',
                    gmdate(DateTime::ATOM, $from),
                    gmdate(DateTime::ATOM, $to),
                );
            })(),
            'players' => (in_array('players', $skips, true) || count($this->battlePlayers) === 0)
                ? null
                : array_map(
                    fn ($model) => $model->toJsonArray($this),
                    $this->battlePlayers,
                ),
            'events' => $events,
            'splatnet_json' => $splatnetJson,
            'agent' => [
                'name' => $this->agent ? $this->agent->name : null,
                'version' => $this->agent ? $this->agent->version : null,
                'game_version' => $this->agentGameVersion->name ?? null,
                'game_version_date' => $this->agent_game_version_date,
                'custom' => $this->ua_custom,
                'variables' => $this->ua_variables
                    ? (is_string($this->ua_variables)
                        ? Json::decode($this->ua_variables, false)
                        : $this->ua_variables
                    )
                    : null,
            ],
            'automated' => !!$this->is_automated,
            'environment' => $this->env ? $this->env->text : null,
            'link_url' => ((string)$this->link_url !== '') ? $this->link_url : null,
            'note' => ((string)$this->note !== '') ? $this->note : null,
            'game_version' => $this->version ? $this->version->name : null,
            'nawabari_bonus' => (($this->rule->key ?? null) === 'nawabari')
                ? 1000
                : null,
            'start_at' => $this->start_at != ''
                ? DateTimeFormatter::unixTimeToJsonArray(strtotime($this->start_at))
                : null,
            'end_at' => $this->end_at != ''
                ? DateTimeFormatter::unixTimeToJsonArray(strtotime($this->end_at))
                : null,
            'register_at' => DateTimeFormatter::unixTimeToJsonArray(strtotime($this->created_at)),
        ];
    }

    public function toIkaLogCsv(): array
    {
        // https://github.com/hasegaw/IkaLog/blob/b2e3f3f1315719ad42837ffdb2362680ae09a5dc/ikalog/outputs/csv.py#L130
        // t_unix, t_str, map, rule, won

        // t_str = t.strftime("%Y,%m,%d,%H,%M")
        // t_str を埋め込むときはそれぞれ別フィールドになるようにする（"" でくくって一つにしたりしない）
        $t = strtotime($this->end_at ?: $this->at);
        return [
            (string)$t,
            date('Y', $t),
            date('m', $t),
            date('d', $t),
            date('H', $t),
            date('i', $t),
            $this->map ? Yii::t('app-map2', $this->map->name) : '?',
            $this->rule ? Yii::t('app-rule2', $this->rule->name) : '?',
            $this->is_win === true
                ? Yii::t('app', 'Win')
                : ($this->is_win === false
                    ? Yii::t('app', 'Lose')
                    : Yii::t('app', 'Unknown')
                ),
        ];
    }

    public function toCsvArray(): array
    {
        $t = strtotime($this->end_at ?: $this->at);
        $mode = (function (): string {
            if ($this->lobby && $this->lobby->key === 'private') {
                return 'Private Battle';
            }
            if (!$this->mode) {
                return '';
            }
            switch ($this->mode->key) {
                case 'private':
                    return 'Private Battle';

                case 'gachi':
                    if (!$this->lobby) {
                        return 'Ranked Battle';
                    }
                    switch ($this->lobby->key) {
                        case 'squad_2':
                            return 'League Battle (Twin)';

                        case 'squad_4':
                            return 'League Battle (Quad)';
                    }
                    return 'Ranked Battle';

                case 'fest':
                    if ($this->lobby && $this->lobby === 'squad_4') {
                        return 'Splatfest (Team)';
                    }
                    return 'Splatfest';

                default:
                    return $this->mode->name;
            }
            return '';
        })();

        return [
            $t,
            date('Y/m/d H:i:s', $t),
            Yii::t('app-rule2', $mode),
            $this->rule ? Yii::t('app-rule2', $this->rule->name) : '',
            $this->map ? Yii::t('app-map2', $this->map->name) : '',
            $this->weapon ? Yii::t('app-weapon2', $this->weapon->name) : '',
            $this->is_win === null ? '' : Yii::t('app', $this->is_win ? 'Win' : 'Lose'),
            $this->is_knockout === null
                ? ''
                : Yii::t('app', $this->is_knockout ? 'Knockout' : 'Time is up'),
            $this->my_team_id,
            $this->rank
                ? trim(sprintf(
                    '%s %s',
                    Yii::t('app-rank2', $this->rank->name),
                    $this->rank_exp ?? '',
                ))
                : '',
            $this->rankAfter
                ? trim(sprintf(
                    '%s %s',
                    Yii::t('app-rank2', $this->rankAfter->name),
                    $this->rank_after_exp ?? '',
                ))
                : '',
            $this->estimate_gachi_power,
            $this->league_point,
            $this->level,
            $this->kill,
            $this->death,
            $this->kill_or_assist,
            $this->special,
            $this->inked,
            $this->x_power,
            $this->x_power_after,
        ];
    }

    public function getPrettyMode()
    {
        $key = implode('-', [
            $this->lobby->key ?? '?',
            $this->mode->key ?? '?',
            $this->rule->key ?? '?',
        ]);

        switch ($key) {
            case 'standard-regular-nawabari':
                return Yii::t('app-rule2', 'Turf War - Regular Battle');
            case 'standard-gachi-area':
                return Yii::t('app-rule2', 'Splat Zones - Ranked Battle');
            case 'standard-gachi-yagura':
                return Yii::t('app-rule2', 'Tower Control - Ranked Battle');
            case 'standard-gachi-hoko':
                return Yii::t('app-rule2', 'Rainmaker - Ranked Battle');
            case 'standard-gachi-asari':
                return Yii::t('app-rule2', 'Clam Blitz - Ranked Battle');
            case 'squad_2-gachi-area':
                return Yii::t('app-rule2', 'Splat Zones - League Battle (Twin)');
            case 'squad_2-gachi-yagura':
                return Yii::t('app-rule2', 'Tower Control - League Battle (Twin)');
            case 'squad_2-gachi-hoko':
                return Yii::t('app-rule2', 'Rainmaker - League Battle (Twin)');
            case 'squad_2-gachi-asari':
                return Yii::t('app-rule2', 'Clam Blitz - League Battle (Twin)');
            case 'squad_4-gachi-area':
                return Yii::t('app-rule2', 'Splat Zones - League Battle (Quad)');
            case 'squad_4-gachi-yagura':
                return Yii::t('app-rule2', 'Tower Control - League Battle (Quad)');
            case 'squad_4-gachi-hoko':
                return Yii::t('app-rule2', 'Rainmaker - League Battle (Quad)');
            case 'squad_4-gachi-asari':
                return Yii::t('app-rule2', 'Clam Blitz - League Battle (Quad)');
            case 'standard-fest-nawabari':
                if ($this->version) {
                    if (version_compare($this->version->tag, '4.0.0', '<')) {
                        return Yii::t('app-rule2', 'Turf War - Splatfest (Solo)');
                    } else {
                        return Yii::t('app-rule2', 'Turf War - Splatfest (Pro)');
                    }
                }
                return Yii::t('app-rule2', 'Turf War - Splatfest (Pro/Solo)');
            case 'fest_normal-fest-nawabari':
                return Yii::t('app-rule2', 'Turf War - Splatfest (Normal)');
            case 'squad_4-fest-nawabari':
                return Yii::t('app-rule2', 'Turf War - Splatfest (Team)');
            case 'private-private-nawabari':
                return Yii::t('app-rule2', 'Turf War - Private Battle');
            case 'private-private-area':
                return Yii::t('app-rule2', 'Splat Zones - Private Battle');
            case 'private-private-yagura':
                return Yii::t('app-rule2', 'Tower Control - Private Battle');
            case 'private-private-hoko':
                return Yii::t('app-rule2', 'Rainmaker - Private Battle');
            case 'private-private-asari':
                return Yii::t('app-rule2', 'Clam Blitz - Private Battle');
        }
        return null;
    }

    public function getHasDisconnectedPlayer(): bool
    {
        return (bool)$this->has_disconnect;
    }

    public function getPrivateRoomId(): ?string
    {
        return $this->getPrivateTeamId($this->battlePlayersPure);
    }

    public function getPrivateMyTeamId(): ?string
    {
        return $this->getPrivateTeamId(array_filter(
            $this->battlePlayersPure,
            fn ($model): bool => $model->is_my_team === true,
        ));
    }

    public function getPrivateHisTeamId(): ?string
    {
        return $this->getPrivateTeamId(array_filter(
            $this->battlePlayersPure,
            fn ($model): bool => $model->is_my_team === false,
        ));
    }

    private function getPrivateTeamId(array $players): ?string
    {
        if (!$this->lobby || $this->lobby->key !== 'private') {
            return null;
        }

        $playerIds = array_map(
            function ($player): ?string {
                $id =  trim($player->splatnet_id);
                return preg_match('/^[0-9a-f]{16}$/u', $id)
                    ? $id
                    : null;
            },
            $players,
        );
        if (
            count($playerIds) < 1 ||
            count($playerIds) > 8 ||
            in_array(null, $playerIds, true)
        ) {
            return null;
        }

        sort($playerIds);

        return hash('sha256', implode('&', $playerIds));
    }

    public function getAssist(): ?int
    {
        if ($this->kill_or_assist === null || $this->kill === null) {
            return null;
        }

        return $this->kill_or_assist - $this->kill;
    }

    public function getGearAbilitySummary(): ?array
    {
        static $cache = false;
        if ($cache === false) {
            $cache = $this->getGearAbilitySummaryImpl();
        }
        return $cache;
    }

    private function getGearAbilitySummaryImpl(): ?array
    {
        $specials = ArrayHelper::map(
            Special2::find()->all(),
            'key',
            fn (Special2 $model): Special2 => $model,
        );

        $results = [];

        $addAbility = function (
            Ability2 $ability,
            bool $isPrimary,
            bool $haveDoubler
        ) use (&$results): void {
            if ($haveDoubler && $isPrimary) {
                return;
            }

            $key = $ability->key;
            if (!isset($results[$key])) {
                $results[$key] = Yii::createObject([
                    '__class' => Ability2Info::class,
                    'ability' => $ability,
                    'weapon' => $this->weapon,
                    'version' => $this->version,
                ]);
            }
            if ($isPrimary) {
                $results[$key]->primary += 1;
            } else {
                $results[$key]->secondary += ($haveDoubler ? 2 : 1);
            }
        };

        foreach ([$this->headgear, $this->clothing, $this->shoes] as $gearConf) {
            if (!$gearConf || !$gearConf->primaryAbility) {
                return null;
            }

            $haveDoubler = ($gearConf->primaryAbility->key === 'ability_doubler');
            $addAbility($gearConf->primaryAbility, true, $haveDoubler);
            foreach ($gearConf->secondaries as $secondary) {
                if ($secondary->ability) {
                    $addAbility($secondary->ability, false, $haveDoubler);
                }
            }
        }

        // イカニンジャが設定されていれば全アイテムにフラグを立てていく
        if (isset($results['ninja_squid'])) {
            foreach ($results as $info) {
                $info->haveNinja = true;
            }
        }

        uasort($results, function (Ability2Info $a, Ability2Info $b): int {
            // メインにしかつかないやつは後回し
            if ($a->isPrimaryOnly !== $b->isPrimaryOnly) {
                return $a->isPrimaryOnly ? 1 : -1;
            }

            return $b->get57Format() <=> $a->get57Format()
                ?: $b->primary <=> $a->primary
                ?: $b->secondary <=> $b->secondary
                ?: strcmp($a->ability->name, $b->ability->name);
        });

        return $results;
    }

    public function adjustUserWeapon($weaponIds, ?int $excludeBattle = null): void
    {
        $weaponIds = array_unique(array_filter((array)$weaponIds, fn ($value) => $value > 0));
        if (!$weaponIds) {
            return;
        }
        // $list: [weapon_id => attrs, ...] {{{
        $query = (new \yii\db\Query())
            ->select([
                'weapon_id',
                'battles'       => 'COUNT(*)',
                'last_used_at'  => 'MAX(CASE WHEN [[end_at]] IS NOT NULL THEN [[end_at]] ELSE [[created_at]] END)',
            ])
            ->from('battle2')
            ->where([
                'user_id' => $this->user_id,
                'weapon_id' => $weaponIds,
            ])
            ->groupBy('weapon_id');
        if ($excludeBattle) {
            $query->andWhere(['<>', 'id', $excludeBattle]);
        }
        $list = ArrayHelper::map(
            $query->all(),
            'weapon_id',
            fn ($a) => $a,
        );
        // }}}
        foreach ($weaponIds as $weapon_id) {
            if (isset($list[$weapon_id])) {
                if (!$model = UserWeapon2::findOne(['user_id' => $this->user_id, 'weapon_id' => $weapon_id])) {
                    $model = Yii::createObject([
                        'class' => UserWeapon2::class,
                        'user_id' => $this->user_id,
                        'weapon_id' => $weapon_id,
                    ]);
                }
                $model->battles = (int)$list[$weapon_id]['battles'];
                $model->last_used_at = $list[$weapon_id]['last_used_at'];
                $model->save();
            } else {
                if ($model = UserWeapon2::findOne(['user_id' => $this->user_id, 'weapon_id' => $weapon_id])) {
                    $model->delete();
                }
            }
        }
    }

    public function updateUserStats(): void
    {
        Yii::$app->queue
            ->priority(UserStatsJob::getJobPriority())
            ->push(new UserStatsJob([
                'version' => 2,
                'user' => $this->user_id,
            ]));
    }

    public function deleteRelated(): void
    {
        $queries = [
            $this->getBattleDeathReasons(),
            $this->getEvents(),
            $this->getSplatnetJson(),
            $this->hasMany(BattleImage2::class, ['battle_id' => 'id']),
            $this->hasMany(BattlePlayer2::class, ['battle_id' => 'id']),
        ];
        foreach ($queries as $query) {
            foreach ($query->orderBy([])->all() as $model) {
                $model->delete();
            }
        }
    }
}
