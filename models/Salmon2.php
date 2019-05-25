<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\behaviors\TimestampBehavior;
use app\components\helpers\Battle as BattleHelper;
use app\components\helpers\DateTimeFormatter;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

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
    public static function getRoughCount(): ?int
    {
        try {
            return (new Query())
                ->select('[[last_value]]')
                ->from('{{salmon2_id_seq}}')
                ->scalar();
        } catch (Exception $e) {
            return null;
        }
    }

    public static function find(): ActiveQuery
    {
        return new Salmon2Query(static::class);
    }

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
            'shift_period' => Yii::t('app-salmon2', 'Rotation'),
            'start_at' => Yii::t('app-salmon2', 'Job Started'),
            'end_at' => Yii::t('app-salmon2', 'Job Ended'),
            'note' => Yii::t('app', 'Note'),
            'private_note' => Yii::t('app', 'Note (private)'),
            'link_url' => Yii::t('app-salmon2', 'URL related to this job'),
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
        return $this->hasMany(SalmonBossAppearance2::class, ['salmon_id' => 'id'])
            ->with('boss');
    }

    public function getBosses(): ActiveQuery
    {
        return $this->hasMany(SalmonBoss2::class, ['id' => 'boss_id'])
            ->viaTable('salmon_boss_appearance2', ['salmon_id' => 'id']);
    }

    public function getWaves(): ActiveQuery
    {
        return $this->hasMany(SalmonWave2::class, ['salmon_id' => 'id'])
            ->orderBy(['salmon_wave2.wave' => SORT_ASC])
            ->with(['event', 'water']);
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
            if ($player->is_me) {
                return $player;
            }
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

    public function getPrevious(): ?self
    {
        return static::find()
            ->andWhere(['and',
                ['user_id' => $this->user_id],
                ['<', 'id', $this->id],
            ])
            ->orderBy(['id' => SORT_DESC])
            ->limit(1)
            ->one();
    }

    public function getNext(): ?self
    {
        return static::find()
            ->andWhere(['and',
                ['user_id' => $this->user_id],
                ['>', 'id', $this->id],
            ])
            ->orderBy(['id' => SORT_ASC])
            ->limit(1)
            ->one();
    }

    public function getPreviousBySplatNetNumber(): ?self
    {
        $subQuery = (new Query())
            ->select([
                'id' => 'salmon_old.id',
            ])
            ->from('salmon2 salmon_old')
            ->innerJoin('salmon_player2 player_old', implode(' AND ', [
                'salmon_old.id = player_old.work_id',
                'player_old.is_me = TRUE',
            ]))
            ->innerJoin('salmon2 salmon_new', implode(' AND ', [
                'salmon_old.user_id = salmon_new.user_id',
                'salmon_old.splatnet_number + 1 = salmon_new.splatnet_number',
                'salmon_old.stage_id = salmon_new.stage_id',
                'salmon_old.shift_period = salmon_new.shift_period',
            ]))
            ->innerJoin('salmon_player2 player_new', implode(' AND ', [
                'salmon_new.id = player_new.work_id',
                'player_new.is_me = TRUE',
                'player_old.splatnet_id = player_new.splatnet_id',
            ]))
            ->where(['salmon_new.id' => $this->id]);

        return static::find()
            ->andWhere(['id' => $subQuery])
            ->limit(1)
            ->one();
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

    public function getPlayWaves(): ?int
    {
        if ($this->clear_waves === null) {
            return null;
        }

        return min(3, $this->clear_waves + 1);
    }

    public function getGoldenPerWave(): ?float
    {
        $myData = $this->getMyData();
        $waves = $this->getPlayWaves();
        if (!$myData || !$waves) {
            return null;
        }

        return (int)$myData->golden_egg_delivered / $waves;
    }

    public function getPwrEggsPerWave(): ?float
    {
        $myData = $this->getMyData();
        $waves = $this->getPlayWaves();
        if (!$myData || !$waves) {
            return null;
        }

        return (int)$myData->power_egg_collected / $waves;
    }

    public function getTeamTotalGoldenEggs(): ?int
    {
        if (!$this->waves) {
            return null;
        }

        return array_reduce(
            $this->waves,
            function (?int $carry, SalmonWave2 $item): ?int {
                if ($carry === null || $item->golden_egg_delivered === null) {
                    return null;
                }
                return $carry + $item->golden_egg_delivered;
            },
            0
        );
    }

    public function getTeamTotalGoldenEggsPerWave(): ?array
    {
        if (!$this->waves) {
            return null;
        }

        return array_map(
            function (SalmonWave2 $item): ?\stdClass {
                if ($item->golden_egg_delivered === null) {
                    return null;
                }

                return (object)[
                    'quota' => $item->golden_egg_quota,
                    'delivered' => $item->golden_egg_delivered,
                ];
            },
            $this->waves
        );
    }

    public function getTeamTotalPowerEggs(): ?int
    {
        if (!$this->waves) {
            return null;
        }

        return array_reduce(
            $this->waves,
            function (?int $carry, SalmonWave2 $item): ?int {
                if ($carry === null || $item->power_egg_collected === null) {
                    return null;
                }
                return $carry + $item->power_egg_collected;
            },
            0
        );
    }

    public function getTeamTotalPowerEggsPerWave(): ?array
    {
        if (!$this->waves) {
            return null;
        }

        return array_map(
            function (SalmonWave2 $item): ?int {
                return $item->power_egg_collected;
            },
            $this->waves
        );
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

    public function toJsonArray(): array
    {
        $isCleared = ($this->clear_waves === null) ? null : ($this->clear_waves >= 3);
        $gender = null;
        if ($myData = $this->getMyData()) {
            $gender = $myData->gender;
        }

        return [
            'id' => (int)$this->id,
            'uuid' => $this->uuid,
            'splatnet_number' => (int)$this->splatnet_number,
            'url' => Url::to(['salmon/view',
                'screen_name' => $this->user->screen_name,
                'id' => $this->id,
            ], true),
            'api_endpoint' => Url::to(['api-v2-salmon/view',
                'id' => $this->id
            ], true),
            'user' => $this->user->toSalmonJsonArray(),
            'stage' => $this->stage_id ? $this->stage->toJsonArray() : null,
            'is_cleared' => $isCleared,
            'fail_reason' => ($isCleared === false && $this->fail_reason_id)
                ? $this->failReason->toJsonArray()
                : null,
            'clear_waves' => $this->clear_waves,
            'danger_rate' => $this->danger_rate,
            'quota' => $this->getQuota(),
            'title' => $this->title_before_id ? $this->titleBefore->toJsonArray($gender) : null,
            'title_exp' => $this->title_before_exp,
            'title_after' => $this->title_after_id ? $this->titleAfter->toJsonArray($gender) : null,
            'title_exp_after' => $this->title_after_exp,
            'boss_appearances' => $this->getBossAppearancesMap(),
            'waves' => $this->getWavesMap(),
            'my_data' => $myData ? $myData->toJsonArray() : null,
            'teammates' => $this->getTeammatesMap(),
            'agent' => [
                'name' => $this->agent ? $this->agent->name : null,
                'version' => $this->agent ? $this->agent->version : null,
            ],
            'automated' => !!$this->is_automated,
            'note' => ((string)$this->note !== '') ? $this->note : null,
            'link_url' => ((string)$this->link_url !== '') ? $this->link_url : null,
            'shift_start_at' => $this->shift_period
                ? DateTimeFormatter::unixTimeToJsonArray(
                    BattleHelper::periodToRange2($this->shift_period)[0]
                )
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

    public static function csvArraySchema(): array
    {
        return ArrayHelper::toFlatten(array_merge(
            [
                'statink_id',
                'rotation_period',
                'shift_start',
                'shift_start',
                'splatnet_number',
                'stage_key',
                'stage_name',
                'clear_wave',
                'fail_reason',
                'fail_reason',
                'hazard_level',
                'title_before',
                'title_before',
                'title_before',
                'title_after',
                'title_after',
                'title_after',
            ],
            array_map(function (int $wave): array {
                return [
                    "w{$wave}_event",
                    "w{$wave}_event",
                    "w{$wave}_water",
                    "w{$wave}_water",
                    "w{$wave}_quota",
                    "w{$wave}_delivers",
                    "w{$wave}_appearances",
                    "w{$wave}_pwr_eggs",
                ];
            }, range(1, 3)),
            array_map(function (int $i): array {
                $prefix = $i === 0 ? 'player' : "mate{$i}";
                return [
                    "{$prefix}_id",
                    "{$prefix}_name",
                    "{$prefix}_w1_weapon",
                    "{$prefix}_w1_weapon",
                    "{$prefix}_w2_weapon",
                    "{$prefix}_w2_weapon",
                    "{$prefix}_w3_weapon",
                    "{$prefix}_w3_weapon",
                    "{$prefix}_special",
                    "{$prefix}_special",
                    "{$prefix}_w1_sp_use",
                    "{$prefix}_w2_sp_use",
                    "{$prefix}_w3_sp_use",
                    "{$prefix}_rescues",
                    "{$prefix}_rescued",
                    "{$prefix}_golden_eggs",
                    "{$prefix}_power_eggs",
                ];
            }, range(0, 3)),
            array_map(function (SalmonBoss2 $boss): array {
                $prefix = preg_replace('/[^a-z0-9]+/', '_', strtolower($boss->name));
                return [
                    "{$prefix}_appearances",
                    "{$prefix}_player_kills",
                    "{$prefix}_mate1_kills",
                    "{$prefix}_mate2_kills",
                    "{$prefix}_mate3_kills",
                ];
            }, SalmonBoss2::find()->orderBy(['name' => SORT_ASC])->all()),
        ));
    }

    public function toCSVArray(): array
    {
        $gender = null;
        if ($myData = $this->getMyData()) {
            $gender = $myData->gender;
        }

        static $bosses = null;
        if (!$bosses) {
            $bosses = SalmonBoss2::find()->orderBy(['name' => SORT_ASC])->all();
        }
        if (!$this->bossAppearances) {
            $bossAppearances = [];
        } else {
            $bossAppearances = ArrayHelper::map($this->bossAppearances, 'boss_id', 'count');
        }

        return ArrayHelper::toFlatten(array_merge(
            [
                (string)$this->id,
                (string)$this->shift_period,
                $this->start_at ? (string)strtotime($this->start_at) : '',
                $this->start_at ? date(DATE_ATOM, strtotime($this->start_at)) : '',
                (string)$this->splatnet_number,
                $this->stage_id ? $this->stage->key : '',
                $this->stage_id ? Yii::t('app-salmon-map2', $this->stage->name) : '',
                (string)$this->clear_waves,
                ($this->clear_waves === null || $this->clear_waves >= 3 || !$this->fail_reason_id)
                    ? ''
                    : $this->failReason->key,
                ($this->clear_waves === null || $this->clear_waves >= 3 || !$this->fail_reason_id)
                    ? ''
                    : Yii::t('app-salmon2', $this->failReason->name),
                $this->danger_rate ? sprintf('%.1f', $this->danger_rate) : '',
                $this->title_before_id ? $this->titleBefore->key : '',
                $this->title_before_id ? $this->titleBefore->getTranslatedName($gender) : '',
                $this->title_before_exp,
                $this->title_after_id ? $this->titleAfter->key : '',
                $this->title_after_id ? $this->titleAfter->getTranslatedName($gender) : '',
                $this->title_after_exp,
            ],
            array_map(function (int $w): array {
                if (!$wave = $this->waves[$w - 1] ?? null) {
                    $quotas = $this->getQuota();
                    return [
                        '', // event
                        '', // event
                        '', // water
                        '', // water
                        $quotas ? $quotas[$w - 1] : '',
                        '', // delivers
                        '', // appearances
                        '', // power eggs
                    ];
                }

                return [
                    $wave->event_id ? $wave->event->key : '',
                    $wave->event_id ? Yii::t('app-salmon-event2', $wave->event->name) : '',
                    $wave->water_id ? $wave->water->key : '',
                    $wave->water_id ? Yii::t('app-salmon-tide2', $wave->water->name) : '',
                    $wave->golden_egg_quota,
                    $wave->golden_egg_appearances,
                    $wave->golden_egg_delivered,
                    $wave->power_egg_collected,
                ];
            }, range(1, 3)),
            array_map(function (int $i): array {
                $p = ($i === 0)
                    ? $this->myData
                    : $this->teamMates[$i] ?? null;
                $weapons = $p ? $p->weapons : [];
                $spUses = $p ? $p->specialUses : [];

                return [
                    $p->splatnet_id ?? '',
                    $p->name ?? '',
                    $weapons[0]->weapon->key ?? '',
                    Yii::t('app-weapon2', $weapons[0]->weapon->name ?? ''),
                    $weapons[1]->weapon->key ?? '',
                    Yii::t('app-weapon2', $weapons[1]->weapon->name ?? ''),
                    $weapons[2]->weapon->key ?? '',
                    Yii::t('app-weapon2', $weapons[2]->weapon->name ?? ''),
                    $p->special->key ?? '',
                    Yii::t('app-special2', $p->special->name ?? ''),
                    $spUses[0]->count ?? '',
                    $spUses[1]->count ?? '',
                    $spUses[2]->count ?? '',
                    $p->rescue ?? '',
                    $p->death ?? '',
                    $p->golden_egg_delivered ?? '',
                    $p->power_egg_collected ?? '',
                ];
            }, range(0, 3)),
            array_map(function (SalmonBoss2 $boss) use ($bossAppearances): array {
                return [
                    (int)ArrayHelper::getValue($bossAppearances, $boss->id, 0),
                    '', // player
                    '', // mate1
                    '', // mate2
                    '', // mate3
                ];
            }, $bosses),
        ));
    }

    public function getBossAppearancesMap(): ?array
    {
        if (!$this->bossAppearances) {
            return null;
        }

        return array_map(
            function (SalmonBossAppearance2 $bossAppearance): array {
                return $bossAppearance->toJsonArray();
            },
            $this->bossAppearances
        );
    }

    public function getWavesMap(): ?array
    {
        if (!$this->waves) {
            return null;
        }

        return array_map(
            function (SalmonWave2 $wave): array {
                return $wave->toJsonArray();
            },
            $this->waves
        );
    }

    public function getTeammatesMap(): ?array
    {
        if (!$list = $this->getTeamMates()) {
            return null;
        }

        return array_map(
            function (SalmonPlayer2 $player): array {
                return $player->toJsonArray();
            },
            array_values($list)
        );
    }

    public function getIsEditable(): bool
    {
        if (!$loggedIn = Yii::$app->user->identity) {
            return false;
        }

        return (int)$loggedIn->id === (int)$this->user_id;
    }

    public function getCreatedAt(): int
    {
        return (new DateTimeImmutable($this->created_at))->getTimestamp();
    }

    public function delete()
    {
        return Yii::$app->db->transactionEx(function (): bool {
            $profile = "Delete salmon2 (id={$this->id})";
            Yii::beginProfile($profile, __METHOD__);

            // delete related tables
            foreach ($this->bossAppearances as $_) {
                if (!$_->delete()) {
                    return false;
                }
            }

            foreach ($this->players as $_) {
                if (!$_->delete()) {
                    return false;
                }
            }

            foreach ($this->waves as $_) {
                if (!$_->delete()) {
                    return false;
                }
            }

            // delete me
            $result = !!parent::delete();
            Yii::endProfile($profile, __METHOD__);
            return $result;
        });
    }
}
