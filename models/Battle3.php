<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "battle3".
 *
 * @property integer $id
 * @property string $uuid
 * @property string $client_uuid
 * @property integer $user_id
 * @property integer $lobby_id
 * @property integer $rule_id
 * @property integer $map_id
 * @property integer $weapon_id
 * @property integer $result_id
 * @property boolean $is_knockout
 * @property integer $rank_in_team
 * @property integer $kill
 * @property integer $assist
 * @property integer $kill_or_assist
 * @property integer $death
 * @property integer $special
 * @property integer $inked
 * @property integer $our_team_inked
 * @property integer $their_team_inked
 * @property string $our_team_percent
 * @property string $their_team_percent
 * @property integer $our_team_count
 * @property integer $their_team_count
 * @property integer $level_before
 * @property integer $level_after
 * @property integer $rank_before_id
 * @property integer $rank_before_s_plus
 * @property integer $rank_before_exp
 * @property integer $rank_after_id
 * @property integer $rank_after_s_plus
 * @property integer $rank_after_exp
 * @property integer $cash_before
 * @property integer $cash_after
 * @property string $note
 * @property string $private_note
 * @property string $link_url
 * @property integer $version_id
 * @property integer $agent_id
 * @property boolean $is_automated
 * @property boolean $use_for_entire
 * @property string $start_at
 * @property string $end_at
 * @property integer $period
 * @property string $remote_addr
 * @property integer $remote_port
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $is_deleted
 * @property integer $challenge_win
 * @property integer $challenge_lose
 * @property integer $rank_exp_change
 * @property boolean $is_rank_up_battle
 * @property integer $clout_before
 * @property integer $clout_after
 * @property integer $clout_change
 * @property integer $fest_dragon_id
 * @property string $fest_power
 * @property boolean $has_disconnect
 * @property string $x_power_before
 * @property string $x_power_after
 * @property integer $our_team_role_id
 * @property integer $their_team_role_id
 * @property integer $third_team_role_id
 * @property string $our_team_color
 * @property string $their_team_color
 * @property string $third_team_color
 * @property integer $our_team_theme_id
 * @property integer $their_team_theme_id
 * @property integer $third_team_theme_id
 * @property integer $third_team_inked
 * @property string $third_team_percent
 * @property integer $signal
 *
 * @property Agent $agent
 * @property BattleAgentVariable3[] $battleAgentVariable3s
 * @property BattleImageGear3 $battleImageGear3
 * @property BattleImageJudge3 $battleImageJudge3
 * @property BattleImageResult3 $battleImageResult3
 * @property BattleMedal3[] $battleMedal3s
 * @property BattlePlayer3[] $battlePlayer3s
 * @property BattleTricolorPlayer3[] $battleTricolorPlayer3s
 * @property DragonMatch3 $festDragon
 * @property Lobby3 $lobby
 * @property Map3 $map
 * @property Medal3[] $medals
 * @property TricolorRole3 $ourTeamRole
 * @property Splatfest3Theme $ourTeamTheme
 * @property Rank3 $rankAfter
 * @property Rank3 $rankBefore
 * @property Result3 $result
 * @property Rule3 $rule
 * @property TricolorRole3 $theirTeamRole
 * @property Splatfest3Theme $theirTeamTheme
 * @property TricolorRole3 $thirdTeamRole
 * @property Splatfest3Theme $thirdTeamTheme
 * @property User $user
 * @property AgentVariable3[] $variables
 * @property SplatoonVersion3 $version
 * @property Weapon3 $weapon
 */
