<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon3".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $uuid
 * @property string $client_uuid
 * @property boolean $is_big_run
 * @property integer $stage_id
 * @property integer $big_stage_id
 * @property string $danger_rate
 * @property integer $clear_waves
 * @property integer $fail_reason_id
 * @property integer $king_smell
 * @property integer $king_salmonid_id
 * @property boolean $clear_extra
 * @property integer $title_before_id
 * @property integer $title_exp_before
 * @property integer $title_after_id
 * @property integer $title_exp_after
 * @property integer $golden_eggs
 * @property integer $power_eggs
 * @property integer $gold_scale
 * @property integer $silver_scale
 * @property integer $bronze_scale
 * @property integer $job_point
 * @property integer $job_score
 * @property string $job_rate
 * @property integer $job_bonus
 * @property string $note
 * @property string $private_note
 * @property string $link_url
 * @property integer $version_id
 * @property integer $agent_id
 * @property boolean $is_automated
 * @property string $start_at
 * @property string $end_at
 * @property integer $period
 * @property integer $schedule_id
 * @property boolean $has_disconnect
 * @property boolean $is_deleted
 * @property string $remote_addr
 * @property integer $remote_port
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $is_private
 * @property boolean $has_broken_data
 * @property string $scenario_code
 * @property boolean $is_eggstra_work
 *
 * @property Agent $agent
 * @property Map3 $bigStage
 * @property SalmonBoss3[] $bosses
 * @property SalmonFailReason2 $failReason
 * @property SalmonKing3 $kingSalmonid
 * @property SalmonAgentVariable3[] $salmonAgentVariable3s
 * @property SalmonBossAppearance3[] $salmonBossAppearance3s
 * @property SalmonExportJson3[] $salmonExportJson3s
 * @property SalmonPlayer3[] $salmonPlayer3s
 * @property SalmonWave3[] $salmonWave3s
 * @property SalmonSchedule3 $schedule
 * @property SalmonMap3 $stage
 * @property SalmonTitle3 $titleAfter
 * @property SalmonTitle3 $titleBefore
 * @property User $user
 * @property AgentVariable3[] $variables
 * @property SplatoonVersion3 $version
 */
