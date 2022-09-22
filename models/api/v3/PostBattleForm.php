<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v3;

use Yii;
use app\components\behaviors\TrimAttributesBehavior;
use app\components\db\Connection;
use app\components\helpers\CriticalSection;
use app\components\helpers\ImageConverter;
use app\components\helpers\UuidRegexp;
use app\components\helpers\db\Now;
use app\components\validators\BattleAgentVariable3Validator;
use app\components\validators\BattleImageValidator;
use app\components\validators\BattlePlayer3FormValidator;
use app\components\validators\KeyValidator;
use app\models\Agent;
use app\models\AgentVariable3;
use app\models\Battle3;
use app\models\BattleAgentVairable3;
use app\models\BattleImageGear3;
use app\models\BattleImageJudge3;
use app\models\BattleImageResult3;
use app\models\BattleMedal3;
use app\models\Lobby3;
use app\models\Map3;
use app\models\Map3Alias;
use app\models\Medal3;
use app\models\Rank3;
use app\models\Result3;
use app\models\Rule3;
use app\models\SplatoonVersion3;
use app\models\User;
use app\models\Weapon3;
use app\models\Weapon3Alias;
use app\models\api\v3\postBattle\PlayerForm;
use app\models\api\v3\postBattle\TypeHelperTrait;
use jp3cki\uuid\Uuid;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\web\UploadedFile;

/**
 * @property-read Battle3|null $sameBattle
 * @property-read bool $isTest
 */
final class PostBattleForm extends Model
{
    use TypeHelperTrait;

    public const SAME_BATTLE_THRESHOLD_TIME = 86400;

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
    public $inked;
    public $our_team_inked;
    public $their_team_inked;
    public $our_team_percent;
    public $their_team_percent;
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
    public $challenge_win;
    public $challenge_lose;
    public $cash_before;
    public $cash_after;
    public $our_team_players;
    public $their_team_players;
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
            [['private_note', 'link_url', 'agent', 'agent_version'], 'string'],

