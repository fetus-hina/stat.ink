<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v3;

use Throwable;
use Yii;
use app\components\behaviors\TrimAttributesBehavior;
use app\components\db\Connection;
use app\components\helpers\Battle3Helper;
use app\components\helpers\CriticalSection;
use app\components\helpers\ImageConverter;
use app\components\helpers\UuidRegexp;
use app\components\helpers\db\Now;
use app\components\validators\AgentVersionValidator;
use app\components\validators\Base64Validator;
use app\components\validators\BattleAgentVariable3Validator;
use app\components\validators\BattleImageValidator;
use app\components\validators\BattlePlayer3FormValidator;
use app\components\validators\KeyValidator;
use app\models\Ability3;
use app\models\Battle3;
use app\models\BattleAgentVariable3;
use app\models\BattleImageGear3;
use app\models\BattleImageJudge3;
use app\models\BattleImageResult3;
use app\models\BattleMedal3;
use app\models\BattlePlayer3;
use app\models\BattlePlayerGearPower3;
use app\models\BattleTricolorPlayer3;
use app\models\BattleTricolorPlayerGearPower3;
use app\models\ConchClash3;
use app\models\DragonMatch3;
use app\models\DragonMatch3Alias;
use app\models\Lobby3;
use app\models\Map3;
use app\models\Map3Alias;
use app\models\Medal3;
use app\models\Rank3;
use app\models\Result3;
use app\models\Rule3;
use app\models\Rule3Alias;
use app\models\Splatfest3Theme;
use app\models\TricolorRole3;
use app\models\Weapon3;
use app\models\Weapon3Alias;
use app\models\api\v3\postBattle\AgentVariableTrait;
use app\models\api\v3\postBattle\GameVersionTrait;
use app\models\api\v3\postBattle\PlayerForm;
use app\models\api\v3\postBattle\TypeHelperTrait;
use app\models\api\v3\postBattle\UserAgentTrait;
use jp3cki\uuid\Uuid;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\UploadedFile;

use function array_keys;
use function asort;
use function base64_encode;
use function count;
use function file_get_contents;
use function floor;
use function hash_hmac;
use function in_array;
use function is_array;
use function is_int;
use function is_string;
use function rtrim;
use function strtolower;
use function strtotime;
use function substr;
use function time;
use function trim;
use function vsprintf;

/**
 * @property-read Battle3|null $sameBattle
 * @property-read bool $isTest
 */
final class PostBattleForm extends Model
{
    use AgentVariableTrait;
    use GameVersionTrait;
    use TypeHelperTrait;
    use UserAgentTrait;

    public $test;

    public $uuid;
    public $lobby;
    public $rule;
    public $stage;
    public $weapon;
    public $result;
    public $knockout;
    public $rank_in_team;
    public $kill;
    public $assist;
    public $kill_or_assist;
    public $death;
    public $special;
    public $signal;
    public $inked;
    public $our_team_inked;
    public $their_team_inked;
    public $third_team_inked;
    public $our_team_percent;
    public $their_team_percent;
    public $third_team_percent;
    public $our_team_count;
    public $their_team_count;
    public $level_before;
    public $level_after;
    public $rank_before;
    public $rank_before_s_plus;
    public $rank_before_exp;
    public $rank_after;
    public $rank_after_s_plus;
    public $rank_after_exp;
    public $rank_exp_change;
    public $rank_up_battle;
    public $challenge_win;
    public $challenge_lose;
    public $x_power_before;
    public $x_power_after;
    public $fest_power;
    public $fest_dragon;
    public $conch_clash;
    public $bankara_power_before;
    public $bankara_power_after;
    public $series_weapon_power_before;
    public $series_weapon_power_after;
    public $clout_before;
    public $clout_after;
    public $clout_change;
    public $event;
    public $event_power;
    public $cash_before;
    public $cash_after;
    public $our_team_color;
    public $their_team_color;
    public $third_team_color;
    public $our_team_role;
    public $their_team_role;
    public $third_team_role;
    public $our_team_theme;
    public $their_team_theme;
    public $third_team_theme;
    public $our_team_players;
    public $their_team_players;
    public $third_team_players;
    public $note;
    public $private_note;
    public $link_url;
    public $agent;
    public $agent_version;
    public $automated;
    public $start_at;
    public $end_at;

