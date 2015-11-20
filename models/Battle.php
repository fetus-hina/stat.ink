<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use app\components\helpers\DateTimeFormatter;

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
 * @property integer $env_id
 *
 * @property Agent $agent
 * @property Environment $env
 * @property FestTitle $festTitle
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
 */
class Battle extends ActiveRecord
{
    public static function find()
    {
        $query = new query\BattleQuery(get_called_class());
        $query->orderBy('{{battle}}.[[id]] DESC');
        return $query;
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

        $this->on(ActiveRecord::EVENT_BEFORE_INSERT, [$this, 'updateUserWeapon']);
        $this->on(ActiveRecord::EVENT_BEFORE_UPDATE, [$this, 'updateUserWeapon']);

        $this->on(ActiveRecord::EVENT_AFTER_INSERT, [$this, 'updateUserStat']);
        $this->on(ActiveRecord::EVENT_AFTER_UPDATE, [$this, 'updateUserStat']);
        $this->on(ActiveRecord::EVENT_AFTER_DELETE, [$this, 'updateUserStat']);
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
            [['is_win', 'is_knock_out'], 'boolean'],
            [['start_at', 'end_at', 'at'], 'safe'],
            [['kill_ratio', 'my_team_final_percent', 'his_team_final_percent'], 'number'],
            [['my_team_color_rgb', 'his_team_color_rgb'], 'string', 'min' => 6, 'max' => 6],
            [['ua_custom'], 'string'],
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
            'env_id' => 'Env ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgent()
    {
        return $this->hasOne(Agent::className(), ['id' => 'agent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnv()
    {
        return $this->hasOne(Environment::className(), ['id' => 'env_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFestTitle()
    {
        return $this->hasOne(FestTitle::className(), ['id' => 'fest_title_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGender()
    {
        return $this->hasOne(Gender::className(), ['id' => 'gender_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLobby()
    {
        return $this->hasOne(Lobby::className(), ['id' => 'lobby_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map::className(), ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRank()
    {
        return $this->hasOne(Rank::className(), ['id' => 'rank_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRankAfter()
    {
        return $this->hasOne(Rank::className(), ['id' => 'rank_after_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::className(), ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon::className(), ['id' => 'weapon_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattleDeathReasons()
    {
        return $this->hasMany(BattleDeathReason::className(), ['battle_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReasons()
    {
        return $this
            ->hasMany(DeathReason::className(), ['id' => 'reason_id'])
            ->viaTable('battle_death_reason', ['battle_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattlePlayers()
    {
        return $this->hasMany(BattlePlayer::className(), ['battle_id' => 'id'])
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
     * @return \yii\db\ActiveQuery
     */
    public function getBattleImages()
    {
        return $this->hasMany(BattleImage::className(), ['battle_id' => 'id']);
    }

    public function getBattleImageJudge()
    {
        return $this->hasOne(BattleImage::className(), ['battle_id' => 'id'])
            ->andWhere(['type_id' => BattleImageType::ID_JUDGE]);
    }

    public function getBattleImageResult()
    {
        return $this->hasOne(BattleImage::className(), ['battle_id' => 'id'])
            ->andWhere(['type_id' => BattleImageType::ID_RESULT]);
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
        if ($this->rule_id === null) {
            return false;
        }
        if (!$rule = $this->getRule()->with('mode')->one()) {
            return false;
        }
        return $rule->mode && $rule->mode->key === $key;
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

    public function getPreviousBattle()
    {
        return $this->hasOne(static::className(), ['user_id' => 'user_id'])
            ->andWhere(['<', '{{battle}}.[[id]]', $this->id])
            ->orderBy('{{battle}}.[[id]] DESC')
            ->limit(1);
    }

    public function getNextBattle()
    {
        return $this->hasOne(static::className(), ['user_id' => 'user_id'])
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

    public function setKillRatio()
    {
        if ($this->kill === null || $this->death === null) {
            $this->kill_ratio = null;
            return;
        }
        if ($this->death == 0) {
            $this->kill_ratio = ($this->kill == 0) ? 1.00 : 99.99;
            return;
        }
        $this->kill_ratio = sprintf('%.2f', $this->kill / $this->death);
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
            'death_reasons' => in_array('death_reasons', $skips, true)
                ? null
                : array_map(
                    function ($model) {
                        return $model->toJsonArray();
                    },
                    $this->battleDeathReasons
                ),
            'gender' => $this->gender ? $this->gender->toJsonArray() : null,
            'fest_title' => $this->gender && $this->festTitle
                ? $this->festTitle->toJsonArray($this->gender)
                : null,
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
            'period' => $this->period,
            'players' => (in_array('players', $skips, true) || count($this->battlePlayers) === 0)
                ? null
                : array_map(
                    function ($model) {
                        return $model->toJsonArray();
                    },
                    $this->battlePlayers
                ),
            'agent' => [
                'name' => $this->agent ? $this->agent->name : null,
                'version' => $this->agent ? $this->agent->version : null,
                'custom' => $this->ua_custom,
            ],
            'environment' => $this->env ? $this->env->text : null,
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
            $this->map ? Yii::t('app-map', $this->map->name) : '不明',
            $this->rule ? Yii::t('app-rule', $this->rule->name) : '不明',
            $this->is_win === true
                ? '勝ち'
                : ($this->is_win === false ? '負け' : '不明'),
        ];
    }
}