            [['uuid'], 'match', 'pattern' => UuidRegexp::get(true)],
            [['result'], 'in', 'range' => ['win', 'lose', 'draw']],
            [['link_url'], 'url',
                'validSchemes' => ['http', 'https'],
                'defaultScheme' => null,
                'enableIDN' => false,
            ],
            [['agent'], 'string', 'max' => 64],
            [['agent_version'], 'string', 'max' => 255],
            [['agent', 'agent_version'], 'required',
                'when' => fn () => \trim((string)$this->agent) !== '' || \trim((string)$this->agent_version) !== '',
            ],
            [['test', 'knockout', 'automated'], 'in',
                'range' => ['yes', 'no', true, false],
                'strict' => true,
            ],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['kill', 'assist', 'kill_or_assist', 'death', 'special'], 'integer', 'min' => 0, 'max' => 99],
            [['inked'], 'integer', 'min' => 0, 'max' => 9999],
            [['our_team_inked', 'their_team_inked'], 'integer', 'min' => 0, 'max' => 99999],
            [['our_team_percent', 'their_team_percent'], 'number', 'min' => 0, 'max' => 100],
            [['our_team_count', 'their_team_count'], 'integer', 'min' => 0, 'max' => 100],
            [['level_before', 'level_after'], 'integer', 'min' => 1, 'max' => 99],
            [['rank_before_s_plus', 'rank_after_s_plus'], 'integer', 'min' => 0, 'max' => 50],
            [['rank_before_exp', 'rank_after_exp'], 'integer', 'min' => 0],
            [['rank_exp_change'], 'integer'],
            [['challenge_win'], 'integer', 'min' => 0, 'max' => 5],
            [['challenge_lose'], 'integer', 'min' => 0, 'max' => 3],
            [['cash_before', 'cash_after'], 'integer', 'min' => 0, 'max' => 9999999],
            [['start_at', 'end_at'], 'integer',
                'min' => \strtotime('2022-01-01T00:00:00+00:00'),
                'max' => time() + 3600,
            ],

            [['lobby'], KeyValidator::class, 'modelClass' => Lobby3::class],
            [['rule'], KeyValidator::class, 'modelClass' => Rule3::class],
            [['stage'], KeyValidator::class,
                'modelClass' => Map3::class,
                'aliasClass' => Map3Alias::class,
            ],
            [['weapon'], KeyValidator::class,
                'modelClass' => Weapon3::class,
                'aliasClass' => Weapon3Alias::class,
            ],
            [['rank_before', 'rank_after'], KeyValidator::class, 'modelClass' => Rank3::class],

            [['our_team_players', 'their_team_players'], 'each',
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
            !\is_string($this->uuid) ||
            $this->uuid === ''
        ) {
            return null;
        }

        if (!$user = Yii::$app->user->identity) {
            return null;
        }

        $t = (int)($_SERVER['REQUEST_TIME'] ?? time());
        return Battle3::find()
            ->where([
                'user_id' => $user->id,
                'client_uuid' => $this->uuid,
                'is_deleted' => false,
            ])
            ->andWhere(
                ['>=', 'created_at', \gmdate('Y-m-d\TH:i:sP', $t - self::SAME_BATTLE_THRESHOLD_TIME)]
            )
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
            return $this->getSameBattle() ?? $this->saveNewBattleRelation();
        } finally {
            unset($lock);
        }
    }

    private function getCriticalSectionId(): string
    {
        $values = [
            'class' => __CLASS__,
            'user' => Yii::$app->user->id,
            'version' => 1,
        ];
        \asort($values);
        return \rtrim(
            \base64_encode(
                \hash_hmac(
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

                // TODO: more data

                if (!$this->saveBattleImages($battle)) {
                    return null;
                }

                return $battle;
            });
        } catch (Throwable $e) {
            $this->addError(
                '_system',
                vsprintf('Failed to store your battle (internal error), %s', [
                    \get_class($e),
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
            'rule_id' => self::key2id($this->rule, Rule3::class),
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
            'inked' => self::intVal($this->inked),
            'our_team_inked' => self::intVal($this->our_team_inked),
            'their_team_inked' => self::intVal($this->their_team_inked),
            'our_team_percent' => self::floatVal($this->our_team_percent),
            'their_team_percent' => self::floatVal($this->their_team_percent),
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
            'version_id' => self::gameVersion(self::intVal($this->start_at), self::intVal($this->end_at)),
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
        ]);

        // kill+assistが不明でkillとassistがわかっている
        if ($model->kill_or_assist === null && \is_int($model->kill) && \is_int($model->assist)) {
            $model->kill_or_assist = $model->kill + $model->assist;
        }

        // 設定された値から統計に使えそうか雑な判断をする
        $model->use_for_entire = self::isUsableForEntireStats($model, self::intVal($this->start_at));

        if (!$model->save()) {
            $this->addError('_system', vsprintf('Failed to store new battle, info=%s', [
                \base64_encode(Json::encode($model->getFirstErrors())),
            ]));
            return null;
        }

        return $model;
    }

    private function savePlayers(Battle3 $battle): bool
    {
        if (\is_array($this->our_team_players) && $this->our_team_players) {
            foreach ($this->our_team_players as $player) {
                $model = Yii::createObject(PlayerForm::class);
                $model->attributes = $player;
                if (!$model->save($battle, true)) {
                    return false;
                }
            }
        }

        if (\is_array($this->their_team_players) && $this->their_team_players) {
            foreach ($this->their_team_players as $player) {
                $model = Yii::createObject(PlayerForm::class);
                $model->attributes = $player;
                if (!$model->save($battle, false)) {
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
        $text = \trim($text);
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
        if (!\is_array($map) || !$map) {
            return true;
        }

        foreach ($map as $k => $v) {
            $model = Yii::createObject([
                'class' => BattleAgentVairable3::class,
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

    private function findOrCreateAgentVariable(string $key, string $value): ?int
    {
        // use double-checking lock pattern
        //
        // 1. find data without lock (fast, the data already exists)
        // In most cases, we'll find them here.
        $model = AgentVariable3::findOne(['key' => $key, 'value' => $value]);
        if (!$model) {
            // 2. lock if not found
            if (!$lock = CriticalSection::lock(AgentVariable3::class, 60)) {
                return null;
            }
            try {
                // 3. find data again with lock (it may created on another process/thread)
                $model = AgentVariable3::findOne(['key' => $key, 'value' => $value]);
                if (!$model) {
                    // 4. create new data with lock (it's new!)
                    $model = Yii::createObject([
                        'class' => AgentVariable3::class,
                        'key' => $key,
                        'value' => $value,
                    ]);
                    if (!$model->save()) {
                        return null;
                    }
                }
            } finally {
                unset($lock);
            }
        }

        return (int)$model->id;
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
                    $target['isResult']
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
        bool $isResultImage
    ): bool {
        if ($this->$attribute === null || $this->$attribute === '') {
            return true;
        }

        $binary = $this->$attribute instanceof UploadedFile
            ? (string)@\file_get_contents($this->$attribute->tempName)
            : $this->$attribute;

        if (
            !ImageConverter::convert(
                $binary,
                \vsprintf('%s/%s', [
                    (string)Yii::getAlias('@webroot/images'),
                    $fileName,
                ]),
                false, // TODO: $blackoutPosList => $isResultImage を参照
                null // $outPathArchivePng
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

    private static function userAgent(?string $agentName, ?string $agentVersion): ?int
    {
        $agentName = self::strVal($agentName);
        $agentVersion = self::strVal($agentVersion);
        if ($agentName === null || $agentVersion === null) {
            return null;
        }

        $model = Agent::find()
            ->andWhere([
                'name' => $agentName,
                'version' => $agentVersion,
            ])
            ->limit(1)
            ->one();
        if (!$model) {
            $model = Yii::createObject([
                'class' => Agent::class,
                'name' => $agentName,
                'version' => $agentVersion,
            ]);
            if (!$model->save()) {
                return null;
            }
        }

        return (int)$model->id;
    }

    private static function now(): Now
    {
        return Yii::createObject(Now::class);
    }

    private static function gameVersion(?int $startAt, ?int $endAt): ?int
    {
        $startAt = self::guessStartAt($startAt, $endAt);
        $model = SplatoonVersion3::find()
            ->andWhere(['<=', 'release_at', self::tsVal($startAt)])
            ->orderBy(['release_at' => SORT_DESC])
            ->limit(1)
            ->one();
        return $model ? (int)$model->id : null;
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
        if (\is_int($startAt)) {
            return $startAt;
        }

        if (\is_int($endAt)) {
            // Guess the battle started 3 minutes before the end time.
            // It is clear if the battle is Turf War.
            // In other modes, the regulation time is 5 minutes,
            // but 3 minutes would be a reasonable estimate because of knockout possibilities.
            return $endAt - 180;
        }

        // Use 5 minutes before the current time as an estimated value if the time is unknown.
        return \time() - 300;
    }

    private static function isUsableForEntireStats(Battle3 $model, ?int $startAt): bool
    {
        if (
            !$model->is_automated ||
            !\is_int($startAt) ||
            $startAt < \time() - 86400 ||
            $startAt > \time() ||
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
            !($result = $model->result)
        ) {
            return false;
        }

        if (
            $lobby->key === 'private' ||
            !$result->aggregatable
        ) {
            return false;
        }

        return true;
    }

    private static function generateRandomFilename(string $ext): string
    {
        $uuid = \strtolower((string)Uuid::v4());
        return \vsprintf('%s/%s/%s.%s', [
            'spl3',
            \substr($uuid, 0, 2),
            $uuid,
            \strtolower($ext),
        ]);
    }
}
