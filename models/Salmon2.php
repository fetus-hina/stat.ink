<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
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
 * @property SalmonBossAppearance2[] $bossAppearances
 * @property SalmonBoss2[] $bosses
 * @property SalmonPlayer2[] $players
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

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'uuid'], 'required'],
            [['user_id', 'splatnet_number', 'stage_id', 'clear_waves'], 'default', 'value' => null],
            [['fail_reason_id', 'title_before_id', 'title_before_exp'], 'default', 'value' => null],
            [['title_after_id', 'title_after_exp', 'shift_period'], 'default', 'value' => null],
            [['agent_id', 'remote_port'], 'default', 'value' => null],
            [['user_id', 'splatnet_number', 'stage_id', 'clear_waves'], 'integer'],
            [['fail_reason_id', 'title_before_id', 'title_before_exp', 'title_after_id'], 'integer'],
            [['title_after_exp', 'shift_period', 'agent_id', 'remote_port'], 'integer'],
            [['uuid', 'note', 'private_note', 'link_url', 'remote_addr'], 'string'],
            [['danger_rate'], 'number'],
            [['start_at', 'end_at', 'created_at', 'updated_at'], 'safe'],
            [['is_automated'], 'boolean'],
            [['agent_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Agent::class,
                'targetAttribute' => ['agent_id' => 'id'],
            ],
            [['fail_reason_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonFailReason2::class,
                'targetAttribute' => ['fail_reason_id' => 'id'],
            ],
            [['stage_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonMap2::class,
                'targetAttribute' => ['stage_id' => 'id'],
            ],
            [['title_before_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonTitle2::class,
                'targetAttribute' => ['title_before_id' => 'id'],
            ],
            [['title_after_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonTitle2::class,
                'targetAttribute' => ['title_after_id' => 'id'],
            ],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
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
            'uuid' => 'Uuid',
            'splatnet_number' => Yii::t('app', 'SplatNet #'),
            'stage_id' => Yii::t('app', 'Stage'),
            'clear_waves' => 'Clear Waves',
            'fail_reason_id' => 'Fail Reason ID',
            'title_before_id' => 'Title Before ID',
            'title_before_exp' => 'Title Before Exp',
            'title_after_id' => 'Title After ID',
            'title_after_exp' => 'Title After Exp',
            'danger_rate' => Yii::t('app-salmon2', 'Hazard Level'),
            'shift_period' => Yii::t('app-salmon2', 'Shift'),
            'start_at' => Yii::t('app-salmon2', 'Work Started'),
            'end_at' => Yii::t('app-salmon2', 'Work Ended'),
            'note' => Yii::t('app', 'Note'),
            'private_note' => Yii::t('app', 'Note (private)'),
            'link_url' => Yii::t('app-salmon2', 'URL related to this work'),
            'is_automated' => 'Is Automated',
            'agent_id' => Yii::t('app', 'User Agent'),
            'remote_addr' => 'Remote Addr',
            'remote_port' => 'Remote Port',
            'created_at' => Yii::t('app', 'Data Sent'),
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

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getBossAppearances(): ActiveQuery
    {
        return $this->hasMany(SalmonBossAppearance2::class, ['salmon_id' => 'id']);
    }

    public function getBosses(): ActiveQuery
    {
        return $this->hasMany(SalmonBoss2::class, ['id' => 'boss_id'])
            ->viaTable('salmon_boss_appearance2', ['salmon_id' => 'id']);
    }

    public function getWaves(): ActiveQuery
    {
        return $this->hasMany(SalmonWave2::class, ['salmon_id' => 'id'])
            ->orderBy(['salmon_wave2.wave' => SORT_ASC]);
    }

    public function getSalmonWave2s(): ActiveQuery
    {
        return $this->getWaves();
    }

    public function getPlayers(): ActiveQuery
    {
        return $this->hasMany(SalmonPlayer2::class, ['work_id' => 'id'])
            ->with([
                'bossKills',
                'forceBlackout',
                'gender',
                'special',
                'specialUses',
                'species',
                'weapons',
            ])
            ->orderBy(['salmon_player2.id' => SORT_ASC]);
    }

    public function getMyData(): ?SalmonPlayer2
    {
        foreach ($this->players as $player) {
            return $player;
        }

        return null;
    }

    public function getTeamMates(): ?array
    {
        if (!$list = $this->players) {
            return null;
        }

        return array_filter($this->players, function (SalmonPlayer2 $player): bool {
            return !$player->is_me;
        });
    }

    public function getIsCleared(): ?bool
    {
        if ($this->clear_waves === null) {
            return null;
        }

        return $this->clear_waves >= 3;
    }

    public function getIsFailed(): ?bool
    {
        $cleared = $this->getIsCleared();
        if ($cleared === null) {
            return null;
        }

        return !$cleared;
    }

    public function getQuota(): ?array
    {
        if ($this->danger_rate === null) {
            return null;
        }
        $danger = (float)$this->danger_rate;
        $data = [
            [200,   [21, 23, 25]],
            [189,   [20, 22, 24]],
            [187.6, [20, 21, 23]],
            [177.8, [19, 21, 23]],
            [175,   [19, 20, 22]],
            [166.8, [18, 20, 22]],
            [162.6, [18, 19, 21]],
            [155.6, [17, 19, 21]],
            [150,   [17, 18, 20]],
            [144.6, [16, 18, 20]],
            [137.6, [16, 17, 19]],
            [133.4, [15, 17, 19]],
            [125,   [15, 16, 18]],
            [122.4, [14, 16, 18]],
            [112.6, [14, 15, 17]],
            [111.2, [13, 15, 17]],
            [100,   [13, 14, 16]],
            [ 93.4, [12, 13, 15]],
            [ 86.8, [11, 12, 14]],
            [ 80,   [10, 11, 13]],
            [ 70,   [ 9, 10, 12]],
            [ 60,   [ 8,  9, 11]],
            [ 40,   [ 7,  8, 10]],
            [ 30,   [ 6,  7,  9]],
            [ 20,   [ 6,  7,  8]],
            [ 14,   [ 5,  6,  7]],
            [  8,   [ 4,  5,  6]],
            [  4,   [ 3,  4,  5]],
            [  0,   [ 2,  3,  4]],
        ];

        foreach ($data as $_) {
            list($minDanger, $quota) = $_;
            if ($minDanger <= $danger) {
                return $quota;
            }
        }

        // Why!?
        return null;
    }
}