    /** @var string[] */
    public $medals;

    /** @var array<string, string> */
    public $agent_variables;

    /** @var UploadedFile|string|null */
    public $image_judge;

    /** @var UploadedFile|string|null */
    public $image_result;

    /** @var UploadedFile|string|null */
    public $image_gear;

    public $splatnet_json;

    public ?bool $isCreated = null;

    public function behaviors()
    {
        return [
            [
                'class' => TrimAttributesBehavior::class,
                'targets' => array_keys($this->attributes),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['uuid', 'lobby', 'rule', 'stage', 'weapon', 'result', 'rank_before', 'rank_after', 'note'], 'string'],
            [['private_note', 'link_url', 'agent', 'agent_version', 'event'], 'string'],
            [['our_team_role', 'their_team_role', 'third_team_role'], 'string'],
            [['our_team_theme', 'their_team_theme', 'third_team_theme'], 'string'],
            [['fest_dragon', 'conch_clash'], 'string'],

            [['uuid'], 'match', 'pattern' => UuidRegexp::get(true)],
            [['result'], 'in', 'range' => [
                'draw',
                'exempted_lose',
                'lose',
                'win',
            ],
            ],
            [['link_url'], 'url',
                'validSchemes' => ['http', 'https'],
                'defaultScheme' => null,
                'enableIDN' => false,
            ],
            [['agent'], 'string', 'max' => 64],
            [['agent_version'], 'string', 'max' => 255],
            [['agent', 'agent_version'], 'required',
                'when' => fn () => trim((string)$this->agent) !== '' || trim((string)$this->agent_version) !== '',
            ],
            [['agent_version'], AgentVersionValidator::class,
                'gameVersion' => 'splatoon3',
                'when' => fn () => trim((string)$this->agent) !== '' && trim((string)$this->agent_version) !== '',
            ],
            [['event'], Base64Validator::class],

            [['test', 'knockout', 'automated', 'rank_up_battle'], 'in',
                'range' => ['yes', 'no', true, false],
                'strict' => true,
            ],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['kill', 'assist', 'kill_or_assist', 'death', 'special', 'signal'], 'integer', 'min' => 0, 'max' => 99],
            [['inked'], 'integer', 'min' => 0],
            [['our_team_inked', 'their_team_inked', 'third_team_inked'], 'integer', 'min' => 0],
            [['our_team_percent', 'their_team_percent', 'third_team_percent'], 'number', 'min' => 0, 'max' => 100],
            [['our_team_count', 'their_team_count'], 'integer', 'min' => 0, 'max' => 100],
            [['level_before', 'level_after'], 'integer', 'min' => 1, 'max' => 99],
            [['rank_before_s_plus', 'rank_after_s_plus'], 'integer', 'min' => 0, 'max' => 50],
            [['rank_before_exp', 'rank_after_exp'], 'integer'],
            [['rank_exp_change'], 'integer'],
            [['fest_power', 'event_power', 'x_power_before', 'x_power_after'], 'number', 'min' => 0, 'max' => 99999.9],
            [['bankara_power_before', 'bankara_power_after'], 'number', 'min' => 0, 'max' => 99999.9],
            [['series_weapon_power_before', 'series_weapon_power_after'], 'number', 'min' => 0, 'max' => 99999.9],
            [['clout_before', 'clout_after', 'clout_change'], 'integer', 'min' => 0],
            [['cash_before', 'cash_after'], 'integer', 'min' => 0, 'max' => 9999999],
            [['start_at', 'end_at'], 'integer',
                'min' => strtotime('2022-01-01T00:00:00+00:00'),
                'max' => time() + 3600,
            ],

            [['challenge_win'], 'integer', 'min' => 0, 'max' => 5,
                'when' => fn (self $model): bool => self::boolVal($model->rank_up_battle) !== true ||
                        self::strVal($model->lobby) === 'xmatch',
            ],
            [['challenge_win'], 'integer', 'min' => 0, 'max' => 3,
                'when' => fn (self $model): bool => self::boolVal($model->rank_up_battle) === true &&
                        self::strVal($model->lobby) !== 'xmatch',
            ],
            [['challenge_lose'], 'integer', 'min' => 0, 'max' => 3,
                'when' => fn (self $model): bool => self::strVal($model->lobby) !== 'xmatch',
            ],
            [['challenge_lose'], 'integer', 'min' => 0, 'max' => 5,
                'when' => fn (self $model): bool => self::strVal($model->lobby) === 'xmatch',
            ],

            [['our_team_color', 'their_team_color', 'third_team_color'], 'match',
                'pattern' => '/^[0-9a-f]{6}(?:[0-9a-f]{2})?$/i',
            ],

            [['lobby'], KeyValidator::class, 'modelClass' => Lobby3::class],
            [['rule'], KeyValidator::class,
                'modelClass' => Rule3::class,
                'aliasClass' => Rule3Alias::class,
            ],
            [['stage'], KeyValidator::class,
                'modelClass' => Map3::class,
                'aliasClass' => Map3Alias::class,
            ],
            [['weapon'], KeyValidator::class,
                'modelClass' => Weapon3::class,
                'aliasClass' => Weapon3Alias::class,
            ],
            [['fest_dragon'], KeyValidator::class,
                'modelClass' => DragonMatch3::class,
                'aliasClass' => DragonMatch3Alias::class,
            ],
            [['conch_clash'], KeyValidator::class,
                'modelClass' => ConchClash3::class,
            ],
            [['rank_before', 'rank_after'], KeyValidator::class, 'modelClass' => Rank3::class],
            [['our_team_role', 'their_team_role', 'third_team_role'], KeyValidator::class,
                'modelClass' => TricolorRole3::class,
            ],

            [['our_team_players', 'their_team_players', 'third_team_players'], 'each',
                'message' => '{attribute} must be an array',
                'rule' => Yii::createObject(BattlePlayer3FormValidator::class),
            ],

            [['medals'], 'each',
                'message' => '{attribute} must be an array of strings',
                'rule' => ['string',
                    'min' => 1,
                    'max' => 64,
                    'skipOnEmpty' => false,
                ],
            ],

            [['agent_variables'], BattleAgentVariable3Validator::class],
            [['image_judge', 'image_result', 'image_gear'], BattleImageValidator::class],
            [['splatnet_json'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    public function getSameBattle(): ?Battle3
    {
        if (
            !is_string($this->uuid) ||
            $this->uuid === ''
        ) {
            return null;
        }

        if (!$user = Yii::$app->user->identity) {
            return null;
        }

        return Battle3::find()
            ->where([
                'user_id' => $user->id,
                'client_uuid' => $this->uuid,
                'is_deleted' => false,
            ])
            ->limit(1)
            ->one();
    }

    public function getIsTest(): bool
    {
        return $this->test === 'yes' || $this->test === true;
    }

    /**
     * @return Battle3|bool|null
     */
    public function save()
    {
        if (!$this->validate()) {
            return null;
        }

        if ($this->getIsTest()) {
            return true;
        }

        if (!$lock = CriticalSection::lock($this->getCriticalSectionId(), 60)) {
            $this->addError('_system', 'Failed to get lock. System busy. Try again.');
            return null;
        }

        try {
            if ($model = $this->getSameBattle()) {
                $this->isCreated = false;
                return $model;
            }

            if ($model = $this->saveNewBattleRelation()) {
                $this->isCreated = true;
                return $model;
            }

            return null;
        } finally {
            unset($lock);
        }
    }

    private function getCriticalSectionId(): string
    {
        $values = [
            'class' => self::class,
            'user' => Yii::$app->user->id,
            'version' => 1,
        ];
        asort($values);
        return rtrim(
            base64_encode(
                hash_hmac(
                    'sha256',
                    Json::encode($values),
                    (string)Yii::getAlias('@app'),
                    true,
                ),
            ),
            '=',
        );
    }

    private function saveNewBattleRelation(): ?Battle3
    {
        try {
            $connection = Yii::$app->db;
            if (!$connection instanceof Connection) {
                throw new InvalidConfigException();
            }

            return $connection->transactionEx(function (Connection $connection): ?Battle3 {
                if (!$battle = $this->saveNewBattle()) {
                    return null;
                }

                if (!$this->savePlayers($battle)) {
                    return null;
                }

                if (!$this->saveMedals($battle)) {
                    return null;
                }

                if (!$this->saveAgentVariables($battle)) {
                    return null;
                }

                if (!$this->saveBattleImages($battle)) {
                    return null;
                }

                return $battle;
            });
        } catch (Throwable $e) {
            $this->addError(
                '_system',
                vsprintf('Failed to store your battle (internal error), %s, %s', [
                    $e::class,
                    $e->getMessage(),
                ]),
            );
            return null;
        }
    }

    private function saveNewBattle(): ?Battle3
    {
        $uuid = (string)Uuid::v4();
        $model = Yii::createObject([
            'class' => Battle3::class,
            'uuid' => $uuid,
            'client_uuid' => $this->uuid ?: $uuid,
            'user_id' => Yii::$app->user->id,
            'lobby_id' => self::key2id($this->lobby, Lobby3::class),
            'rule_id' => self::key2id($this->rule, Rule3::class, Rule3Alias::class, 'rule_id'),
            'map_id' => self::key2id($this->stage, Map3::class, Map3Alias::class, 'map_id'),
            'weapon_id' => self::key2id($this->weapon, Weapon3::class, Weapon3Alias::class, 'weapon_id'),
            'result_id' => self::key2id($this->result, Result3::class),
            'is_knockout' => self::boolVal($this->knockout),
            'rank_in_team' => self::intVal($this->rank_in_team),
            'kill' => self::intVal($this->kill),
            'assist' => self::intVal($this->assist),
            'kill_or_assist' => self::intVal($this->kill_or_assist), // あとで確認
            'death' => self::intVal($this->death),
            'special' => self::intVal($this->special),
            'signal' => self::intVal($this->signal),
            'inked' => self::intVal($this->inked),
            'our_team_inked' => self::intVal($this->our_team_inked),
            'their_team_inked' => self::intVal($this->their_team_inked),
            'third_team_inked' => self::intVal($this->third_team_inked),
            'our_team_percent' => self::floatVal($this->our_team_percent),
            'their_team_percent' => self::floatVal($this->their_team_percent),
            'third_team_percent' => self::floatVal($this->third_team_percent),
            'our_team_count' => self::intVal($this->our_team_count),
            'their_team_count' => self::intVal($this->their_team_count),
            'level_before' => self::intVal($this->level_before),
            'level_after' => self::intVal($this->level_after),
            'rank_before_id' => self::key2id($this->rank_before, Rank3::class),
            'rank_before_s_plus' => self::intVal($this->rank_before_s_plus),
            'rank_before_exp' => self::intVal($this->rank_before_exp),
            'rank_after_id' => self::key2id($this->rank_after, Rank3::class),
            'rank_after_s_plus' => self::intVal($this->rank_after_s_plus),
            'rank_after_exp' => self::intVal($this->rank_after_exp),
            'rank_exp_change' => self::intVal($this->rank_exp_change),
            'cash_before' => self::intVal($this->cash_before),
            'cash_after' => self::intVal($this->cash_after),
            'note' => self::strVal($this->note),
            'private_note' => self::strVal($this->private_note),
            'link_url' => self::strVal($this->link_url),
            'version_id' => self::gameVersion(
                self::guessStartAt(
                    self::intVal($this->start_at),
                    self::intVal($this->end_at),
                ),
            ),
            'agent_id' => self::userAgent($this->agent, $this->agent_version),
            'is_automated' => self::boolVal($this->automated) ?: false,
            'use_for_entire' => false, // あとで上書き
            'start_at' => self::tsVal(self::intVal($this->start_at)),
            'end_at' => self::tsVal(self::intVal($this->end_at) ?? time()),
            'period' => self::guessPeriod(self::intVal($this->start_at), self::intVal($this->end_at)),
            'remote_addr' => Yii::$app->request->getUserIP() ?? '127.0.0.2',
            'remote_port' => self::intVal($_SERVER['REMOTE_PORT'] ?? 0),
            'created_at' => self::now(),
            'updated_at' => self::now(),
            'is_deleted' => false,
            'challenge_win' => self::intVal($this->challenge_win),
            'challenge_lose' => self::intVal($this->challenge_lose),
            'is_rank_up_battle' => self::boolVal($this->rank_up_battle),
            'clout_before' => self::intVal($this->clout_before),
            'clout_after' => self::intVal($this->clout_after),
            'clout_change' => self::intVal($this->clout_change),
            'fest_dragon_id' => self::key2id(
                $this->fest_dragon,
                DragonMatch3::class,
                DragonMatch3Alias::class,
                'dragon_id',
            ),
            'fest_power' => self::powerVal($this->fest_power),
            'has_disconnect' => $this->hasDisconnect(),
            'x_power_before' => self::powerVal($this->x_power_before),
            'x_power_after' => self::powerVal($this->x_power_after),
            'our_team_role_id' => self::key2id($this->our_team_role, TricolorRole3::class),
            'their_team_role_id' => self::key2id($this->their_team_role, TricolorRole3::class),
            'third_team_role_id' => self::key2id($this->third_team_role, TricolorRole3::class),
            'our_team_color' => self::colorVal($this->our_team_color),
            'their_team_color' => self::colorVal($this->their_team_color),
            'third_team_color' => self::colorVal($this->third_team_color),
            'our_team_theme_id' => $this->findOrCreateSplatfestTheme(self::strVal($this->our_team_theme))?->id,
            'their_team_theme_id' => $this->findOrCreateSplatfestTheme(self::strVal($this->their_team_theme))?->id,
            'third_team_theme_id' => $this->findOrCreateSplatfestTheme(self::strVal($this->third_team_theme))?->id,
            'bankara_power_before' => self::powerVal($this->bankara_power_before),
            'bankara_power_after' => self::powerVal($this->bankara_power_after),
            'series_weapon_power_before' => self::powerVal($this->series_weapon_power_before),
            'series_weapon_power_after' => self::powerVal($this->series_weapon_power_after),
            'event_id' => $this->lobby === 'event'
                ? self::eventIdVal(
                    $this->event,
                    self::guessStartAt(
                        self::intVal($this->start_at),
                        self::intVal($this->end_at),
                    ),
                )
                : null,
            'event_power' => self::powerVal($this->event_power),
            'conch_clash_id' => self::key2id($this->conch_clash, ConchClash3::class),
        ]);

        // kill+assistが不明でkillとassistがわかっている
        if (
            $model->kill_or_assist === null &&
            is_int($model->kill) &&
            is_int($model->assist)
        ) {
            $model->kill_or_assist = $model->kill + $model->assist;
        }

        // 設定された値から統計に使えそうか雑な判断をする
        $model->use_for_entire = $this->isUsableForEntireStats($model, self::intVal($this->start_at));

        if (!$model->save()) {
            $this->addError('_system', vsprintf('Failed to store new battle, info=%s', [
                base64_encode(Json::encode($model->getFirstErrors())),
            ]));
            return null;
        }

        return $model;
    }

    private function hasDisconnect(): bool
    {
        if (is_array($this->our_team_players) && $this->our_team_players) {
            foreach ($this->our_team_players as $player) {
                $model = Yii::createObject(PlayerForm::class);
                $model->attributes = $player;
                if (self::boolVal($model->disconnected)) {
                    return true;
                }
            }
        }

        if (is_array($this->their_team_players) && $this->their_team_players) {
            foreach ($this->their_team_players as $player) {
                $model = Yii::createObject(PlayerForm::class);
                $model->attributes = $player;
                if (self::boolVal($model->disconnected)) {
                    return true;
                }
            }
        }

        if (is_array($this->third_team_players) && $this->third_team_players) {
            foreach ($this->third_team_players as $player) {
                $model = Yii::createObject(PlayerForm::class);
                $model->attributes = $player;
                if (self::boolVal($model->disconnected)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function savePlayers(Battle3 $battle): bool
    {
        $list = [
            [$this->our_team_players, true, 1],
            [$this->their_team_players, false, 2],
            [$this->third_team_players, false, 3],
        ];
        foreach ($list as [$players, $isOurTeam, $tricolorTeamNumber]) {
            if (!is_array($players) || !$players) {
                continue;
            }

            foreach ($players as $player) {
                if (!$this->savePlayerImpl($battle, $player, $isOurTeam, $tricolorTeamNumber)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function savePlayerImpl(
        Battle3 $battle,
        array $playerData,
        bool $isOurTeam,
        int $tricolorTeamNumber,
    ): bool {
        $form = Yii::createObject(PlayerForm::class);
        $form->attributes = $playerData;
        $model = $form->save($battle, $isOurTeam, $tricolorTeamNumber);
        if (!$model) {
            return false;
        }

        if ($gpData = Battle3Helper::calcGPs($model)) {
            $abilities = ArrayHelper::map(
                Ability3::find()
                    ->andWhere(['key' => array_keys($gpData)])
                    ->asArray()
                    ->all(),
                'key',
                'id',
            );

            foreach ($gpData as $key => $gp) {
                $gpModel = Yii::createObject([
                    'class' => match ($model::class) {
                        BattlePlayer3::class => BattlePlayerGearPower3::class,
                        BattleTricolorPlayer3::class => BattleTricolorPlayerGearPower3::class,
                    },
                    'player_id' => $model->id,
                    'ability_id' => $abilities[$key],
                    'gear_power' => $gp,
                ]);
                if (!$gpModel->save()) {
                    return false;
                }
            }
        }

        return true;
    }

    private function saveMedals(Battle3 $battle): bool
    {
        if (!$list = $this->medals) {
            return true;
        }

        foreach ($list as $medal) {
            $medalModel = $this->findOrCreateMedal($medal);
            if (!$medalModel) {
                return false;
            }

            if (!$this->saveMedal($battle, $medalModel)) {
                return false;
            }
        }

        return true;
    }

    private function saveMedal(Battle3 $battle, Medal3 $medal): bool
    {
        // check duplicated
        $model = BattleMedal3::findOne(['battle_id' => $battle->id, 'medal_id' => $medal->id]);
        if ($model) {
            // dupe
            return true;
        }

        $model = Yii::createObject([
            'class' => BattleMedal3::class,
            'battle_id' => (int)$battle->id,
            'medal_id' => (int)$medal->id,
        ]);
        return (bool)$model->save();
    }

    private function findOrCreateMedal(string $text): ?Medal3
    {
        $text = trim($text);
        if ($text === null) {
            return null;
        }

        // use double-checking lock pattern
        //
        // 1. find data without lock (fast, the data already exists)
        // In most cases, we'll find them here.
        $model = Medal3::findOne(['name' => $text]);
        if (!$model) {
            // 2. lock if not found
            if (!$lock = CriticalSection::lock(Medal3::class, 60)) {
                return null;
            }
            try {
                // 3. find data again with lock (it may created on another process/thread)
                $model = Medal3::findOne(['name' => $text]);
                if (!$model) {
                    // 4. create new data with lock (it's new!)
                    $model = Yii::createObject([
                        'class' => Medal3::class,
                        'name' => $text,
                    ]);
                    if (!$model->save()) {
                        return null;
                    }
                }
            } finally {
                unset($lock);
            }
        }

        return $model;
    }

    private function saveAgentVariables(Battle3 $battle): bool
    {
        $map = $this->agent_variables;
        if (!is_array($map) || !$map) {
            return true;
        }

        foreach ($map as $k => $v) {
            $model = Yii::createObject([
                'class' => BattleAgentVariable3::class,
                'battle_id' => $battle->id,
                // `findOrCreateAgentVariable()` may returns null and it will fail on `save()`
                'variable_id' => $this->findOrCreateAgentVariable($k, $v),
            ]);
            if (!$model->save()) {
                return false;
            }
        }

        return true;
    }

    private function saveBattleImages(Battle3 $battle): bool
    {
        $targets = [
            [
                'modelClass' => BattleImageJudge3::class,
                'attribute' => 'image_judge',
                'filename' => self::generateRandomFilename('jpg'),
                'isResult' => false,
            ],
            [
                'modelClass' => BattleImageResult3::class,
                'attribute' => 'image_result',
                'filename' => self::generateRandomFilename('jpg'),
                'isResult' => true,
            ],
            [
                'modelClass' => BattleImageGear3::class,
                'attribute' => 'image_gear',
                'filename' => self::generateRandomFilename('jpg'),
                'isResult' => false,
            ],
        ];

        foreach ($targets as $target) {
            $attribute = $target['attribute'];
            if ($this->$attribute === null || $this->$attribute === '') {
                continue;
            }

            if (
                !$this->saveBattleImage(
                    $battle,
                    $target['modelClass'],
                    $target['attribute'],
                    $target['filename'],
                    $target['isResult'],
                )
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @phpstan-param class-string<ActiveRecord> $modelClass
     */
    private function saveBattleImage(
        Battle3 $battle,
        string $modelClass,
        string $attribute,
        string $fileName,
        bool $isResultImage,
    ): bool {
        if ($this->$attribute === null || $this->$attribute === '') {
            return true;
        }

        $binary = $this->$attribute instanceof UploadedFile
            ? (string)@file_get_contents($this->$attribute->tempName)
            : $this->$attribute;

        if (
            !ImageConverter::convert(
                $binary,
                vsprintf('%s/%s', [
                    (string)Yii::getAlias('@webroot/images'),
                    $fileName,
                ]),
                false, // TODO: $blackoutPosList => $isResultImage を参照
                null, // $outPathArchivePng
            )
        ) {
            return false;
        }

        $model = Yii::createObject([
            'class' => $modelClass,
            'battle_id' => $battle->id,
            'bucket_id' => 1,
            'filename' => $fileName,
        ]);
        return $model->save();
    }

    private function findOrCreateSplatfestTheme(?string $name): ?Splatfest3Theme
    {
        $name = trim((string)$name);
        if ($name === '') {
            return null;
        }

        $model = Splatfest3Theme::find()
            ->andWhere(['name' => $name])
            ->limit(1)
            ->one();
        if ($model) {
            return $model;
        }

        if (!$lock = CriticalSection::lock(Splatfest3Theme::class, 60)) {
            return null;
        }
        try {
            $model = Splatfest3Theme::find()
                ->andWhere(['name' => $name])
                ->limit(1)
                ->one();
            if ($model) {
                return $model;
            }

            $model = Yii::createObject([
                'class' => Splatfest3Theme::class,
                'name' => $name,
            ]);
            return $model->save() ? $model : null;
        } finally {
            unset($lock);
        }
    }

    private static function now(): Now
    {
        return Yii::createObject(Now::class);
    }

    private static function guessPeriod(?int $startAt, ?int $endAt): int
    {
        return self::timestamp2period(self::guessStartAt($startAt, $endAt));
    }

    private static function timestamp2period(int $ts): int
    {
        return (int)floor($ts / 7200);
    }

    private static function guessStartAt(?int $startAt, ?int $endAt): int
    {
        if (is_int($startAt)) {
            return $startAt;
        }

        if (is_int($endAt)) {
            // Guess the battle started 3 minutes before the end time.
            // It is clear if the battle is Turf War.
            // In other modes, the regulation time is 5 minutes,
            // but 3 minutes would be a reasonable estimate because of knockout possibilities.
            return $endAt - 180;
        }

        // Use 5 minutes before the current time as an estimated value if the time is unknown.
        return time() - 300;
    }

    private function isUsableForEntireStats(Battle3 $model, ?int $startAt): bool
    {
        if (
            !$model->is_automated ||
            !is_int($startAt) ||
            $startAt < time() - 86400 ||
            $startAt > time() ||
            !$model->lobby_id ||
            !$model->rule_id ||
            !$model->map_id ||
            !$model->weapon_id ||
            !$model->result_id
        ) {
            return false;
        }

        if (
            !($lobby = $model->lobby) ||
            !($result = $model->result) ||
            !($rule = $model->rule) ||
            $lobby->key === 'private' ||
            !$result->aggregatable
        ) {
            return false;
        }

        if ($lobby->key === 'event') {
            if (!$event = $model->event) {
                return false;
            }

            // デュオ決定戦は全体が4人、各チーム2人
            if ($event->internal_id === 'TGVhZ3VlTWF0Y2hFdmVudC1QYWlyQ3Vw') {
                return $this->isValidPlayersCount($model, 4, [2]);
            }
        }

        return $this->isValidPlayersCount(
            $model,
            8,
            $lobby->key === 'splatfest_open' && $rule->key === 'tricolor'
                ? [2, 4]
                : [4],
        );
    }

    /**
     * @param int[] $expectPerTeamCount
     */
    private function isValidPlayersCount(
        Battle3 $model,
        int $expectTotalCount = 8,
        array $expectPerTeamCount = [4],
    ): bool {
        $playerCount = 0;

        if (is_array($this->our_team_players) && $this->our_team_players) {
            $isMeCount = 0;
            foreach ($this->our_team_players as $player) {
                $model = Yii::createObject(PlayerForm::class);
                $model->attributes = $player;

                ++$playerCount;
                if (self::boolVal($model->me)) {
                    ++$isMeCount;
                }
            }

            if ($isMeCount !== 1) {
                return false;
            }

            if (!in_array(count($this->our_team_players), $expectPerTeamCount, true)) {
                return false;
            }
        } else {
            return false;
        }

        if (is_array($this->their_team_players) && $this->their_team_players) {
            foreach ($this->their_team_players as $player) {
                $model = Yii::createObject(PlayerForm::class);
                $model->attributes = $player;

                ++$playerCount;
                if (self::boolVal($model->me)) {
                    return false;
                }
            }

            if (!in_array(count($this->their_team_players), $expectPerTeamCount, true)) {
                return false;
            }
        } else {
            return false;
        }

        if (is_array($this->third_team_players) && $this->third_team_players) {
            foreach ($this->third_team_players as $player) {
                $model = Yii::createObject(PlayerForm::class);
                $model->attributes = $player;

                ++$playerCount;
                if (self::boolVal($model->me)) {
                    return false;
                }
            }

            if (!in_array(count($this->third_team_players), $expectPerTeamCount, true)) {
                return false;
            }
        }

        return $playerCount === $expectTotalCount;
    }

    private static function generateRandomFilename(string $ext): string
    {
        $uuid = strtolower((string)Uuid::v4());
        return vsprintf('%s/%s/%s.%s', [
            'spl3',
            substr($uuid, 0, 2),
            $uuid,
            strtolower($ext),
        ]);
    }
}
