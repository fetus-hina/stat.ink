<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\behaviors\TimestampBehavior;
use app\components\behaviors\RemoteAddrBehavior;
use app\components\behaviors\RemotePortBehavior;
use jp3cki\uuid\Uuid;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "battle2".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $lobby_id
 * @property integer $mode_id
 * @property integer $rule_id
 * @property integer $map_id
 * @property integer $weapon_id
 * @property boolean $is_win
 * @property boolean $is_knockout
 * @property integer $level
 * @property integer $level_after
 * @property integer $rank_id
 * @property integer $rank_exp
 * @property integer $rank_after_id
 * @property integer $rank_after_exp
 * @property integer $rank_in_team
 * @property integer $kill
 * @property integer $death
 * @property string $kill_ratio
 * @property string $kill_rate
 * @property integer $max_kill_combo
 * @property integer $max_kill_streak
 * @property integer $my_point
 * @property integer $my_team_point
 * @property integer $his_team_point
 * @property string $my_team_percent
 * @property string $his_team_percent
 * @property integer $my_team_count
 * @property integer $his_team_count
 * @property string $my_team_color_hue
 * @property string $his_team_color_hue
 * @property string $my_team_color_rgb
 * @property string $his_team_color_rgb
 * @property integer $cash
 * @property integer $cash_after
 * @property string $note
 * @property string $private_note
 * @property string $link_url
 * @property integer $period
 * @property integer $version_id
 * @property integer $bonus_id
 * @property integer $env_id
 * @property string $client_uuid
 * @property string $ua_variables
 * @property string $ua_custom
 * @property integer $agent_game_version_id
 * @property string $agent_game_version_date
 * @property integer $agent_id
 * @property boolean $is_automated
 * @property boolean $use_for_entire
 * @property string $remote_addr
 * @property integer $remote_port
 * @property string $start_at
 * @property string $end_at
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Agent $agent
 * @property Environment $env
 * @property Lobby2 $lobby
 * @property Map2 $map
 * @property Mode2 $mode
 * @property Rank2 $rank
 * @property Rank2 $rankAfter
 * @property Rule2 $rule
 * @property SplatoonVersion2 $version
 * @property SplatoonVersion2 $agentGameVersion
 * @property TurfwarWinBonus2 $bonus
 * @property User $user
 * @property Weapon2 $weapon
 */
class Battle2 extends ActiveRecord
{
    const CLIENT_UUID_NAMESPACE = '15de9082-1c7b-11e7-8f94-001b21a098c2';

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            RemoteAddrBehavior::class,
            RemotePortBehavior::class,
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
        ];
    }

    public static function createClientUuid($value) : string
    {
        if (!is_scalar($value)) {
            return Uuid::v4()->formatAsString();
        }
        $value = trim((string)$value);
        if ($value === '') {
            return Uuid::v4()->formatAsString();
        }
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value)) {
            return strtolower($value);
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
            [['env_id', 'agent_game_version_id', 'agent_id', 'remote_port'], 'integer'],
            [['is_win', 'is_knockout', 'is_automated', 'use_for_entire'], 'boolean'],
            [['kill_ratio', 'kill_rate', 'my_team_percent', 'his_team_percent'], 'number'],
            [['my_team_color_hue', 'his_team_color_hue', 'note', 'private_note', 'link_url'], 'string'],
            [['ua_variables', 'ua_custom', 'remote_addr'], 'string'],
            [['start_at', 'end_at', 'created_at', 'updated_at'], 'safe'],
            [['my_team_color_rgb', 'his_team_color_rgb'], 'string', 'max' => 6],
            [['agent_game_version_date'], 'string', 'max' => 32],
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
            'map_id' => 'Map ID',
            'weapon_id' => 'Weapon ID',
            'is_win' => 'Is Win',
            'is_knockout' => 'Is Knockout',
            'level' => 'Level',
            'level_after' => 'Level After',
            'rank_id' => 'Rank ID',
            'rank_exp' => 'Rank Exp',
            'rank_after_id' => 'Rank After ID',
            'rank_after_exp' => 'Rank After Exp',
            'rank_in_team' => 'Rank In Team',
            'kill' => 'Kill',
            'death' => 'Death',
            'kill_ratio' => 'Kill Ratio',
            'kill_rate' => 'Kill Rate',
            'max_kill_combo' => 'Max Kill Combo',
            'max_kill_streak' => 'Max Kill Streak',
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
            'note' => 'Note',
            'private_note' => 'Private Note',
            'link_url' => 'Link Url',
            'period' => 'Period',
            'version_id' => 'Version ID',
            'bonus_id' => 'Bonus ID',
            'env_id' => 'Env ID',
            'client_uuid' => 'Client Uuid',
            'ua_variables' => 'Ua Variables',
            'ua_custom' => 'Ua Custom',
            'agent_game_version_id' => 'Agent Game Version ID',
            'agent_game_version_date' => 'Agent Game Version Date',
            'agent_id' => 'Agent ID',
            'is_automated' => 'Is Automated',
            'use_for_entire' => 'Use For Entire',
            'remote_addr' => 'Remote Addr',
            'remote_port' => 'Remote Port',
            'start_at' => 'Start At',
            'end_at' => 'End At',
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
    public function getEnv()
    {
        return $this->hasOne(Environment::class, ['id' => 'env_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLobby()
    {
        return $this->hasOne(Lobby2::class, ['id' => 'lobby_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map2::class, ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMode()
    {
        return $this->hasOne(Mode2::class, ['id' => 'mode_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRank()
    {
        return $this->hasOne(Rank2::class, ['id' => 'rank_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRankAfter()
    {
        return $this->hasOne(Rank2::class, ['id' => 'rank_after_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVersion()
    {
        return $this->hasOne(SplatoonVersion2::class, ['id' => 'version_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAgentGameVersion()
    {
        return $this->hasOne(SplatoonVersion2::class, ['id' => 'agent_game_version_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBonus()
    {
        return $this->hasOne(TurfwarWinBonus2::class, ['id' => 'bonus_id']);
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
    public function getWeapon()
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }

    public function getIsMeaningful() : bool
    {
        $props = [
            'rule_id', 'map_id', 'weapon_id', 'is_win', 'rank_in_team', 'kill', 'death',
        ];
        foreach ($props as $prop) {
            if ($this->$prop !== null) {
                return true;
            }
        }
        return false;
    }
}
