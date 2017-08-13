<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models\api\v2;

use Yii;
use app\components\behaviors\FixAttributesBehavior;
use app\components\behaviors\TrimAttributesBehavior;
use app\components\helpers\CriticalSection;
use app\components\helpers\db\Now;
use app\models\Agent;
use app\models\Battle2;
use app\models\BattleDeathReason2;
use app\models\BattleEvents2;
use app\models\BattleImage2;
use app\models\BattleImageType;
use app\models\BattlePlayer2;
use app\models\DeathReason2;
use app\models\Lobby2;
use app\models\Map2;
use app\models\Mode2;
use app\models\Rank2;
use app\models\Rule2;
use app\models\SplatoonVersion2;
use app\models\User;
use app\models\Weapon2;
use yii\base\Model;
use yii\helpers\Json;
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
    public $knock_out;
    public $rank_in_team;
    public $kill;
    public $death;
    public $kill_or_assist;
    public $special;
    public $max_kill_combo;
    public $max_kill_streak;
    public $level;
    public $level_after;
    public $rank;
    public $rank_after;
    public $my_point;
    public $my_team_point;
    public $his_team_point;
    public $my_team_percent;
    public $his_team_percent;
    public $my_team_count;
    public $his_team_count;
    public $players;
    public $death_reasons;
    public $events;
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

    public $image_judge;
    public $image_result;
    public $image_gear;

    public function behaviors()
    {
        return [
            [
                'class' => TrimAttributesBehavior::class,
                'targets' => array_keys($this->attributes),
            ],
            [
                'class' => FixAttributesBehavior::class,
                'attributes' => [
                    'stage' => [
                        'combu' => 'kombu', // issue #219
                    ],
                    'weapon' => [
                        'manueuver' => 'maneuver', // issue #221
                        'manueuver_collabo' => 'maneuver_collabo', // issue #221
                    ],
                ],
            ],
        ];
    }

    public function rules()
    {
        return [
            [['mode'], 'default',
                'value' => function ($model, $attribute) : ?string {
                    if (!$model->rule) {
                        return null;
                    }
                    if (!$rule = Rule2::findOne(['key' => $model->rule])) {
                        return null;
                    }
                    if (count($rule->modes) !== 1) {
                        return null;
                    }
                    return $rule->modes[0]->key;
                },
            ],
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
                'targetClass' => Map2::class,
                'targetAttribute' => 'key',
            ],
            [['weapon'], 'exist',
                'targetClass' =>  Weapon2::class,
                'targetAttribute' => 'key',
            ],
            [['level', 'level_after'], 'integer', 'min' => 1],
            [['rank', 'rank_after'], 'exist',
                'targetClass' => Rank2::class,
                'targetAttribute' => 'key',
            ],
            [['result'], 'boolean', 'trueValue' => 'win', 'falseValue' => 'lose'],
            [['knock_out'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['kill', 'death', 'max_kill_combo', 'max_kill_streak'], 'integer', 'min' => 0],
            [['kill_or_assist', 'special'], 'integer', 'min' => 0],
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
                        : ((strtolower($this->agent) === 'ikalog') ? 'yes' : 'no');
                },
            ],
            [['my_point'], 'integer', 'min' => 0],
            [['my_team_point', 'his_team_point'], 'integer', 'min' => 0],
            [['my_team_percent', 'his_team_percent'], 'number',
                'min' => 0.0,
                'max' => 100.0,
            ],
            [['my_team_count', 'his_team_count'], 'integer', 'min' => 0, 'max' => 100],
            [['players'], 'validatePlayers'],
            [['death_reasons'], 'validateDeathReasons'],
            [['events'], 'validateEvents'],
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
            [['image_judge', 'image_result', 'image_gear'], 'safe'],
            [['image_judge', 'image_result', 'image_gear'], 'file',
                'maxSize' => 3 * 1024 * 1024,
                'when' => function ($model, $attr) {
                    return !is_string($model->$attr);
                }],
            [['image_judge', 'image_result', 'image_gear'], 'validateImageFile',
                'when' => function ($model, $attr) {
                    return !is_string($model->$attr);
                }],
            [['image_judge', 'image_result', 'image_gear'], 'validateImageString',
                'when' => function ($model, $attr) {
                    return is_string($model->$attr);
                }],
            [['map'], 'safe'],
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
                ['>=', 'created_at', gmdate('Y-m-d H:i:sP', $t - static::SAME_BATTLE_THRESHOLD_TIME)],
            ])
            ->limit(1)
            ->one();
    }

    public function getIsTest() : bool
    {
        $value = (string)$this->test;
        return $value !== '' && $value !== 'no';
    }

    public function setMap($key) : self
    {
        $this->stage = $key;
        return $this;
    }

    public function getMap() : Map2
    {
        return $this->stage;
    }

    public function toBattle() : Battle2
    {
        $intval = function ($string) : ?int {
            return $string === null ? null : intval($string, 10);
        };
        $floatval = function ($string) : ?float {
            return $string === null ? null : floatval($string);
        };
        $datetime = function ($value) use ($intval) : ?string {
            $value = $intval($value);
            return $value === null ? null : gmdate('Y-m-d\TH:i:sP', $value);
        };
        $key2id = function ($key, string $class) {
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
        $battle->client_uuid    = $this->uuid;
        $battle->lobby_id       = $key2id($this->lobby, Lobby2::class);
        $battle->mode_id        = $key2id($this->mode, Mode2::class);
        $battle->rule_id        = $key2id($this->rule, Rule2::class);
        $battle->map_id         = $key2id($this->stage, Map2::class);
        $battle->weapon_id      = $key2id($this->weapon, Weapon2::class);
        $battle->is_win = (function ($value) {
            switch ((string)$value) {
                case 'win':
                    return true;
                case 'lose':
                    return false;
                default:
                    null;
            }
        })($this->result);
        $battle->is_knockout = (function ($value) {
            switch ((string)$value) {
                case 'yes':
                    return true;
                case 'no':
                    return false;
                default:
                    null;
            }
        })($this->knock_out);
        $battle->rank_in_team   = $intval($this->rank_in_team);
        $battle->kill           = $intval($this->kill);
        $battle->death          = $intval($this->death);
        $battle->kill_or_assist = $intval($this->kill_or_assist);
        $battle->special        = $intval($this->special);
        $battle->max_kill_combo = $intval($this->max_kill_combo);
        $battle->max_kill_streak = $intval($this->max_kill_streak);
        $battle->level          = $intval($this->level);
        $battle->level_after    = $intval($this->level_after);
        $battle->rank_id        = $key2id($this->rank, Rank2::class);
        $battle->rank_after_id  = $key2id($this->rank_after, Rank2::class);
        $battle->my_point       = $intval($this->my_point);
        $battle->my_team_point  = $intval($this->my_team_point);
        $battle->his_team_point = $intval($this->his_team_point);
        $battle->my_team_percent  = $floatval($this->my_team_percent);
        $battle->his_team_percent = $floatval($this->his_team_percent);
        $battle->my_team_count  = $intval($this->my_team_count);
        $battle->his_team_count = $intval($this->his_team_count);
        $battle->is_automated   = ($this->automated === 'yes');
        $battle->link_url       = $this->link_url;
        $battle->note           = $this->note;
        $battle->private_note   = $this->private_note;
        $battle->agent_id       = $this->getAgentId($this->agent, $this->agent_version);
        $battle->ua_custom      = $this->agent_custom;
        $battle->ua_variables   = $this->agent_variables
            ? Json::encode(
                $this->agent_variables,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT
            )
            : null;
        $battle->start_at       = $datetime($this->start_at);
        $battle->end_at         = $datetime($this->end_at);
        $battle->use_for_entire = $this->getIsUsableForEntireStats();
        //if ($this->gears) {
        //    $o->headgear_id = $this->processGear('headgear');
        //    $o->clothing_id = $this->processGear('clothing');
        //    $o->shoes_id    = $this->processGear('shoes');
        //}
        if ($this->isTest) {
            $now = (int)($_SERVER['REQUEST_TIME'] ?? time());
            $battle->id = 0;
            foreach ($battle->attributes as $k => $v) {
                if ($v instanceof Now) {
                    $battle->$k = gmdate('Y-m-d H:i:sP', $now);
                }
            }
        }
        return $battle;
    }

    public function toEvents(Battle2 $battle) : ?BattleEvents2
    {
        if (!$this->events) {
            return null;
        }
        return Yii::createObject([
            'class'  => BattleEvents2::class,
            'id'     => $battle->id,
            'events' => Json::encode($this->events),
        ]);
    }

    public function toDeathReasons(Battle2 $battle)
    {
        if (is_array($this->death_reasons) || $this->death_reasons instanceof \stdClass) {
            $unknownCount = 0;
            foreach ($this->death_reasons as $key => $count) {
                $reason = DeathReason2::findOne(['key' => $key]);
                if ($key === 'unknown' || !$reason) {
                    $unknownCount += (int)$count;
                } else {
                    yield Yii::createObject([
                        'class'     => BattleDeathReason2::class,
                        'battle_id' => $battle->id,
                        'reason_id' => $reason->id,
                        'count'     => (int)$count,
                    ]);
                }
            }
            if ($unknownCount > 0) {
                $reason = DeathReason2::findOne(['key' => 'unknown']);
                if ($reason) {
                    yield Yii::createObject([
                        'class' => BattleDeathReason2::class,
                        'battle_id' => $battle->id,
                        'reason_id' => $reason->id,
                        'count'     => (int)$unknownCount,
                    ]);
                }
            }
        }
    }

    public function toPlayers(Battle2 $battle)
    {
        if (is_array($this->players) && !empty($this->players)) {
            foreach ($this->players as $form) {
                if (!$form instanceof PostBattlePlayerForm) {
                    throw new \Exception('Logic error: assert: instanceof PostBattlePlayerForm');
                }

                $weapon = ($form->weapon == '')
                    ? null
                    : Weapon2::findOne(['key' => $form->weapon]);

                $rank = ($form->rank == '')
                    ? null
                    : Rank2::findOne(['key' => $form->rank]);

                yield Yii::createObject([
                    'class'         => BattlePlayer2::class,
                    'battle_id'     => $battle->id,
                    'is_my_team'    => $form->team === 'my',
                    'is_me'         => $form->is_me === 'yes',
                    'weapon_id'     => $weapon->id ?? null,
                    'level'         => $form->level,
                    'rank_id'       => $rank->id ?? null,
                    'rank_in_team'  => $form->rank_in_team,
                    'kill'          => $form->kill,
                    'death'         => $form->death,
                    'kill_or_assist' => $form->kill_or_assist,
                    'special'       => $form->special,
                    'point'         => $form->point,
                    'my_kill'       => $form->my_kill,
                    'name'          => $form->name,
                ]);
            }
        }
    }


    public function toImageJudge(Battle2 $battle)
    {
        return $this->toImage($battle, BattleImageType::ID_JUDGE, 'image_judge');
    }

    public function toImageResult(Battle2 $battle)
    {
        return $this->toImage($battle, BattleImageType::ID_RESULT, 'image_result');
    }

    public function toImageGear(Battle2 $battle)
    {
        return $this->toImage($battle, BattleImageType::ID_GEAR, 'image_gear');
    }

    protected function toImage(Battle2 $battle, int $imageTypeId, string $attr)
    {
        if ($this->isTest) {
            return null;
        }
        if ($this->$attr == '' && !$this->$attr instanceof UploadedFile) {
            return null;
        }
        return Yii::createObject([
            'class'     => BattleImage2::class,
            'battle_id' => $battle->id,
            'type_id'   => $imageTypeId,
            'filename'  => BattleImage2::generateFilename(),
        ]);
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

    // protected function processGear($key)
    // {
    //     if ($this->isTest || !($this->gears instanceof PostGearsForm)) {
    //         return null;
    //     }

    //     $gearForm = $this->gears->$key;
    //     if (!($gearForm instanceof BaseGearForm)) {
    //         return null;
    //     }

    //     $gearModel = $gearForm->getGearModel(); // may null
    //     $primaryAbility = $gearForm->primary_ability
    //         ? Ability::findOne(['key' => $gearForm->primary_ability])
    //         : null;
    //     $secondaryAbilityIdList = [];
    //     if (is_array($gearForm->secondary_abilities)) {
    //         foreach ($gearForm->secondary_abilities as $aKey) {
    //             if ($aKey == '') {
    //                 $secondaryAbilityIdList[] = null;
    //             } else {
    //                 if ($a = Ability::findOne(['key' => $aKey])) {
    //                     $secondaryAbilityIdList[] = $a->id;
    //                 }
    //             }
    //         }
    //     }
    //     $fingerPrint = GearConfiguration::generateFingerPrint(
    //         $gearModel ? $gearModel->id : null,
    //         $primaryAbility ? $primaryAbility->id : null,
    //         $secondaryAbilityIdList
    //     );

    //     $lock = CriticalSection::lock(__METHOD__, 60);
    //     $config = GearConfiguration::findOne(['finger_print' => $fingerPrint]);
    //     if (!$config) {
    //         $config = new GearConfiguration();
    //         $config->finger_print = $fingerPrint;
    //         $config->gear_id = $gearModel ? $gearModel->id : null;
    //         $config->primary_ability_id = $primaryAbility ? $primaryAbility->id : null;
    //         if (!$config->save()) {
    //             throw new \Exception('Could not save gear_counfiguration');
    //         }

    //         foreach ($secondaryAbilityIdList as $aId) {
    //             $sub = new GearConfigurationSecondary();
    //             $sub->config_id = $config->id;
    //             $sub->ability_id = $aId;
    //             if (!$sub->save()) {
    //                 throw new \Exception('Could not save gear_configuration_secondary');
    //             }
    //         }
    //     }

    //     return $config->id;
    // }

    public function getIsUsableForEntireStats()
    {
        if ($this->automated !== 'yes') {
            return false;
        }

        // IkaLog 以外で automated が yes のものは使えることにする
        if (strtolower(substr((string)$this->agent, 0, 6)) !== 'ikalog') {
            return true;
        }

        //FIXME: とりあえず ikalog なら ok ということにする
        return true;

        // // stat.ink の要求する最小IkaLogバージョンを取得
        // $ikalogReq = IkalogRequirement::find()
        //     ->andWhere(['<=','[[from]]', new Now()])
        //     ->orderBy('[[from]] DESC')
        //     ->limit(1)
        //     ->one();
        // if (!$ikalogReq) {
        //     // 最小IkaLogバージョンの定義がなければokと見なす
        //     return true;
        // }

        // // IkaLog では統計に利用するためには agent_game_version_date が必須になりました
        // if (trim((string)$this->agent_game_version_date) == '') {
        //     return false;
        // }

        // // "2016-06-08_00" => "2016.6.8.0" のような文字列に game_version_date を変換する
        // // "." 区切りにするのはバージョン比較は version_compare に喰わせると楽だから
        // //
        // // 1. とりあえず数字以外を "." に置き換えて
        // // 2. "." で分割して配列を作って
        // // 3. 各要素の左側の "0" を取り去って
        // // 4. 取り去った結果空文字列になる可能性があるのでそのときに "0" にするために int 経由して（黒魔術）
        // // 5. "." で再結合する
        // $fConvertVersionDate = function ($version_date) {
        //     return implode(
        //         '.',
        //         array_map(
        //             function ($a) {
        //                 return (string)(int)ltrim($a, '0');
        //             },
        //             explode(
        //                 '.',
        //                 trim(preg_replace('/[^0-9]+/', '.', trim((string)$version_date)))
        //             )
        //         )
        //     );
        // };

        // if (version_compare(
        //     $fConvertVersionDate($this->agent_game_version_date),
        //     $fConvertVersionDate($ikalogReq->version_date),
        //     '>='
        // )) {
        //     return true;
        // }

        // return false;
    }

    public function getCriticalSectionName()
    {
        $values = [
            'class' => __CLASS__,
            'version' => 2,
            'user' => Yii::$app->user->identity->id,
        ];
        asort($values);
        return rtrim(
            base64_encode(
                hash_hmac(
                    'sha256',
                    http_build_query($values, '', '&'),
                    Yii::getAlias('@app'),
                    true
                )
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

    public function validateEvents(string $attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        if (!is_array($this->$attribute)) {
            if ($this->$attribute == '') {
                $this->$attribute = null;
            } else {
                $this->addError($attribute, "{$attribute} must be an array.");
            }
            return;
        }

        if (count($this->$attribute) === 0) {
            return;
        }

        $newValues = [];
        foreach ($this->$attribute as $value) {
            if (is_array($value)) {
                $value = (object)$value;
            }
            if (!isset($value->at) || !isset($value->type)) {
                continue;
            }
            $value->at = filter_var($value->at, FILTER_VALIDATE_FLOAT);
            if ($value->at === false) {
                continue;
            }
            $newValues[] = $value;
        }
        usort($newValues, function ($a, $b) {
            return $a->at - $b->at;
        });
        $this->$attribute = $newValues;
    }


    public function validateAgentVariables(string $attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        $value = $this->$attribute;
        if ($value == '') {
            $this->$attribute = null;
            return;
        }
        if (is_array($value)) {
            if (count($value) === 0) {
                $this->$attribute = null;
                return;
            }
            $value = (object)$value;
        }
        if (is_object($value) && ($value instanceof \stdClass)) {
            $newValue = new \stdClass();
            foreach ($value as $k => $v) {
                $k = is_int($k) ? "ARRAY[{$k}]" : (string)$k;
                if (!mb_check_encoding($k, 'UTF-8')) {
                    $this->addError($attribute, 'Invalid UTF-8 sequence in KEY');
                    return;
                }
                if (!is_string($v)) {
                    if (is_int($v) || is_float($v)) {
                        $v = (string)$v;
                    } elseif (is_bool($v)) {
                        $v = $v ? 'true' : 'false';
                    } elseif (is_object($v) && is_callable([$v, '__toString'])) {
                        $v = $v->__toString();
                    } else {
                        $v = Json::encode($v);
                    }
                }
                if (!mb_check_encoding($v, 'UTF-8')) {
                    $this->addError($attribute, 'Invalid UTF-8 sequence in VALUE (key=' . $k . ')');
                    return;
                }
                $newValue->$k = $v;
            }
            $this->$attribute = $newValue;
        } else {
            $this->addError($attribute, 'Invalid format: ' . $attribute);
        }
    }

    public function validatePlayers(string $attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        if (!is_array($this->$attribute)) {
            $this->addError($attribute, "{$attribute} must be an array.");
            return;
        }

        if (count($this->$attribute) === 0) {
            return;
        }

        if (count($this->$attribute) < 2 || count($this->$attribute) > 8) {
            $this->addError($attribute, "{$attribute} must be contain 2-8 elements.");
            return;
        }

        $newValues = [];
        foreach ($this->$attribute as $i => $oldValue) {
            $newValue = Yii::createObject(['class' => PostBattlePlayerForm::class]);
            $newValue->attributes = $oldValue;
            if (!$newValue->validate()) {
                $this->addError("{$attribute}.{$i}", $newValue->getErrors());
            }
            $newValues[] = $newValue;
        }
        $this->$attribute = $newValues;
    }

    public function validateDeathReasons(string $attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        $value = $this->$attribute;
        if ($value == '') {
            $this->$attribute = [];
            return;
        }
        if (!is_array($value) && !$value instanceof \stdClass) {
            $this->addError($attribute, "{$attribute} should be a map.");
            return;
        }
        foreach ($value as $k => $v) {
            $tmp = filter_var($v, FILTER_VALIDATE_INT);
            if ($tmp === false || $tmp < 1 || $tmp > 99) {
                $this->addError($attribute, "Value of {$attribute}[{$k}] (= {$v}) looks broken.");
            }
        }
    }

    public function validateImageFile($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        if (!($this->$attribute instanceof UploadedFile)) {
            // 先に file バリデータを通すのでここは絶対通らないはず
            $this->addError($attribute, '[BUG?] $attributes is not an instance of UploadedFile');
            return;
        }
        return $this->validateImageStringImpl(
            file_get_contents($this->$attribute->tempName, false, null),
            $attribute
        );
    }

    public function validateImageString($attribute, $params)
    {
        return $this->validateImageStringImpl($this->$attribute, $attribute);
    }

    private function validateImageStringImpl($binary, $attribute)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        if (!$gd = @imagecreatefromstring($binary)) {
            $this->addError($attribute, 'Could not decode binary that contained an image data.');
            return;
        }
        imagedestroy($gd);
    }

    private function getAgentId(?string $name, ?string $version) : ?int
    {
        $name = trim($name);
        $version = trim($version);
        if ($name == '' || $version == '') {
            return null;
        }
        $model = Agent::findOne([
            'name' => $name,
            'version' => $version,
        ]);
        if ($model) {
            return (int)$model->id;
        }
        $model = Yii::createObject([
            'class' => Agent::class,
            'name' => $name,
            'version' => $version,
        ]);
        if (!$model->save()) {
            return null;
        }
        return (int)$model->id;
    }
}
