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
use Throwable;
use Yii;
use app\components\behaviors\RemoteAddrBehavior;
use app\components\behaviors\RemotePortBehavior;
use app\components\behaviors\TimestampBehavior;
use app\components\helpers\Battle as BattleHelper;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\db\Now;
use app\components\jobs\UserStatsJob;
use app\models\query\Battle2Query;
use jp3cki\uuid\Uuid;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

use const FILTER_VALIDATE_INT;
use const SORT_ASC;
use const SORT_STRING;

/**
 * This is the model class for table "battle2".
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $lobby_id
 * @property int|null $mode_id
 * @property int|null $rule_id
 * @property int|null $map_id
 * @property int|null $weapon_id
 * @property bool|null $is_win
 * @property bool|null $is_knockout
 * @property int|null $level
 * @property int|null $level_after
 * @property int|null $rank_id
 * @property int|null $rank_exp
 * @property int|null $rank_after_id
 * @property int|null $rank_after_exp
 * @property int|null $rank_in_team
 * @property int|null $kill
 * @property int|null $death
 * @property float|null $kill_ratio
 * @property float|null $kill_rate
 * @property int|null $max_kill_combo
 * @property int|null $max_kill_streak
 * @property int|null $my_point
 * @property int|null $my_team_point
 * @property int|null $his_team_point
 * @property float|null $my_team_percent
 * @property float|null $his_team_percent
 * @property int|null $my_team_count
 * @property int|null $his_team_count
 * @property int|null $my_team_color_hue
 * @property int|null $his_team_color_hue
 * @property string|null $my_team_color_rgb
 * @property string|null $his_team_color_rgb
 * @property int|null $cash
 * @property int|null $cash_after
 * @property string|null $note
 * @property string|null $private_note
 * @property string|null $link_url
 * @property int|null $period
 * @property int|null $version_id
 * @property int|null $bonus_id
 * @property int|null $env_id
 * @property string $client_uuid
 * @property string|null $ua_variables
 * @property string|null $ua_custom
 * @property int|null $agent_game_version_id
 * @property string|null $agent_game_version_date
 * @property int|null $agent_id
 * @property bool $is_automated
 * @property bool $use_for_entire
 * @property string|null $remote_addr
 * @property int|null $remote_port
 * @property string|null $start_at
 * @property string|null $end_at
 * @property string $created_at
 * @property string $updated_at
 * @property int|null $kill_or_assist
 * @property int|null $special
 * @property int|null $gender_id
 * @property int|null $fest_title_id
 * @property int|null $fest_exp
 * @property int|null $fest_title_after_id
 * @property int|null $fest_exp_after
 * @property int|null $splatnet_number
 * @property string|null $my_team_id
 * @property string|null $his_team_id
 * @property int|null $estimate_gachi_power
 * @property float|null $league_point
 * @property int|null $my_team_estimate_league_point
 * @property int|null $his_team_estimate_league_point
 * @property float|null $fest_power
 * @property int|null $my_team_estimate_fest_power
 * @property int|null $his_team_estimate_fest_power
 * @property int|null $headgear_id
 * @property int|null $clothing_id
 * @property int|null $shoes_id
 * @property int|null $star_rank
 * @property int|null $my_team_fest_theme_id
 * @property int|null $his_team_fest_theme_id
 * @property float|null $x_power
 * @property float|null $x_power_after
 * @property int|null $estimate_x_power
 * @property int|null $species_id
 * @property int|null $special_battle_id
 * @property int|null $my_team_nickname_id
 * @property int|null $his_team_nickname_id
 * @property int|null $clout
 * @property int|null $total_clout
 * @property int|null $total_clout_after
 * @property float|null $synergy_bonus
 * @property int|null $my_team_win_streak
 * @property int|null $his_team_win_streak
 * @property float|null $freshness
 * @property bool $has_disconnect
 *
 * @property ?Agent $agent
 * @property ?SplatoonVersion2 $agentGameVersion
 * @property ?Battle2Splatnet $battle2Splatnet
 * @property BattleDeathReason2[] $battleDeathReason2s
 * @property ?BattleEvents2 $battleEvents2
 * @property BattleImage2[] $battleImage2s
 * @property BattlePlayer2[] $battlePlayer2s
 * @property ?TurfwarWinBonus2 $bonus
 * @property ?GearConfiguration2 $clothing
 * @property ?Environment $env
 * @property ?FestTitle $festTitle
 * @property ?FestTitle $festTitleAfter
 * @property ?Gender $gender
 * @property ?GearConfiguration2 $headgear
 * @property ?Splatfest2Theme $hisTeamFestTheme
 * @property ?TeamNickname2 $hisTeamNickname
 * @property ?Lobby2 $lobby
 * @property ?Map2 $map
 * @property ?Mode2 $mode
 * @property ?Splatfest2Theme $myTeamFestTheme
 * @property ?TeamNickname2 $myTeamNickname
 * @property ?Rank2 $rank
 * @property ?Rank2 $rankAfter
 * @property DeathReason2[] $reasons
 * @property ?Rule2 $rule
 * @property ?GearConfiguration2 $shoes
 * @property ?SpecialBattle2 $specialBattle
 * @property ?Species2 $species
 * @property BattleImageType[] $types
 * @property ?User $user
 * @property ?SplatoonVersion2 $version
 * @property ?Weapon2 $weapon
 *
 * @property-read Battle2Splatnet|null $splatnetJson
 * @property-read BattleDeathReason2[] $battleDeathReasons
 * @property-read BattleEvents2|null $events
 * @property-read BattleImage2|null $battleImageGear
 * @property-read BattleImage2|null $battleImageJudge
 * @property-read BattleImage2|null $battleImageResult
 * @property-read BattlePlayer2[] $battlePlayers
 * @property-read BattlePlayer2[] $battlePlayersPure
 * @property-read BattlePlayer2[] $hisTeamPlayers
 * @property-read BattlePlayer2[] $myTeamPlayers
 * @property-read Freshness2|null $freshnessModel
 * @property-read bool $isGachi
 * @property-read bool $isMeaningful
 * @property-read bool $isNawabari
 * @property-read int|null $elapsedTime
 * @property-read int|null $inked
 * @property-read self|null $nextBattle
 * @property-read self|null $previousBattle
 */
