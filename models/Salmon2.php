<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon2".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $uuid
 * @property integer $splatnet_number
 * @property integer $stage_id
 * @property integer $clear_waves
 * @property integer $fail_reason_id
 * @property integer $title_before_id
 * @property integer $title_before_exp
 * @property integer $title_after_id
 * @property integer $title_after_exp
 * @property string $danger_rate
 * @property integer $shift_period
 * @property string $start_at
 * @property string $end_at
 * @property string $note
 * @property string $private_note
 * @property string $link_url
 * @property boolean $is_automated
 * @property integer $agent_id
 * @property string $remote_addr
 * @property integer $remote_port
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Agent $agent
 * @property SalmonFailReason2 $failReason
 * @property SalmonMap2 $stage
 * @property SalmonTitle2 $titleBefore
 * @property SalmonTitle2 $titleAfter
 * @property User $user
 * @property SalmonBossAppearance2[] $salmonBossAppearance2s
 * @property SalmonBoss2[] $bosses
 * @property SalmonWave2[] $salmonWave2s
 */
class Salmon2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salmon2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'uuid', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'splatnet_number', 'stage_id', 'clear_waves', 'fail_reason_id', 'title_before_id', 'title_before_exp', 'title_after_id', 'title_after_exp', 'shift_period', 'agent_id', 'remote_port'], 'default', 'value' => null],
            [['user_id', 'splatnet_number', 'stage_id', 'clear_waves', 'fail_reason_id', 'title_before_id', 'title_before_exp', 'title_after_id', 'title_after_exp', 'shift_period', 'agent_id', 'remote_port'], 'integer'],
            [['uuid', 'note', 'private_note', 'link_url', 'remote_addr'], 'string'],
            [['danger_rate'], 'number'],
            [['start_at', 'end_at', 'created_at', 'updated_at'], 'safe'],
            [['is_automated'], 'boolean'],
            [['agent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Agent::class, 'targetAttribute' => ['agent_id' => 'id']],
            [['fail_reason_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonFailReason2::class, 'targetAttribute' => ['fail_reason_id' => 'id']],
            [['stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonMap2::class, 'targetAttribute' => ['stage_id' => 'id']],
            [['title_before_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonTitle2::class, 'targetAttribute' => ['title_before_id' => 'id']],
            [['title_after_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonTitle2::class, 'targetAttribute' => ['title_after_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'uuid' => 'Uuid',
            'splatnet_number' => 'Splatnet Number',
            'stage_id' => 'Stage ID',
            'clear_waves' => 'Clear Waves',
            'fail_reason_id' => 'Fail Reason ID',
            'title_before_id' => 'Title Before ID',
            'title_before_exp' => 'Title Before Exp',
            'title_after_id' => 'Title After ID',
            'title_after_exp' => 'Title After Exp',
            'danger_rate' => 'Danger Rate',
            'shift_period' => 'Shift Period',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'note' => 'Note',
            'private_note' => 'Private Note',
            'link_url' => 'Link Url',
            'is_automated' => 'Is Automated',
            'agent_id' => 'Agent ID',
            'remote_addr' => 'Remote Addr',
            'remote_port' => 'Remote Port',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgent()
    {
        return $this->hasOne(Agent::class, ['id' => 'agent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFailReason()
    {
        return $this->hasOne(SalmonFailReason2::class, ['id' => 'fail_reason_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStage()
    {
        return $this->hasOne(SalmonMap2::class, ['id' => 'stage_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitleBefore()
    {
        return $this->hasOne(SalmonTitle2::class, ['id' => 'title_before_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitleAfter()
    {
        return $this->hasOne(SalmonTitle2::class, ['id' => 'title_after_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalmonBossAppearance2s()
    {
        return $this->hasMany(SalmonBossAppearance2::class, ['salmon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBosses()
    {
        return $this->hasMany(SalmonBoss2::class, ['id' => 'boss_id'])->viaTable('salmon_boss_appearance2', ['salmon_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSalmonWave2s()
    {
        return $this->hasMany(SalmonWave2::class, ['salmon_id' => 'id']);
    }
}