class Battle3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'battle3';
    }

    public function rules()
    {
        return [
            [['uuid', 'client_uuid', 'user_id', 'remote_addr', 'remote_port', 'created_at', 'updated_at'], 'required'],
            [['uuid', 'client_uuid', 'note', 'private_note', 'link_url', 'remote_addr'], 'string'],
            [['user_id', 'lobby_id', 'rule_id', 'map_id', 'weapon_id', 'result_id', 'rank_in_team', 'kill', 'assist', 'kill_or_assist', 'death', 'special', 'inked', 'our_team_inked', 'their_team_inked', 'our_team_count', 'their_team_count', 'level_before', 'level_after', 'rank_before_id', 'rank_before_s_plus', 'rank_before_exp', 'rank_after_id', 'rank_after_s_plus', 'rank_after_exp', 'cash_before', 'cash_after', 'version_id', 'agent_id', 'period', 'remote_port', 'challenge_win', 'challenge_lose', 'rank_exp_change', 'clout_before', 'clout_after', 'clout_change', 'fest_dragon_id', 'our_team_role_id', 'their_team_role_id', 'third_team_role_id', 'our_team_theme_id', 'their_team_theme_id', 'third_team_theme_id', 'third_team_inked', 'signal'], 'default', 'value' => null],
            [['user_id', 'lobby_id', 'rule_id', 'map_id', 'weapon_id', 'result_id', 'rank_in_team', 'kill', 'assist', 'kill_or_assist', 'death', 'special', 'inked', 'our_team_inked', 'their_team_inked', 'our_team_count', 'their_team_count', 'level_before', 'level_after', 'rank_before_id', 'rank_before_s_plus', 'rank_before_exp', 'rank_after_id', 'rank_after_s_plus', 'rank_after_exp', 'cash_before', 'cash_after', 'version_id', 'agent_id', 'period', 'remote_port', 'challenge_win', 'challenge_lose', 'rank_exp_change', 'clout_before', 'clout_after', 'clout_change', 'fest_dragon_id', 'our_team_role_id', 'their_team_role_id', 'third_team_role_id', 'our_team_theme_id', 'their_team_theme_id', 'third_team_theme_id', 'third_team_inked', 'signal'], 'integer'],
            [['is_knockout', 'is_automated', 'use_for_entire', 'is_deleted', 'is_rank_up_battle', 'has_disconnect'], 'boolean'],
            [['our_team_percent', 'their_team_percent', 'fest_power', 'x_power_before', 'x_power_after', 'third_team_percent'], 'number'],
            [['start_at', 'end_at', 'created_at', 'updated_at'], 'safe'],
            [['our_team_color', 'their_team_color', 'third_team_color'], 'string', 'max' => 8],
            [['uuid'], 'unique'],
            [['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agent::class, 'targetAttribute' => ['agent_id' => 'id']],
            [['fest_dragon_id'], 'exist', 'skipOnError' => true, 'targetClass' => DragonMatch3::class, 'targetAttribute' => ['fest_dragon_id' => 'id']],
            [['lobby_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lobby3::class, 'targetAttribute' => ['lobby_id' => 'id']],
            [['map_id'], 'exist', 'skipOnError' => true, 'targetClass' => Map3::class, 'targetAttribute' => ['map_id' => 'id']],
            [['rank_before_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rank3::class, 'targetAttribute' => ['rank_before_id' => 'id']],
            [['rank_after_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rank3::class, 'targetAttribute' => ['rank_after_id' => 'id']],
            [['result_id'], 'exist', 'skipOnError' => true, 'targetClass' => Result3::class, 'targetAttribute' => ['result_id' => 'id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['our_team_theme_id'], 'exist', 'skipOnError' => true, 'targetClass' => Splatfest3Theme::class, 'targetAttribute' => ['our_team_theme_id' => 'id']],
            [['their_team_theme_id'], 'exist', 'skipOnError' => true, 'targetClass' => Splatfest3Theme::class, 'targetAttribute' => ['their_team_theme_id' => 'id']],
            [['third_team_theme_id'], 'exist', 'skipOnError' => true, 'targetClass' => Splatfest3Theme::class, 'targetAttribute' => ['third_team_theme_id' => 'id']],
            [['version_id'], 'exist', 'skipOnError' => true, 'targetClass' => SplatoonVersion3::class, 'targetAttribute' => ['version_id' => 'id']],
            [['our_team_role_id'], 'exist', 'skipOnError' => true, 'targetClass' => TricolorRole3::class, 'targetAttribute' => ['our_team_role_id' => 'id']],
            [['their_team_role_id'], 'exist', 'skipOnError' => true, 'targetClass' => TricolorRole3::class, 'targetAttribute' => ['their_team_role_id' => 'id']],
            [['third_team_role_id'], 'exist', 'skipOnError' => true, 'targetClass' => TricolorRole3::class, 'targetAttribute' => ['third_team_role_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['weapon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Weapon3::class, 'targetAttribute' => ['weapon_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uuid' => 'Uuid',
            'client_uuid' => 'Client Uuid',
            'user_id' => 'User ID',
            'lobby_id' => 'Lobby ID',
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
            'weapon_id' => 'Weapon ID',
            'result_id' => 'Result ID',
            'is_knockout' => 'Is Knockout',
            'rank_in_team' => 'Rank In Team',
            'kill' => 'Kill',
            'assist' => 'Assist',
            'kill_or_assist' => 'Kill Or Assist',
            'death' => 'Death',
            'special' => 'Special',
            'inked' => 'Inked',
            'our_team_inked' => 'Our Team Inked',
            'their_team_inked' => 'Their Team Inked',
            'our_team_percent' => 'Our Team Percent',
            'their_team_percent' => 'Their Team Percent',
            'our_team_count' => 'Our Team Count',
            'their_team_count' => 'Their Team Count',
            'level_before' => 'Level Before',
            'level_after' => 'Level After',
            'rank_before_id' => 'Rank Before ID',
            'rank_before_s_plus' => 'Rank Before S Plus',
            'rank_before_exp' => 'Rank Before Exp',
            'rank_after_id' => 'Rank After ID',
            'rank_after_s_plus' => 'Rank After S Plus',
            'rank_after_exp' => 'Rank After Exp',
            'cash_before' => 'Cash Before',
            'cash_after' => 'Cash After',
            'note' => 'Note',
            'private_note' => 'Private Note',
            'link_url' => 'Link Url',
            'version_id' => 'Version ID',
            'agent_id' => 'Agent ID',
            'is_automated' => 'Is Automated',
            'use_for_entire' => 'Use For Entire',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'period' => 'Period',
            'remote_addr' => 'Remote Addr',
            'remote_port' => 'Remote Port',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_deleted' => 'Is Deleted',
            'challenge_win' => 'Challenge Win',
            'challenge_lose' => 'Challenge Lose',
            'rank_exp_change' => 'Rank Exp Change',
            'is_rank_up_battle' => 'Is Rank Up Battle',
            'clout_before' => 'Clout Before',
            'clout_after' => 'Clout After',
            'clout_change' => 'Clout Change',
            'fest_dragon_id' => 'Fest Dragon ID',
            'fest_power' => 'Fest Power',
            'has_disconnect' => 'Has Disconnect',
            'x_power_before' => 'X Power Before',
            'x_power_after' => 'X Power After',
            'our_team_role_id' => 'Our Team Role ID',
            'their_team_role_id' => 'Their Team Role ID',
            'third_team_role_id' => 'Third Team Role ID',
            'our_team_color' => 'Our Team Color',
            'their_team_color' => 'Their Team Color',
            'third_team_color' => 'Third Team Color',
            'our_team_theme_id' => 'Our Team Theme ID',
            'their_team_theme_id' => 'Their Team Theme ID',
            'third_team_theme_id' => 'Third Team Theme ID',
            'third_team_inked' => 'Third Team Inked',
            'third_team_percent' => 'Third Team Percent',
            'signal' => 'Signal',
        ];
    }

    public function getAgent(): ActiveQuery
    {
        return $this->hasOne(Agent::class, ['id' => 'agent_id']);
    }

    public function getBattleAgentVariable3s(): ActiveQuery
    {
        return $this->hasMany(BattleAgentVariable3::class, ['battle_id' => 'id']);
    }

    public function getBattleImageGear3(): ActiveQuery
    {
        return $this->hasOne(BattleImageGear3::class, ['battle_id' => 'id']);
    }

    public function getBattleImageJudge3(): ActiveQuery
    {
        return $this->hasOne(BattleImageJudge3::class, ['battle_id' => 'id']);
    }

    public function getBattleImageResult3(): ActiveQuery
    {
        return $this->hasOne(BattleImageResult3::class, ['battle_id' => 'id']);
    }

    public function getBattleMedal3s(): ActiveQuery
    {
        return $this->hasMany(BattleMedal3::class, ['battle_id' => 'id']);
    }

    public function getBattlePlayer3s(): ActiveQuery
    {
        return $this->hasMany(BattlePlayer3::class, ['battle_id' => 'id']);
    }

    public function getBattleTricolorPlayer3s(): ActiveQuery
    {
        return $this->hasMany(BattleTricolorPlayer3::class, ['battle_id' => 'id']);
    }

    public function getFestDragon(): ActiveQuery
    {
        return $this->hasOne(DragonMatch3::class, ['id' => 'fest_dragon_id']);
    }

    public function getLobby(): ActiveQuery
    {
        return $this->hasOne(Lobby3::class, ['id' => 'lobby_id']);
    }

    public function getMap(): ActiveQuery
    {
        return $this->hasOne(Map3::class, ['id' => 'map_id']);
    }

    public function getMedals(): ActiveQuery
    {
        return $this->hasMany(Medal3::class, ['id' => 'medal_id'])->viaTable('battle_medal3', ['battle_id' => 'id']);
    }

    public function getOurTeamRole(): ActiveQuery
    {
        return $this->hasOne(TricolorRole3::class, ['id' => 'our_team_role_id']);
    }

    public function getOurTeamTheme(): ActiveQuery
    {
        return $this->hasOne(Splatfest3Theme::class, ['id' => 'our_team_theme_id']);
    }

    public function getRankAfter(): ActiveQuery
    {
        return $this->hasOne(Rank3::class, ['id' => 'rank_after_id']);
    }

    public function getRankBefore(): ActiveQuery
    {
        return $this->hasOne(Rank3::class, ['id' => 'rank_before_id']);
    }

    public function getResult(): ActiveQuery
    {
        return $this->hasOne(Result3::class, ['id' => 'result_id']);
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule3::class, ['id' => 'rule_id']);
    }

    public function getTheirTeamRole(): ActiveQuery
    {
        return $this->hasOne(TricolorRole3::class, ['id' => 'their_team_role_id']);
    }

    public function getTheirTeamTheme(): ActiveQuery
    {
        return $this->hasOne(Splatfest3Theme::class, ['id' => 'their_team_theme_id']);
    }

    public function getThirdTeamRole(): ActiveQuery
    {
        return $this->hasOne(TricolorRole3::class, ['id' => 'third_team_role_id']);
    }

    public function getThirdTeamTheme(): ActiveQuery
    {
        return $this->hasOne(Splatfest3Theme::class, ['id' => 'third_team_theme_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getVariables(): ActiveQuery
    {
        return $this->hasMany(AgentVariable3::class, ['id' => 'variable_id'])->viaTable('battle_agent_variable3', ['battle_id' => 'id']);
    }

    public function getVersion(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersion3::class, ['id' => 'version_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon3::class, ['id' => 'weapon_id']);
    }
}