final class Battle2 extends ActiveRecord
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
                FILTER_VALIDATE_INT
            );
            if (is_int($count)) {
                return $count;
            }
        } catch (Throwable $e) {
        }

        return null;
    }

    public static function find(): Battle2Query
    {
        return new Battle2Query(static::class);
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
                'value' => fn ($event) => $event->sender->end_at
                        ? $event->sender->end_at
                        : new Now(),
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
                    return $kill === 0 && $death === 0 ? null : ($kill * 100 / ($kill + $death));
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
                $value
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

    public function getEnv(): ActiveQuery
    {
        return $this->hasOne(Environment::class, ['id' => 'env_id']);
    }

    public function getEvents(): ActiveQuery
    {
        return $this->hasOne(BattleEvents2::class, ['id' => 'id']);
    }

    public function getLobby(): ActiveQuery
    {
        return $this->hasOne(Lobby2::class, ['id' => 'lobby_id']);
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(Map2::class, ['id' => 'map_id']);
    }

    public function getMode(): ActiveQuery
    {
        return $this->hasOne(Mode2::class, ['id' => 'mode_id']);
    }

    public function getRank(): ActiveQuery
    {
        return $this->hasOne(Rank2::class, ['id' => 'rank_id']);
    }

    public function getRankAfter(): ActiveQuery
    {
        return $this->hasOne(Rank2::class, ['id' => 'rank_after_id']);
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    public function getVersion(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersion2::class, ['id' => 'version_id']);
    }

    public function getAgentGameVersion(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersion2::class, ['id' => 'agent_game_version_id']);
    }

    public function getBonus(): ActiveQuery
    {
        return $this->hasOne(TurfwarWinBonus2::class, ['id' => 'bonus_id']);
    }

    public function getSplatnetJson(): ActiveQuery
    {
        return $this->hasOne(Battle2Splatnet::class, ['id' => 'id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

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
        return $this->hasOne(self::class, ['user_id' => 'user_id'])
            ->andWhere(['<', 'id', $this->id])
            ->orderBy('id DESC')
            ->limit(1);
    }

    public function getNextBattle(): ActiveQuery
    {
        return $this->hasOne(self::class, ['user_id' => 'user_id'])
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
            return $this->my_point < 1000 ? null : $this->my_point - 1000;
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
            40
        );
        return Yii::getAlias('@jdenticon') . '/' . $hash . '.' . $ext;
    }

    public function toJsonArray(array $skips = []): array
    {
        $events = null;
        if ($this->events && !in_array('events', $skips, true)) {
            if ($tmp = $this->events->events ?? null) {
                $events = Json::decode($tmp);
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
                if (is_array($tmp) && ArrayHelper::isAssociative($tmp)) { // @phpstan-ignore-line
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
                    $this->battleDeathReasons
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
                    $this->my_team_fest_theme_id ? $this->myTeamFestTheme->name : null
                )
                : null,
            'fest_exp' => $this->fest_exp,
            'fest_title_after' => $this->festTitleAfter
                ? $this->festTitleAfter->toJsonArray(
                    $this->gender,
                    $this->my_team_fest_theme_id ? $this->myTeamFestTheme->name : null
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
            'synergy_bonus' => $this->synergy_bonus === null
                ? null
                : new JsExpression(sprintf('%.1f', $this->synergy_bonus)),
            'special_battle' => $this->special_battle_id
                ? $this->specialBattle->toJsonArray()
                : null,
            'image_judge' => $this->battleImageJudge
                ? Url::to(
                    Yii::getAlias('@imageurl') . '/' . $this->battleImageJudge->filename,
                    true
                )
                : null,
            'image_result' => $this->battleImageResult
                ? Url::to(
                    Yii::getAlias('@imageurl') . '/' . $this->battleImageResult->filename,
                    true
                )
                : null,
            'image_gear' => $this->battleImageGear
                ? Url::to(
                    Yii::getAlias('@imageurl') . '/' . $this->battleImageGear->filename,
                    true
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
                [$from, $to] = BattleHelper::periodToRange2($this->period);
                return sprintf(
                    '%s/%s',
                    gmdate(DateTime::ATOM, $from),
                    gmdate(DateTime::ATOM, $to)
                );
            })(),
            'players' => in_array('players', $skips, true) || count($this->battlePlayers) === 0
                ? null
                : array_map(
                    fn ($model) => $model->toJsonArray($this),
                    $this->battlePlayers
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
            'link_url' => (string)$this->link_url !== '' ? $this->link_url : null,
            'note' => (string)$this->note !== '' ? $this->note : null,
            'game_version' => $this->version ? $this->version->name : null,
            'nawabari_bonus' => ($this->rule->key ?? null) === 'nawabari'
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
        $t = strtotime($this->end_at ?: $this->created_at);
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
        $t = strtotime($this->end_at ?: $this->created_at);
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
                    if ($this->lobby && $this->lobby->key === 'squad_4') {
                        return 'Splatfest (Team)';
                    }
                    return 'Splatfest';
            }

            return $this->mode->name;
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
                    $this->rank_exp ?? ''
                ))
                : '',
            $this->rankAfter
                ? trim(sprintf(
                    '%s %s',
                    Yii::t('app-rank2', $this->rankAfter->name),
                    $this->rank_after_exp ?? ''
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
            fn ($model): bool => $model->is_my_team === true
        ));
    }

    public function getPrivateHisTeamId(): ?string
    {
        return $this->getPrivateTeamId(array_filter(
            $this->battlePlayersPure,
            fn ($model): bool => $model->is_my_team === false
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
            $players
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
                    'class' => Ability2Info::class,
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
            if ($a->getIsPrimaryOnly() !== $b->getIsPrimaryOnly()) {
                return $a->getIsPrimaryOnly() ? 1 : -1;
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
        $query = (new Query())
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
            fn ($a) => $a
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
        $this->deleteAllModels(
            BattleDeathReason2::find()
                ->andWhere(['battle_id' => $this->id])
                ->all(),
        );
        $this->deleteAllModels(
            BattleEvents2::find()
                ->andWhere(['id' => $this->id])
                ->all(),
        );
        $this->deleteAllModels(
            Battle2Splatnet::find()
                ->andWhere(['id' => $this->id])
                ->all(),
        );
        $this->deleteAllModels(
            BattleImage2::find()
                ->andWhere(['battle_id' => $this->id])
                ->all(),
        );
        $this->deleteAllModels(
            BattlePlayer2::find()
                ->andWhere(['battle_id' => $this->id])
                ->all(),
        );
    }

    /**
     * @param ActiveRecord[] $list
     */
    private function deleteAllModels(array $list): void
    {
        foreach ($list as $item) {
            $item->delete();
        }
    }
}
