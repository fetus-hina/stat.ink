<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Throwable;
use Yii;
use app\components\ability\Effect;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\Differ;
use app\components\helpers\db\Now;
use app\models\query\BattleQuery;
use stdClass;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Json;
use yii\helpers\Url;

use function array_keys;
use function array_map;
use function call_user_func;
use function count;
use function date;
use function filter_var;
use function in_array;
use function is_array;
use function is_int;
use function is_object;
use function is_string;
use function json_decode;
use function ksort;
use function sprintf;
use function str_replace;
use function strtotime;
use function time;
use function trim;
use function ucwords;
use function usort;

use const FILTER_VALIDATE_INT;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const SORT_STRING;

/**
 * This is the model class for table "battle".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $rule_id
 * @property integer $map_id
 * @property integer $weapon_id
 * @property integer $level
 * @property integer $rank_id
 * @property boolean $is_win
 * @property integer $rank_in_team
 * @property integer $kill
 * @property integer $death
 * @property string $start_at
 * @property string $end_at
 * @property string $at
 * @property integer $agent_id
 * @property integer $level_after
 * @property integer $rank_after_id
 * @property integer $rank_exp
 * @property integer $rank_exp_after
 * @property integer $cash
 * @property integer $cash_after
 * @property integer $lobby_id
 * @property string $kill_ratio
 * @property integer $gender_id
 * @property integer $fest_title_id
 * @property integer $fest_title_after_id
 * @property integer $fest_exp
 * @property integer $fest_exp_after
 * @property integer $my_team_color_hue
 * @property integer $his_team_color_hue
 * @property string $my_team_color_rgb
 * @property string $his_team_color_rgb
 * @property integer $my_point
 * @property integer $my_team_final_point
 * @property integer $his_team_final_point
 * @property string $my_team_final_percent
 * @property string $his_team_final_percent
 * @property boolean $is_knock_out
 * @property integer $my_team_count
 * @property integer $his_team_count
 * @property integer $period
 * @property string $ua_custom
 * @property string $ua_variables
 * @property integer $env_id
 * @property boolean $is_automated
 * @property integer $headgear_id
 * @property integer $clothing_id
 * @property integer $shoes_id
 * @property string $link_url
 * @property string $note
 * @property string $private_note
 * @property integer $my_team_power
 * @property integer $his_team_power
 * @property integer $fest_power
 * @property integer $version_id
 * @property string $client_uuid
 * @property integer $agent_game_version_id
 * @property string $agent_game_version_date
 * @property integer $max_kill_combo
 * @property integer $max_kill_streak
 * @property boolean $use_for_entire
 * @property integer $bonus_id
 *
 * @property Agent $agent
 * @property Environment $env
 * @property FestTitle $festTitle
 * @property FestTitle $festTitleAfter
 * @property GearConfiguration $headgear
 * @property GearConfiguration $clothing
 * @property GearConfiguration $shoes
 * @property Gender $gender
 * @property Lobby $lobby
 * @property Map $map
 * @property Rank $rank
 * @property Rank $rankAfter
 * @property Rule $rule
 * @property User $user
 * @property Weapon $weapon
 * @property BattleDeathReason[] $battleDeathReasons
 * @property DeathReason[] $reasons
 * @property BattleImage[] $battleImages
 * @property BattlePlayer[] $battlePlayers
 * @property SplatoonVersion $splatoonVersion
 * @property SplatoonVersion $agentGameVersion
 * @property TurfwarWinBonus $bonus
 *
 * @property-read BattleImage|null $battleImageGear
 * @property-read BattleImage|null $battleImageJudge
 * @property-read BattleImage|null $battleImageResult
 */
class Battle extends ActiveRecord
{
    public $skipSaveHistory = false;

    public static function find(): BattleQuery
    {
        $query = new BattleQuery(static::class);
        $query->orderBy('{{battle}}.[[id]] DESC');
        return $query;
    }