class Salmon3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon3';
    }

    public function rules()
    {
        return [
            [['user_id', 'uuid', 'client_uuid', 'is_big_run', 'is_automated', 'period', 'remote_addr', 'remote_port', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'stage_id', 'big_stage_id', 'clear_waves', 'fail_reason_id', 'king_smell', 'king_salmonid_id', 'title_before_id', 'title_exp_before', 'title_after_id', 'title_exp_after', 'golden_eggs', 'power_eggs', 'gold_scale', 'silver_scale', 'bronze_scale', 'job_point', 'job_score', 'job_bonus', 'version_id', 'agent_id', 'period', 'schedule_id', 'remote_port'], 'default', 'value' => null],
            [['user_id', 'stage_id', 'big_stage_id', 'clear_waves', 'fail_reason_id', 'king_smell', 'king_salmonid_id', 'title_before_id', 'title_exp_before', 'title_after_id', 'title_exp_after', 'golden_eggs', 'power_eggs', 'gold_scale', 'silver_scale', 'bronze_scale', 'job_point', 'job_score', 'job_bonus', 'version_id', 'agent_id', 'period', 'schedule_id', 'remote_port'], 'integer'],
            [['uuid', 'client_uuid', 'note', 'private_note', 'link_url', 'remote_addr'], 'string'],
            [['is_big_run', 'clear_extra', 'is_automated', 'has_disconnect', 'is_deleted', 'is_private', 'has_broken_data', 'is_eggstra_work'], 'boolean'],
            [['danger_rate', 'job_rate'], 'number'],
            [['start_at', 'end_at', 'created_at', 'updated_at'], 'safe'],
            [['scenario_code'], 'string', 'max' => 16],
            [['uuid'], 'unique'],
            [['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agent::class, 'targetAttribute' => ['agent_id' => 'id']],
            [['big_stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => Map3::class, 'targetAttribute' => ['big_stage_id' => 'id']],
            [['fail_reason_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonFailReason2::class, 'targetAttribute' => ['fail_reason_id' => 'id']],
            [['king_salmonid_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonKing3::class, 'targetAttribute' => ['king_salmonid_id' => 'id']],
            [['stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonMap3::class, 'targetAttribute' => ['stage_id' => 'id']],
            [['schedule_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonSchedule3::class, 'targetAttribute' => ['schedule_id' => 'id']],
            [['title_before_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonTitle3::class, 'targetAttribute' => ['title_before_id' => 'id']],
            [['title_after_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonTitle3::class, 'targetAttribute' => ['title_after_id' => 'id']],
            [['version_id'], 'exist', 'skipOnError' => true, 'targetClass' => SplatoonVersion3::class, 'targetAttribute' => ['version_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'uuid' => 'Uuid',
            'client_uuid' => 'Client Uuid',
            'is_big_run' => 'Is Big Run',
            'stage_id' => 'Stage ID',
            'big_stage_id' => 'Big Stage ID',
            'danger_rate' => 'Danger Rate',
            'clear_waves' => 'Clear Waves',
            'fail_reason_id' => 'Fail Reason ID',
            'king_smell' => 'King Smell',
            'king_salmonid_id' => 'King Salmonid ID',
            'clear_extra' => 'Clear Extra',
            'title_before_id' => 'Title Before ID',
            'title_exp_before' => 'Title Exp Before',
            'title_after_id' => 'Title After ID',
            'title_exp_after' => 'Title Exp After',
            'golden_eggs' => 'Golden Eggs',
            'power_eggs' => 'Power Eggs',
            'gold_scale' => 'Gold Scale',
            'silver_scale' => 'Silver Scale',
            'bronze_scale' => 'Bronze Scale',
            'job_point' => 'Job Point',
            'job_score' => 'Job Score',
            'job_rate' => 'Job Rate',
            'job_bonus' => 'Job Bonus',
            'note' => 'Note',
            'private_note' => 'Private Note',
            'link_url' => 'Link Url',
            'version_id' => 'Version ID',
            'agent_id' => 'Agent ID',
            'is_automated' => 'Is Automated',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'period' => 'Period',
            'schedule_id' => 'Schedule ID',
            'has_disconnect' => 'Has Disconnect',
            'is_deleted' => 'Is Deleted',
            'remote_addr' => 'Remote Addr',
            'remote_port' => 'Remote Port',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'is_private' => 'Is Private',
            'has_broken_data' => 'Has Broken Data',
            'scenario_code' => 'Scenario Code',
            'is_eggstra_work' => 'Is Eggstra Work',
        ];
    }

    public function getAgent(): ActiveQuery
    {
        return $this->hasOne(Agent::class, ['id' => 'agent_id']);
    }

    public function getBigStage(): ActiveQuery
    {
        return $this->hasOne(Map3::class, ['id' => 'big_stage_id']);
    }

    public function getBosses(): ActiveQuery
    {
        return $this->hasMany(SalmonBoss3::class, ['id' => 'boss_id'])->viaTable('salmon_boss_appearance3', ['salmon_id' => 'id']);
    }

    public function getFailReason(): ActiveQuery
    {
        return $this->hasOne(SalmonFailReason2::class, ['id' => 'fail_reason_id']);
    }

    public function getKingSalmonid(): ActiveQuery
    {
        return $this->hasOne(SalmonKing3::class, ['id' => 'king_salmonid_id']);
    }

    public function getSalmonAgentVariable3s(): ActiveQuery
    {
        return $this->hasMany(SalmonAgentVariable3::class, ['salmon_id' => 'id']);
    }

    public function getSalmonBossAppearance3s(): ActiveQuery
    {
        return $this->hasMany(SalmonBossAppearance3::class, ['salmon_id' => 'id']);
    }

    public function getSalmonExportJson3s(): ActiveQuery
    {
        return $this->hasMany(SalmonExportJson3::class, ['last_battle_id' => 'id']);
    }

    public function getSalmonPlayer3s(): ActiveQuery
    {
        return $this->hasMany(SalmonPlayer3::class, ['salmon_id' => 'id']);
    }

    public function getSalmonWave3s(): ActiveQuery
    {
        return $this->hasMany(SalmonWave3::class, ['salmon_id' => 'id']);
    }

    public function getSchedule(): ActiveQuery
    {
        return $this->hasOne(SalmonSchedule3::class, ['id' => 'schedule_id']);
    }

    public function getStage(): ActiveQuery
    {
        return $this->hasOne(SalmonMap3::class, ['id' => 'stage_id']);
    }

    public function getTitleAfter(): ActiveQuery
    {
        return $this->hasOne(SalmonTitle3::class, ['id' => 'title_after_id']);
    }

    public function getTitleBefore(): ActiveQuery
    {
        return $this->hasOne(SalmonTitle3::class, ['id' => 'title_before_id']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getVariables(): ActiveQuery
    {
        return $this->hasMany(AgentVariable3::class, ['id' => 'variable_id'])->viaTable('salmon_agent_variable3', ['salmon_id' => 'id']);
    }

    public function getVersion(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersion3::class, ['id' => 'version_id']);
    }
}
