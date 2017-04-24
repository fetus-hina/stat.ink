<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models\api\v2;

use Yii;
use app\components\helpers\CriticalSection;
use app\components\helpers\db\Now;
use app\models\Battle2;
use app\models\Lobby2;
use app\models\Mode2;
use app\models\Rule2;
use app\models\SplatoonVersion2;
use app\models\Stage2;
use app\models\User;
use app\models\Weapon2;
use yii\base\Model;
use yii\web\UploadedFile;

class PostBattleForm extends Model
{
    const SAME_BATTLE_THRESHOLD_TIME = 86400;

    public $test;

    public $uuid;
    public $lobby;
    public $mode;
    public $rule;
    public $stage;
    public $weapon;
    public $result;
    public $rank_in_team;
    public $kill;
    public $death;
    public $level;
    public $level_after;
    public $my_point;
    public $automated;
    public $link_url;
    public $note;
    public $private_note;
    public $agent;
    public $agent_version;
    public $agent_custom;
    public $agent_variables;
    public $start_at;
    public $end_at;

    public function rules()
    {
        return [
            [['test'], 'in',
                'range' => ['no', 'validate', 'dry_run'],
            ],
            [['lobby'], 'exist',
                'targetClass' => Lobby2::class,
                'targetAttribute' => 'key',
            ],
            [['mode'], 'exist',
                'targetClass' => Mode2::class,
                'targetAttribute' => 'key',
            ],
            [['rule'], 'exist',
                'targetClass' => Rule2::class,
                'targetAttribute' => 'key',
            ],
            [['stage'], 'exist',
                'targetClass' => Stage2::class,
                'targetAttribute' => 'key',
            ],
            [['weapon'], 'exist',
                'targetClass' =>  Weapon2::class,
                'targetAttribute' => 'key',
            ],
            [['level', 'level_after'], 'integer', 'min' => 1],
            [['result'], 'boolean', 'trueValue' => 'win', 'falseValue' => 'lose'],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['kill', 'death'], 'integer', 'min' => 0],
            [['start_at', 'end_at'], 'integer', 'min' => 0],
            [['agent'], 'string', 'max' => 64],
            [['agent_version'], 'string', 'max' => 255],
            [['agent', 'agent_version'], 'required',
                'when' => function ($model, $attr) {
                    return (string)$this->agent !== '' || (string)$this->agent_version !== '';
                },
            ],
            [['agent_custom'], 'string'],
            [['uuid'], 'string', 'max' => 64],
            [['automated'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            [['automated'], 'filter',
                'filter' => function ($value) {
                    return ($value === 'yes' || $value === 'no')
                        ? $value
                        : strtolower($this->agent) === 'ikalog') ? 'yes' : 'no';
                },
            ],
            [['my_point'], 'integer', 'min' => 0],
            [['my_team_final_point', 'his_team_final_point'], 'integer', 'min' => 0],
            [['my_team_final_percent', 'his_team_final_percent'], 'number',
                'min' => 0.0,
                'max' => 100.0,
            ],
            [['link_url'], 'url'],
            [['note', 'private_note'], 'string'],
            [['note', 'private_note'], 'filter',
                'filter' => function ($value) {
                    $value = (string)$value;
                    $value = preg_replace('/\x0d\x0a|\x0d|\x0a/', "\n", $value);
                    $value = preg_replace('/(?:\x0d\x0a|\x0d|\x0a){3,}/', "\n\n", $value);
                    $value = trim($value);
                    return $value === '' ? null : $value;
                },
            ],
            [['agent_variables'], 'validateAgentVariables'],
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

    public function getSameBattle() : ?Battle2
    {
        if (trim($this->uuid) === '') {
            return null;
        }
        if (!$user = Yii::$app->user->identity) {
            return null;
        }
        $t = (int)($_SERVER['REQUEST_TIME'] ?? time());
        return Battle2::find()
            ->where(['and',
                [
                    'user_id' => $user->id,
                    'client_uuid' => Battle2::createClientUuid($this->uuid),
                ],
                ['>=', 'at', gmdate('Y-m-d H:i:sP', $t - static::SAME_BATTLE_THRESHOLD_TIME)],
            ])
            ->limit(1)
            ->one();
    }

    public function getIsTest() : bool
    {
        $value = trim((string)$this->test);
        return $value !== '' && $value !== 'no';
    }

    public function setMap($key) : self
    {
        $this->stage = $key;
        return $this;
    }

    public function getMap()
    {
        return $this->stage;
    }

    public function save()
    {
        $trim = function ($string) : ?string {
            $string = trim((string)$string);
            return $string === '' ? null : $string;
        };
        $intval = function ($string) use ($trim) : ?int {
            $string = $trim($string);
            return $string === null ? null : intval($string, 10);
        };
        $datetime = function ($value) use ($intval) : ?string {
            $value = $trim($value);
            return $value === null ? null : gmdate('Y-m-d\TH:i:sP', $value);
        };
        $key2id = function ($key, string $class) use ($trim) {
            $key = $trim($key);
            if ($key === null) {
                return null;
            }
            if (!$obj = $class::findOne(['key' => $key])) {
                return null;
            }
            return $obj->id;
        };
        $user = Yii::$app->user->identity;
        $battle = Yii::createObject(['class' => Battle2::class]);
        $battle->user_id        = $user->id;
        $battle->env_id         = $user->env_id;
        $battle->client_uuid    = $trim($this->uuid);
        $battle->lobby_id       = $key2id($this->lobby, Lobby2::class);
        $battle->mode_id        = $key2id($this->mode, Mode2::class);
        $battle->rule_id        = $key2id($this->rule, Rule2::class);
        $battle->stage_id       = $key2id($this->stage, Stage2::class);
        $battle->weapon_id      = $key2id($this->weapon, Weapon2::class);
        $battle->is_win = (function ($value) {
            switch (string($value)) {
                case 'win':
                    return true;
                case 'lose':
                    return false;
                default:
                    null;
            }
        })($this->result);
        $battle->rank_in_team   = $intval($this->rank_in_team);
        $battle->kill           = $intval($this->kill);
        $battle->death          = $intval($this->death);
        $battle->level          = $intval($this->level);
        $battle->level_after    = $intval($this->level_after);
        $battle->my_point       = $intval($this->my_point);
        $battle->is_automated   = ($this->automated === 'yes');
        $battle->link_url       = $trim($this->link_url);
        $battle->note           = $trim($this->note);
        $battle->private_note   = $trim($this->private_note);
        //$battle->agent          = $trim($this->agent);
        //$battle->aget_version   = $trim($this->agent_version);
        $battle->agent_id       = null;
        $battle->ua_custom      = $trim($this->agent_custom);
        $battle->ua_variables   = $this->agent_variables
            ? json_encode($this->agent_variables, JSON_FORCE_OBJECT)
            : null;
        $battle->start_at       = $datetime($this->start_at);
        $battle->end_at         = $datetime($this->end_at);
    }

    public function toBattle()
    {
        $user = $this->getUser();

        $o = new Battle();
        $o->user_id         = $user->id;
        $o->env_id          = $user->env_id;
        $o->lobby_id        = $this->lobby ? Lobby::findOne(['key' => $this->lobby])->id : null;
        $o->rule_id         = $this->rule ? Rule::findOne(['key' => $this->rule])->id : null;
        $o->map_id          = $this->map ? Map::findOne(['key' => $this->map])->id : null;
        $o->weapon_id       = $this->weapon ? Weapon::findOne(['key' => $this->weapon])->id : null;
        $o->level           = $this->level ? (int)$this->level : null;
        $o->level_after     = $this->level_after ? (int)$this->level_after : null;
        $o->rank_id         = $this->rank ? Rank::findOne(['key' => $this->rank])->id : null;
        $o->rank_after_id   = $this->rank_after ? Rank::findOne(['key' => $this->rank_after])->id : null;
        $o->rank_exp        = (string)$this->rank_exp != '' ? (int)$this->rank_exp : null;
        $o->rank_exp_after  = (string)$this->rank_exp_after != '' ? (int)$this->rank_exp_after : null;
        $o->cash            = (string)$this->cash != '' ? (int)$this->cash : null;
        $o->cash_after      = (string)$this->cash_after != '' ? (int)$this->cash_after : null;
        $o->is_win          = $this->result === 'win' ? true : ($this->result === 'lose' ? false : null);
        $o->rank_in_team    = $this->rank_in_team ? (int)$this->rank_in_team : null;
        $o->kill            = (string)$this->kill != '' ? (int)$this->kill : null;
        $o->death           = (string)$this->death != '' ? (int)$this->death : null;
        $o->gender_id       = $this->gender === 'boy' ? 1 : ($this->gender === 'girl' ? 2 : null);
        $o->fest_title_id = $this->fest_title ? FestTitle::findOne(['key' => $this->fest_title])->id : null;
        $o->fest_title_after_id = $this->fest_title_after
            ? FestTitle::findOne(['key' => $this->fest_title_after])->id
            : null;
        $o->fest_exp        = (string)$this->fest_exp != '' ? (int)$this->fest_exp : null;
        $o->fest_exp_after  = (string)$this->fest_exp_after != '' ? (int)$this->fest_exp_after : null;
        $o->fest_power      = (string)$this->fest_power != '' ? (int)$this->fest_power : null;
        $o->my_team_power   = (string)$this->my_team_power != '' ? (int)$this->my_team_power : null;
        $o->his_team_power  = (string)$this->his_team_power != '' ? (int)$this->his_team_power : null;
        $o->my_team_color_hue = $this->my_team_color ? $this->my_team_color['hue'] : null;
        $o->my_team_color_rgb = $this->my_team_color ? vsprintf('%02x%02x%02x', $this->my_team_color['rgb']) : null;
        $o->his_team_color_hue = $this->his_team_color ? $this->his_team_color['hue'] : null;
        $o->his_team_color_rgb = $this->his_team_color ? vsprintf('%02x%02x%02x', $this->his_team_color['rgb']) : null;
        $o->start_at        = $this->start_at != ''
            ? gmdate('Y-m-d H:i:sP', (int)$this->start_at)
            : null;
        $o->end_at          = $this->end_at != ''
            ? gmdate('Y-m-d H:i:sP', (int)$this->end_at)
            : new Now();
        $o->agent_id        = null;
        $o->ua_custom       = (string)$this->agent_custom == '' ? null : (string)$this->agent_custom;
        $o->ua_variables    = $this->agent_variables ? json_encode($this->agent_variables, JSON_FORCE_OBJECT) : null;
        $o->agent_game_version_id = $this->agent_game_version != ''
            ? (SplatoonVersion::findOne(['tag' => $this->agent_game_version])->id ?? null)
            : null;
        $o->agent_game_version_date = $this->agent_game_version_date != ''
            ? $this->agent_game_version_date
            : null;
        $o->client_uuid     = (string)$this->uuid == '' ? null : (string)$this->uuid;
        $o->at              = new Now();
        $o->is_automated    = ($this->automated === 'yes');
        $o->link_url        = (string)$this->link_url == '' ? null : (string)$this->link_url;
        $o->note            = $this->note;
        $o->private_note    = $this->private_note;

        $o->my_point                = (string)$this->my_point != '' ? (int)$this->my_point : null;
        $o->my_team_final_point     = (string)$this->my_team_final_point != ''
            ? (int)$this->my_team_final_point
            : null;
        $o->his_team_final_point    = (string)$this->his_team_final_point != ''
            ? (int)$this->his_team_final_point
            : null;
        $o->my_team_final_percent   = (string)$this->my_team_final_percent != ''
            ? sprintf('%.1f', (float)$this->my_team_final_percent)
            : null;
        $o->his_team_final_percent   = (string)$this->his_team_final_percent != ''
            ? sprintf('%.1f', (float)$this->his_team_final_percent)
            : null;
        $o->is_knock_out    = $this->knock_out === 'yes' ? true : ($this->knock_out === 'no' ? false : null);
        $o->my_team_count   = (string)$this->my_team_count != '' ? (int)$this->my_team_count : null;
        $o->his_team_count  = (string)$this->his_team_count != '' ? (int)$this->his_team_count : null;
        $o->max_kill_combo  = (string)$this->max_kill_combo != '' ? (int)$this->max_kill_combo : null;
        $o->max_kill_streak = (string)$this->max_kill_streak != '' ? (int)$this->max_kill_streak : null;

        $o->use_for_entire = $this->getIsUsableForEntireStats();

        if ($this->gears) {
            $o->headgear_id = $this->processGear('headgear');
            $o->clothing_id = $this->processGear('clothing');
            $o->shoes_id    = $this->processGear('shoes');
        }

        if ($this->isTest) {
            $now = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
            $o->id = 0;
            foreach ($o->attributes as $k => $v) {
                if ($v instanceof Now) {
                    $o->$k = gmdate('Y-m-d H:i:sP', $now);
                }
            }
        }

        return $o;
    }

    public function toEvents(Battle $battle)
    {
        if (!$this->events) {
            return null;
        }
        $o = new BattleEvents();
        $o->id = $battle->id;
        $o->events = json_encode($this->events, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return $o;
    }

    public function toDeathReasons(Battle $battle)
    {
        if (is_array($this->death_reasons) || $this->death_reasons instanceof \stdClass) {
            $unknownCount = 0;
            foreach ($this->death_reasons as $key => $count) {
                $reason = DeathReason::findOne(['key' => $key]);
                if ($key === 'unknown' || !$reason) {
                    $unknownCount += (int)$count;
                } else {
                    $o = new BattleDeathReason();
                    $o->battle_id = $battle->id;
                    $o->reason_id = $reason->id;
                    $o->count = (int)$count;
                    yield $o;
                }
            }
            if ($unknownCount > 0) {
                $reason = DeathReason::findOne(['key' => 'unknown']);
                if ($reason) {
                    $o = new BattleDeathReason();
                    $o->battle_id = $battle->id;
                    $o->reason_id = $reason->id;
                    $o->count = (int)$unknownCount;
                    yield $o;
                }
            }
        }
    }

    public function toPlayers(Battle $battle)
    {
        if (is_array($this->players) && !empty($this->players)) {
            foreach ($this->players as $form) {
                if (!$form instanceof PostBattlePlayerForm) {
                    throw new \Exception('Logic error: assert: instanceof PostBattlePlayerForm');
                }

                $weapon = ($form->weapon == '')
                    ? null
                    : Weapon::findOne(['key' => $form->weapon]);

                $rank = ($form->rank == '')
                    ? null
                    : Rank::findOne(['key' => $form->rank]);

                $player = new BattlePlayer();
                $player->attributes = [
                    'battle_id'     => $battle->id,
                    'is_my_team'    => $form->team === 'my',
                    'is_me'         => $form->is_me === 'yes',
                    'weapon_id'     => $weapon ? $weapon->id : null,
                    'rank_id'       => $rank ? $rank->id : null,
                    'level'         => (string)$form->level === '' ? null : (int)$form->level,
                    'rank_in_team'  => (string)$form->rank_in_team === '' ? null : (int)$form->rank_in_team,
                    'kill'          => (string)$form->kill === '' ? null : (int)$form->kill,
                    'death'         => (string)$form->death === '' ? null : (int)$form->death,
                    'point'         => (string)$form->point === '' ? null : (int)$form->point,
                    'my_kill'       => (string)$form->my_kill === '' ? null : (int)$form->my_kill,
                ];
                yield $player;
            }
        }
    }


    public function toImageJudge(Battle $battle)
    {
        return $this->toImage($battle, BattleImageType::ID_JUDGE, 'image_judge');
    }

    public function toImageResult(Battle $battle)
    {
        return $this->toImage($battle, BattleImageType::ID_RESULT, 'image_result');
    }

    public function toImageGear(Battle $battle)
    {
        return $this->toImage($battle, BattleImageType::ID_GEAR, 'image_gear');
    }

    protected function toImage(Battle $battle, $imageTypeId, $attr)
    {
        if ($this->isTest) {
            return null;
        }
        if ($this->$attr == '' && !$this->$attr instanceof UploadedFile) {
            return null;
        }
        $o = new BattleImage();
        $o->battle_id = $battle->id;
        $o->type_id = $imageTypeId;
        $o->filename = BattleImage::generateFilename();
        return $o;
    }

    public function estimateAutomatedAgent()
    {
        if ($this->hasErrors()) {
            return;
        }
        if ($this->automated === 'yes' || $this->automated === 'no') {
            return;
        }

        $this->automated = 'no';
        if ($this->agent != '') {
            $attr = AgentAttribute::findOne(['name' => (string)$this->agent]);
            if ($attr && $attr->is_automated) {
                $this->automated = 'yes';
            }
        }
    }

    protected function processGear($key)
    {
        if ($this->isTest || !($this->gears instanceof PostGearsForm)) {
            return null;
        }

        $gearForm = $this->gears->$key;
        if (!($gearForm instanceof BaseGearForm)) {
            return null;
        }

        $gearModel = $gearForm->getGearModel(); // may null
        $primaryAbility = $gearForm->primary_ability
            ? Ability::findOne(['key' => $gearForm->primary_ability])
            : null;
        $secondaryAbilityIdList = [];
        if (is_array($gearForm->secondary_abilities)) {
            foreach ($gearForm->secondary_abilities as $aKey) {
                if ($aKey == '') {
                    $secondaryAbilityIdList[] = null;
                } else {
                    if ($a = Ability::findOne(['key' => $aKey])) {
                        $secondaryAbilityIdList[] = $a->id;
                    }
                }
            }
        }
        $fingerPrint = GearConfiguration::generateFingerPrint(
            $gearModel ? $gearModel->id : null,
            $primaryAbility ? $primaryAbility->id : null,
            $secondaryAbilityIdList
        );

        $lock = CriticalSection::lock(__METHOD__, 60);
        $config = GearConfiguration::findOne(['finger_print' => $fingerPrint]);
        if (!$config) {
            $config = new GearConfiguration();
            $config->finger_print = $fingerPrint;
            $config->gear_id = $gearModel ? $gearModel->id : null;
            $config->primary_ability_id = $primaryAbility ? $primaryAbility->id : null;
            if (!$config->save()) {
                throw new \Exception('Could not save gear_counfiguration');
            }

            foreach ($secondaryAbilityIdList as $aId) {
                $sub = new GearConfigurationSecondary();
                $sub->config_id = $config->id;
                $sub->ability_id = $aId;
                if (!$sub->save()) {
                    throw new \Exception('Could not save gear_configuration_secondary');
                }
            }
        }

        return $config->id;
    }

    public function getIsUsableForEntireStats()
    {
        if ($this->automated !== 'yes') {
            return false;
        }

        // IkaLog 以外で automated が yes のものは使えることにする
        if (strtolower(substr((string)$this->agent, 0, 6)) !== 'ikalog') {
            return true;
        }

        // stat.ink の要求する最小IkaLogバージョンを取得
        $ikalogReq = IkalogRequirement::find()
            ->andWhere(['<=','[[from]]', new Now()])
            ->orderBy('[[from]] DESC')
            ->limit(1)
            ->one();
        if (!$ikalogReq) {
            // 最小IkaLogバージョンの定義がなければokと見なす
            return true;
        }

        // IkaLog では統計に利用するためには agent_game_version_date が必須になりました
        if (trim((string)$this->agent_game_version_date) == '') {
            return false;
        }

        // "2016-06-08_00" => "2016.6.8.0" のような文字列に game_version_date を変換する
        // "." 区切りにするのはバージョン比較は version_compare に喰わせると楽だから
        //
        // 1. とりあえず数字以外を "." に置き換えて
        // 2. "." で分割して配列を作って
        // 3. 各要素の左側の "0" を取り去って
        // 4. 取り去った結果空文字列になる可能性があるのでそのときに "0" にするために int 経由して（黒魔術）
        // 5. "." で再結合する
        $fConvertVersionDate = function ($version_date) {
            return implode(
                '.',
                array_map(
                    function ($a) {
                        return (string)(int)ltrim($a, '0');
                    },
                    explode(
                        '.',
                        trim(preg_replace('/[^0-9]+/', '.', trim((string)$version_date)))
                    )
                )
            );
        };

        if (version_compare(
            $fConvertVersionDate($this->agent_game_version_date),
            $fConvertVersionDate($ikalogReq->version_date),
            '>='
        )) {
            return true;
        }

        return false;
    }

    public function getCriticalSectionName()
    {
        return rtrim(
            base64_encode(
                hash_hmac('sha256', $this->apikey, __CLASS__, true)
            ),
            '='
        );
    }

    public function acquireLock($timeout = 60)
    {
        try {
            return CriticalSection::lock($this->getCriticalSectionName(), $timeout);
        } catch (\RuntimeException $e) {
            return false;
        }
    }
}