    public static function getRoughCount(): ?int
    {
        try {
            $count = filter_var(
                (new Query())
                    ->select('[[last_value]]')
                    ->from('{{battle_id_seq}}')
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

    public static function getTotalRoughCount()
    {
        $list = [
            [self::class, 'getRoughCount'],
            [Battle2::class, 'getRoughCount'],
        ];
        $total = 0;
        foreach ($list as $callback) {
            $tmp = call_user_func($callback);
            if ($tmp === false || $tmp === null) {
                return false;
            }
            $total += $tmp;
        }
        return $total;
    }

    public function __get($attr)
    {
        switch ($attr) {
            case 'kill_rate':
                return $this->getKillRate();

            default:
                return parent::__get($attr);
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle';
    }

    public function init()
    {
        parent::init();
        $this->on(ActiveRecord::EVENT_BEFORE_INSERT, [$this, 'setKillRatio']);
        $this->on(ActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'setKillRatio']);

        $this->on(ActiveRecord::EVENT_BEFORE_VALIDATE, [$this, 'setPeriod']);
        $this->on(ActiveRecord::EVENT_BEFORE_VALIDATE, [$this, 'setBonus']);

        $this->on(ActiveRecord::EVENT_BEFORE_INSERT, [$this, 'setSplatoonVersion']);

        $this->on(ActiveRecord::EVENT_BEFORE_INSERT, [$this, 'updateUserWeapon']);
        $this->on(ActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'updateUserWeapon']);

        $this->on(ActiveRecord::EVENT_AFTER_INSERT, [$this, 'updateUserStat']);
        $this->on(ActiveRecord::EVENT_AFTER_UPDATE, [$this, 'updateUserStat']);
        $this->on(ActiveRecord::EVENT_AFTER_DELETE, [$this, 'updateUserStat']);

        $this->on(ActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'saveEditHistory']);
        $this->on(ActiveRecord::EVENT_BEFORE_DELETE, [$this, 'saveDeleteHistory']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'at', 'period'], 'required'],
            [['user_id', 'rule_id', 'map_id', 'weapon_id', 'level', 'rank_id', 'period'], 'integer'],
            [['rank_in_team', 'kill', 'death', 'agent_id', 'env_id'], 'integer'],
            [['level_after', 'rank_after_id', 'rank_exp', 'rank_exp_after', 'cash', 'cash_after'], 'integer'],
            [['lobby_id', 'gender_id', 'fest_title_id', 'my_team_color_hue', 'his_team_color_hue'], 'integer'],
            [['my_point', 'my_team_final_point', 'his_team_final_point', 'my_team_count', 'his_team_count'], 'integer'],
            [['fest_title_after_id', 'fest_exp', 'fest_exp_after'], 'integer'],
            [['headgear_id', 'clothing_id', 'shoes_id'], 'integer'],
            [['is_win', 'is_knock_out', 'is_automated'], 'boolean'],
            [['start_at', 'end_at', 'at'], 'safe'],
            [['kill_ratio', 'my_team_final_percent', 'his_team_final_percent'], 'number'],
            [['my_team_color_rgb', 'his_team_color_rgb'], 'string', 'min' => 6, 'max' => 6],
            [['ua_custom', 'ua_variables'], 'string'],
            [['link_url'], 'url'],
            [['note', 'private_note'], 'string'],
            [['my_team_power', 'his_team_power', 'fest_power'], 'integer'],
            [['version_id'], 'integer'],
            [['client_uuid', 'agent_game_version_date'], 'string', 'max' => 64],
            [['agent_game_version_id'], 'integer'],
            [['agent_game_version_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SplatoonVersion::class,
                'targetAttribute' => ['agent_game_version_id' => 'id'],
            ],
            [['max_kill_combo', 'max_kill_streak'], 'integer', 'min' => 0],
            [['use_for_entire'], 'boolean'],
            [['bonus_id'], 'integer'],
            [['bonus_id'], 'exist', 'skipOnError' => true,
                'targetClass' => TurfwarWinBonus::class,
                'targetAttribute' => ['bonus_id' => 'id'],
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
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
            'weapon_id' => 'Weapon ID',
            'level' => 'Level',
            'rank_id' => 'Rank ID',
            'is_win' => 'Is Win',
            'rank_in_team' => 'Rank In Team',
            'kill' => 'Kill',
            'death' => 'Death',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'at' => 'At',
            'agent_id' => 'Agent ID',
            'level_after' => 'Level After',
            'rank_after_id' => 'Rank After ID',
            'rank_exp' => 'Rank Exp',
            'rank_exp_after' => 'Rank Exp After',
            'cash' => 'Cash',
            'cash_after' => 'Cash After',
            'lobby_id' => 'Lobby ID',
            'kill_ratio' => 'Kill Ratio',
            'gender_id' => 'Gender ID',
            'fest_title_id' => 'Fest Title ID',
            'my_team_color_hue' => 'My Team Color Hue',
            'his_team_color_hue' => 'His Team Color Hue',
            'my_team_color_rgb' => 'My Team Color Rgb',
            'his_team_color_rgb' => 'His Team Color Rgb',
            'my_point' => 'My Point',
            'my_team_final_point' => 'My Team Final Point',
            'his_team_final_point' => 'His Team Final Point',
            'my_team_final_percent' => 'My Team Final Percent',
            'his_team_final_percent' => 'His Team Final Percent',
            'is_knock_out' => 'Is Knock Out',
            'my_team_count' => 'My Team Count',
            'his_team_count' => 'His Team Count',
            'period' => 'Period',
            'ua_custom' => 'UA Custom',
            'ua_variables' => 'UA Variables',
            'env_id' => 'Env ID',
            'fest_title_after_id' => 'Fest Title After ID',
            'fest_exp' => 'Fest Exp',
            'fest_exp_after' => 'Fest Exp After',
            'is_automated' => 'Is Automated',
            'headgear_id' => 'Headgear ID',
            'clothing_id' => 'Clothing ID',
            'shoes_id' => 'Shoes ID',
            'link_url' => 'Link URL',
            'note' => 'Note',
            'private_note' => 'Note (Private)',
            'my_team_power' => 'My Team Power',
            'his_team_power' => 'His Team Power',
            'version_id' => 'Splatoon Version ID',
            'client_uuid' => 'Client-side UUID',
            'max_kill_combo' => 'Max Kill Combo',
            'max_kill_streak' => 'Max Kill Streak',
            'use_for_entire' => 'Use for entire stats',
            'bonus_id' => 'Bonus ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAgent()
    {
        return $this->hasOne(Agent::class, ['id' => 'agent_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getEnv()
    {
        return $this->hasOne(Environment::class, ['id' => 'env_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFestTitle()
    {
        return $this->hasOne(FestTitle::class, ['id' => 'fest_title_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFestTitleAfter()
    {
        return $this->hasOne(FestTitle::class, ['id' => 'fest_title_after_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getHeadgear()
    {
        return $this->hasOne(GearConfiguration::class, ['id' => 'headgear_id'])
            ->with(['primaryAbility', 'secondaries.ability']);
    }

    /**
     * @return ActiveQuery
     */
    public function getClothing()
    {
        return $this->hasOne(GearConfiguration::class, ['id' => 'clothing_id'])
            ->with(['primaryAbility', 'secondaries.ability']);
    }

    /**
     * @return ActiveQuery
     */
    public function getShoes()
    {
        return $this->hasOne(GearConfiguration::class, ['id' => 'shoes_id'])
            ->with(['primaryAbility', 'secondaries.ability']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGender()
    {
        return $this->hasOne(Gender::class, ['id' => 'gender_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLobby()
    {
        return $this->hasOne(Lobby::class, ['id' => 'lobby_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map::class, ['id' => 'map_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRank()
    {
        return $this->hasOne(Rank::class, ['id' => 'rank_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRankAfter()
    {
        return $this->hasOne(Rank::class, ['id' => 'rank_after_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::class, ['id' => 'rule_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon::class, ['id' => 'weapon_id']);
    }

    public function getWeaponAttack()
    {
        $weapon = $this->weapon;
        $version = $this->splatoonVersion;
        return $weapon && $version
            ? WeaponAttack::findByWeaponAndVersion($weapon, $version)
            : null;
    }

    /**
     * @return ActiveQuery
     */
    public function getBattleDeathReasons()
    {
        return $this->hasMany(BattleDeathReason::class, ['battle_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getReasons()
    {
        return $this
            ->hasMany(DeathReason::class, ['id' => 'reason_id'])
            ->viaTable('battle_death_reason', ['battle_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBattlePlayers()
    {
        return $this->hasMany(BattlePlayer::class, ['battle_id' => 'id'])
            ->with(['weapon', 'weapon.type', 'weapon.subweapon', 'weapon.special', 'rank'])
            ->orderBy('{{battle_player}}.[[id]] ASC');
    }

    public function getMyTeamPlayers()
    {
        return $this->getBattlePlayers()
            ->andWhere(['{{battle_player}}.[[is_my_team]]' => true]);
    }

    public function getHisTeamPlayers()
    {
        return $this->getBattlePlayers()
            ->andWhere(['{{battle_player}}.[[is_my_team]]' => false]);
    }

    /**
     * @return ActiveQuery
     */
    public function getBattleImages()
    {
        return $this->hasMany(BattleImage::class, ['battle_id' => 'id']);
    }

    public function getBattleImageJudge()
    {
        return $this->hasOne(BattleImage::class, ['battle_id' => 'id'])
            ->andWhere(['type_id' => BattleImageType::ID_JUDGE]);
    }

    public function getBattleImageResult()
    {
        return $this->hasOne(BattleImage::class, ['battle_id' => 'id'])
            ->andWhere(['type_id' => BattleImageType::ID_RESULT]);
    }

    public function getBattleImageGear()
    {
        return $this->hasOne(BattleImage::class, ['battle_id' => 'id'])
            ->andWhere(['type_id' => BattleImageType::ID_GEAR]);
    }

    public function getBattleEvents()
    {
        return $this->hasOne(BattleEvents::class, ['id' => 'id']);
    }

    public function getEvents()
    {
        $model = $this->battleEvents;
        return $model ? $model->events : null;
    }

    public function getSplatoonVersion()
    {
        return $this->hasOne(SplatoonVersion::class, ['id' => 'version_id']);
    }

    public function getAgentGameVersion()
    {
        return $this->hasOne(SplatoonVersion::class, ['id' => 'agent_game_version_id']);
    }

    public function getBonus()
    {
        return $this->hasOne(TurfwarWinBonus::class, ['id' => 'bonus_id']);
    }

    public function getIsNawabari()
    {
        return $this->getIsThisGameMode('regular');
    }

    public function getIsGachi()
    {
        return $this->getIsThisGameMode('gachi');
    }

    private function getIsThisGameMode($key)
    {
        if ($this->rule && $this->rule->mode) {
            return $this->rule->mode->key === $key;
        }
        return false;
    }

    public function getIsMeaningful()
    {
        $props = [
            'rule_id', 'map_id', 'weapon_id', 'is_win', 'rank_in_team', 'kill', 'death',
        ];
        foreach ($props as $prop) {
            if ($this->$prop !== null) {
                return true;
            }
        }
        return true;
    }

    // compat
    public function getPeriodId()
    {
        return $this->period;
    }

    public function getInked()
    {
        if (
            $this->my_point === null ||
                $this->is_win === null ||
                !$this->bonus ||
                !$this->rule ||
                $this->rule->key !== 'nawabari'
        ) {
            return null;
        }
        return $this->is_win
            ? $this->my_point - $this->bonus->bonus
            : $this->my_point;
    }

    public function getPreviousBattle()
    {
        return $this->hasOne(static::class, ['user_id' => 'user_id'])
            ->andWhere(['<', '{{battle}}.[[id]]', $this->id])
            ->orderBy('{{battle}}.[[id]] DESC')
            ->limit(1);
    }

    public function getNextBattle()
    {
        return $this->hasOne(static::class, ['user_id' => 'user_id'])
            ->andWhere(['>', '{{battle}}.[[id]]', $this->id])
            ->orderBy('{{battle}}.[[id]] ASC')
            ->limit(1);
    }

    public function setPeriod()
    {
        // 開始時間があれば開始時間から15秒(適当)引いた値を使うを使う。
        // 終了時間があれば終了時間から3分30秒(適当)引いた値を仕方ないので使う。
        // どっちもなければ登録時間から3分45秒(適当)引いた値を仕方ないので使う。
        $onSale = strtotime('2015-05-28 00:00:00+09:00');
        $now = (int)(@$_SERVER['REQUEST_TIME'] ?: time());

        $time = false;
        if ($time === false && is_string($this->start_at) && trim($this->start_at) !== '') {
            if (($t = strtotime($this->start_at)) !== false && $t >= $onSale && $t <= $now) {
                $time = $t - 15;
            }
        }
        if ($time === false && is_string($this->end_at) && trim($this->end_at) !== '') {
            if (($t = strtotime($this->end_at)) !== false && $t >= $onSale && $t <= $now) {
                $time = $t - (180 + 30);
            }
        }
        if ($time === false && is_string($this->at) && trim($this->at) !== '') {
            if (($t = strtotime($this->at)) !== false && $t >= $onSale && $t <= $now) {
                $time = $t - (180 + 45);
            }
        }
        if ($time === false) {
            $time = $now;
        }
        $this->period = \app\components\helpers\Battle::calcPeriod($time);
    }

    public function setBonus()
    {
        $this->bonus_id = null;
        if (!$this->rule_id) {
            return;
        }
        if (!$rule = Rule::findOne(['id' => $this->rule_id])) {
            return;
        }
        if ($rule->key !== 'nawabari') {
            return;
        }
        if (!$bonus = TurfwarWinBonus::find()->current()->one()) {
            return;
        }
        $this->bonus_id = $bonus->id;
    }

    public function setKillRatio()
    {
        if ($this->kill === null || $this->death === null) {
            $this->kill_ratio = null;
            return;
        }
        if ($this->death == 0) {
            $this->kill_ratio = $this->kill == 0 ? 1.00 : 99.99;
            return;
        }
        $this->kill_ratio = sprintf('%.2f', $this->kill / $this->death);
    }

    public function setSplatoonVersion()
    {
        if ($this->version_id) {
            return;
        }
        $time = (function () {
            if (is_string($this->end_at) && trim($this->end_at) !== '') {
                return strtotime($this->end_at);
            }
            if (is_string($this->at) && trim($this->at) !== '') {
                return strtotime($this->at);
            }
            return false;
        })();
        if (!is_int($time)) {
            $time = (int)($_SERVER['REQUEST_TIME'] ?? time());
        }
        $version = SplatoonVersion::findCurrentVersion($time);
        $this->version_id = $version ? $version->id : null;
    }

    public function updateUserStat()
    {
        if (!$stat = UserStat::findOne(['user_id' => $this->user_id])) {
            $stat = new UserStat();
            $stat->user_id = $this->user_id;
        }
        $stat->createCurrentData();
        $stat->save();
    }

    public function updateUserWeapon()
    {
        $dirty = $this->getDirtyAttributes();
        if (!isset($dirty['weapon_id'])) {
            return;
        }
        if (!$this->getIsNewRecord()) {
            if ($oldWeaponId = $this->oldAttributes['weapon_id']) {
                $old = UserWeapon::findOne([
                    'user_id' => $this->user_id,
                    'weapon_id' => $oldWeaponId,
                ]);
                if ($old) {
                    if ($old->count <= 1) {
                        if (!$old->delete()) {
                            return false;
                        }
                    } else {
                        $old->count--;
                        if (!$old->save()) {
                            return false;
                        }
                    }
                }
            }
        }
        if ($this->weapon_id) {
            $new = UserWeapon::findOne([
                'user_id' => $this->user_id,
                'weapon_id' => $this->weapon_id,
            ]);
            if ($new) {
                $new->count++;
            } else {
                $new = new UserWeapon();
                $new->attributes = [
                    'user_id' => $this->user_id,
                    'weapon_id' => $this->weapon_id,
                    'count' => 1,
                ];
            }
            if (!$new->save()) {
                return false;
            }
        }
    }

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        foreach ($this->battleImages as $img) {
            if (!$img->delete()) {
                return false;
            }
        }
        BattleDeathReason::deleteAll([
            'battle_id' => $this->id,
        ]);
        BattlePlayer::deleteAll([
            'battle_id' => $this->id,
        ]);
        if ($this->weapon_id) {
            $userWeapon = UserWeapon::findOne([
                'user_id' => $this->user_id,
                'weapon_id' => $this->weapon_id,
            ]);
            if ($userWeapon) {
                if ($userWeapon->count <= 1) {
                    if (!$userWeapon->delete()) {
                        return false;
                    }
                } else {
                    $userWeapon->count--;
                    if (!$userWeapon->save()) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    public function toJsonArray(array $skips = [])
    {
        $events = null;
        if ($this->events && !in_array('events', $skips, true)) {
            $events = Json::decode($this->events, false);
            usort($events, fn ($a, $b) => $a->at <=> $b->at);
        }
        return [
            'id' => $this->id,
            'url' => Url::to(['show/battle', 'screen_name' => $this->user->screen_name, 'battle' => $this->id], true),
            'user' => !in_array('user', $skips, true) && $this->user ? $this->user->toJsonArray() : null,
            'lobby' => $this->lobby ? $this->lobby->toJsonArray() : null,
            'rule' => $this->rule ? $this->rule->toJsonArray() : null,
            'map' => $this->map ? $this->map->toJsonArray() : null,
            'weapon' => $this->weapon ? $this->weapon->toJsonArray() : null,
            'rank' => $this->rank ? $this->rank->toJsonArray() : null,
            'rank_exp' => $this->rank_exp,
            'rank_after' => $this->rankAfter ? $this->rankAfter->toJsonArray() : null,
            'rank_exp_after' => $this->rank_exp_after,
            'level' => $this->level,
            'level_after' => $this->level_after,
            'cash' => $this->cash,
            'cash_after' => $this->cash_after,
            'result' => $this->is_win === true ? 'win' : ($this->is_win === false ? 'lose' : null),
            'rank_in_team' => $this->rank_in_team,
            'kill' => $this->kill,
            'death' => $this->death,
            'kill_ratio' => isset($this->kill_ratio) ? (float)$this->kill_ratio : null,
            'max_kill_combo' => $this->max_kill_combo,
            'max_kill_streak' => $this->max_kill_streak,
            'death_reasons' => in_array('death_reasons', $skips, true)
                ? null
                : array_map(
                    fn ($model) => $model->toJsonArray(),
                    $this->battleDeathReasons,
                ),
            'gender' => $this->gender ? $this->gender->toJsonArray() : null,
            'fest_title' => $this->festTitle
                ? $this->festTitle->toJsonArray($this->gender)
                : null,
            'fest_exp' => $this->fest_exp,
            'fest_title_after' => $this->festTitleAfter
                ? $this->festTitleAfter->toJsonArray($this->gender)
                : null,
            'fest_exp_after' => $this->fest_exp_after,
            'fest_power' => $this->fest_power,
            'my_team_power' => $this->my_team_power,
            'his_team_power' => $this->his_team_power,
            'my_point' => $this->my_point,
            'my_team_final_point' => $this->my_team_final_point,
            'his_team_final_point' => $this->his_team_final_point,
            'my_team_final_percent' => $this->my_team_final_percent,
            'his_team_final_percent' => $this->his_team_final_percent,
            'knock_out' => $this->is_knock_out,
            'my_team_count' => $this->my_team_count,
            'his_team_count' => $this->his_team_count,
            'my_team_color' => [
                'hue' => $this->my_team_color_hue,
                'rgb' => $this->my_team_color_rgb,
            ],
            'his_team_color' => [
                'hue' => $this->his_team_color_hue,
                'rgb' => $this->his_team_color_rgb,
            ],
            'image_judge' => $this->battleImageJudge
                ? Url::to(Yii::getAlias('@web/images') . '/' . $this->battleImageJudge->filename, true)
                : null,
            'image_result' => $this->battleImageResult
                ? Url::to(Yii::getAlias('@web/images') . '/' . $this->battleImageResult->filename, true)
                : null,
            'image_gear' => $this->battleImageGear
                ? Url::to(Yii::getAlias('@web/images') . '/' . $this->battleImageGear->filename, true)
                : null,
            'gears' => in_array('gears', $skips, true)
                ? null
                : [
                    'headgear' => $this->headgear ? $this->headgear->toJsonArray() : null,
                    'clothing' => $this->clothing ? $this->clothing->toJsonArray() : null,
                    'shoes' => $this->shoes ? $this->shoes->toJsonArray() : null,
                ],
            'period' => $this->period,
            'players' => in_array('players', $skips, true) || count($this->battlePlayers) === 0
                ? null
                : array_map(
                    fn ($model) => $model->toJsonArray(),
                    $this->battlePlayers,
                ),
            'events' => $events,
            'agent' => [
                'name' => $this->agent ? $this->agent->name : null,
                'version' => $this->agent ? $this->agent->version : null,
                'game_version' => $this->agentGameVersion->name ?? null,
                'game_version_date' => $this->agent_game_version_date,
                'custom' => $this->ua_custom,
                'variables' => $this->ua_variables
                    ? match (true) {
                        is_array($this->ua_variables) => $this->ua_variables,
                        is_string($this->ua_variables) => @json_decode($this->ua_variables, false),
                        default => null,
                    }
                    : null,
            ],
            'automated' => !!$this->is_automated,
            'environment' => $this->env ? $this->env->text : null,
            'link_url' => (string)$this->link_url !== '' ? $this->link_url : null,
            'note' => (string)$this->note !== '' ? $this->note : null,
            'game_version' => $this->splatoonVersion ? $this->splatoonVersion->name : null,
            'nawabari_bonus' => ($this->rule->key ?? null) === 'nawabari' && $this->bonus
                ? (int)$this->bonus->bonus
                : null,
            'start_at' => $this->start_at != ''
                ? DateTimeFormatter::unixTimeToJsonArray(strtotime($this->start_at))
                : null,
            'end_at' => $this->end_at != ''
                ? DateTimeFormatter::unixTimeToJsonArray(strtotime($this->end_at))
                : null,
            'register_at' => DateTimeFormatter::unixTimeToJsonArray(strtotime($this->at)),
        ];
    }

    public function toIkaLogCsv()
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
            $this->map ? Yii::t('app-map', $this->map->name) : '?',
            $this->rule ? Yii::t('app-rule', $this->rule->name) : '?',
            $this->is_win === true
                ? '勝ち'
                : ($this->is_win === false ? '負け' : '不明'),
        ];
    }

    public function toIkaLogJson()
    {
        $ret = [
            'time' => strtotime($this->end_at ?: $this->at),
            'event' => 'GameResult',
            'map' => $this->map ? Yii::t('app-map', $this->map->name) : '?',
            'rule' => $this->rule ? Yii::t('app-rule', $this->rule->name) : '?',
            'result' => $this->is_win === null
                ? 'unknown'
                : ($this->is_win ? 'win' : 'lose'),
        ];
        if ($this->rank) {
            $ret['udemae_pre'] = Yii::t('app-rank', $this->rank->name);
            if ($this->rank_exp !== null) {
                $ret['udemae_exp_pre'] = (int)$this->rank_exp;
            }
        }
        if ($this->rankAfter) {
            $ret['udemae_after'] = Yii::t('app-rank', $this->rankAfter->name);
            if ($this->rank_exp_after !== null) {
                $ret['udemae_exp_after'] = (int)$this->rank_exp_after;
            }
        }
        if ($this->cash_after !== null) {
            $ret['cash_after'] = (int)$this->cash_after;
        }
        if ($this->is_win !== null) {
            $ret['team'] = $this->is_win ? 1 : 2;
        }
        if ($this->kill !== null) {
            $ret['kills'] = (int)$this->kill;
        }
        if ($this->death !== null) {
            $ret['deaths'] = (int)$this->death;
        }
        if ($this->my_point !== null) {
            $ret['score'] = (int)$this->my_point;
        }
        if ($this->weapon) {
            $ret['weapon'] = Yii::t('app-weapon', $this->weapon->name);
        }
        if ($this->rank_in_team !== null) {
            $ret['rank_in_team'] = (int)$this->rank_in_team;
        }

        if ($this->battlePlayers) {
            $ret['players'] = array_map(
                function ($p) {
                    $ret = [];
                    if ($this->is_win !== null) {
                        $ret['team'] = $this->is_win === $p->is_my_team ? 1 : 2;
                    }
                    if ($p->kill !== null) {
                        $ret['kills'] = (int)$p->kill;
                    }
                    if ($p->death !== null) {
                        $ret['deaths'] = (int)$p->death;
                    }
                    if ($p->point !== null) {
                        $ret['score'] = (int)$p->point;
                    }
                    if ($p->rank) {
                        $ret['udemae_pre'] = Yii::t('app-rank', $p->rank->name);
                    }
                    if ($p->weapon) {
                        $ret['weapon'] = Yii::t('app-weapon', $p->weapon->name);
                    }
                    if ($p->rank_in_team !== null) {
                        $ret['rank_in_team'] = (int)$p->rank_in_team;
                    }
                    if (empty($ret)) {
                        return new stdClass();
                    }
                    return $ret;
                },
                $this->battlePlayers,
            );
        }
        return $ret;
    }

    public function getDeathReasonNamesFromEvents()
    {
        try {
            if ($this->events === null || $this->events === '') {
                return [];
            }
            $events = Json::decode($this->events, false);
            if (!is_array($events) || empty($events)) {
                return [];
            }

            // ["key" => null] のデータを一回構築する
            // 後でこの key を取得して理由名取得に回す
            $ret = [];
            foreach ($events as $event) {
                if (is_array($event)) {
                    $event = (object)$event;
                }
                if (is_object($event) && isset($event->type) && isset($event->reason) && $event->type === 'dead') {
                    $ret[$event->reason] = null;
                }
            }
            if (empty($ret)) {
                return [];
            }

            // null だった理由名を埋める
            $reasons = DeathReason::find()
                ->andWhere(['key' => array_keys($ret)])
                ->all();
            foreach ($reasons as $reason) {
                $ret[$reason->key] = $reason->getTranslatedName();
            }
            return $ret;
        } catch (Throwable $e) {
            return [];
        }
    }

    public function getGearAbilities()
    {
        $gears = [
            $this->headgear,
            $this->clothing,
            $this->shoes,
        ];

        $init = fn ($ability) => (object)[
            'name' => Yii::t('app-ability', $ability->name),
            'count' => (object)[
                'main' => 0,
                'sub' => 0,
            ],
        ];

        $ret = [];
        foreach ($gears as $gear) {
            if (!$gear) {
                continue;
            }
            if ($gear->primaryAbility) {
                if ($key = $gear->primaryAbility->key) {
                    if (!isset($ret[$key])) {
                        $ret[$key] = $init($gear->primaryAbility);
                    }
                    ++$ret[$key]->count->main;
                }
            }
            if ($gear->secondaries) {
                foreach ($gear->secondaries as $secondary) {
                    if ($secondary->ability) {
                        if ($key = $secondary->ability->key) {
                            if (!isset($ret[$key])) {
                                $ret[$key] = $init($secondary->ability);
                            }
                            ++$ret[$key]->count->sub;
                        }
                    }
                }
            }
        }

        return (object)$ret;
    }

    public function getHasAbilities()
    {
        return $this->headgear && $this->clothing && $this->shoes;
    }

    public function getAbilityEffects()
    {
        if (!$this->getHasAbilities()) {
            return null;
        }
        return Effect::factory($this);
    }

    public function getExtraData(): array
    {
        $json = $this->ua_variables;
        if ($json == '') {
            return [];
        }

        return (function () use ($json): array {
            $decoded = is_array($json) ? $json : @json_decode($json, true);
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
    }

    public function getKillRate()
    {
        if ($this->kill + $this->death === 0) {
            return null;
        }
        return $this->kill / ($this->kill + $this->death);
    }

    public function saveEditHistory()
    {
        if ($this->skipSaveHistory) {
            return true;
        }

        if (!$this->dirtyAttributes) {
            return true;
        }

        if (!$before = static::findOne(['id' => $this->id])) {
            return true;
        }

        return $this->saveEditHistoryImpl(
            Json::encode($before->toJsonArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            Json::encode($this->toJsonArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        );
    }

    public function saveDeleteHistory()
    {
        if ($this->skipSaveHistory) {
            return true;
        }

        return $this->saveEditHistoryImpl(
            Json::encode(['id' => $this->id], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            Json::encode(['_id' => 'deleted'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
        );
    }

    protected function saveEditHistoryImpl($jsonBefore, $jsonAfter)
    {
        try {
            $edit = new BattleEditHistory();
            $edit->battle_id = $this->id;
            $edit->diff = Differ::diff($jsonBefore, $jsonAfter);
            $edit->at = new Now();
            if ($edit->diff == '') {
                return true;
            }
            return !!$edit->save();
        } catch (Throwable $e) {
            return false;
        }
    }

    public function getCreatedAt(): int
    {
        return strtotime($this->at);
    }
}
