<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\components\helpers\db\Now;
use yii\base\Model;

class Battle2FilterForm extends Model
{
    public $screen_name;

    public $rule;
    public $map;
    public $weapon;
    public $rank;
    public $result;
    public $term;
    public $term_from;
    public $term_to;
    public $timezone;
    public $id_from;
    public $id_to;

    public function formName()
    {
        return 'filter';
    }

    public function rules()
    {
        return [
            [['screen_name'], 'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'screen_name',
            ],
            [['rule'], 'in',
                'range' => [
                    'standard-regular-nawabari',
                    'standard-gachi-any',
                    'standard-gachi-area',
                    'standard-gachi-yagura',
                    'standard-gachi-hoko',
                    'any-gachi-any',
                    'any-gachi-area',
                    'any-gachi-yagura',
                    'any-gachi-hoko',
                    'any_squad-gachi-any',
                    'any_squad-gachi-area',
                    'any_squad-gachi-yagura',
                    'any_squad-gachi-hoko',
                    'squad_2-gachi-any',
                    'squad_2-gachi-area',
                    'squad_2-gachi-yagura',
                    'squad_2-gachi-hoko',
                    'squad_4-gachi-any',
                    'squad_4-gachi-area',
                    'squad_4-gachi-yagura',
                    'squad_4-gachi-hoko',
                    'any-fest-nawabari',
                    'standard-fest-nawabari',
                    'squad_4-fest-nawabari',
                    'private-private-any',
                    'private-private-nawabari',
                    'private-private-gachi',
                    'private-private-area',
                    'private-private-yagura',
                    'private-private-hoko',
                ],
            ],
            [['map'], 'exist',
                'targetClass' => Map2::class,
                'targetAttribute' => 'key',
            ],
            [['weapon'], 'exist',
                'targetClass' => Weapon2::class,
                'targetAttribute' => 'key',
                'when' => function () : bool {
                    return !in_array(substr($this->weapon, 0, 1), ['@', '+', '*', '~'], true);
                },
            ],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => WeaponType2::class,
                ],
                'when' => function () : bool {
                    return substr($this->weapon, 0, 1) === '@';
                },
            ],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => Subweapon2::class,
                ],
                'when' => function () : bool {
                    return substr($this->weapon, 0, 1) === '+';
                },
            ],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => Special2::class,
                ],
                'when' => function () : bool {
                    return substr($this->weapon, 0, 1) === '*';
                },
            ],
            [['weapon'], 'validateRepresentativeWeapon',
                'when' => function () : bool {
                    return substr($this->weapon, 0, 1) === '~';
                },
            ],
            [['rank'], 'exist',
                'targetClass' => Rank2::class,
                'targetAttribute' => 'key',
                'when' => function () : bool {
                    return substr($this->rank, 0, 1) !== '~';
                },
            ],
            [['rank'], 'validateRankGroup',
                'when' => function () {
                    return substr($this->rank, 0, 1) === '~';
                },
            ],
            [['result'], 'boolean',
                'trueValue' => 'win',
                'falseValue' => 'lose',
            ],
            [['term'], 'in',
                'range' => array_merge(
                    [
                        'this-period',
                        'last-period',
                        '24h',
                        'today',
                        'yesterday',
                        'last-10-battles',
                        'last-20-battles',
                        'last-50-battles',
                        'last-100-battles',
                        'last-200-battles',
                        'term',
                    ],
                    array_map(
                        function ($a) : string {
                            return 'v' . $a['tag'];
                        },
                        SplatoonVersion2::find()
                            ->orderBy(['released_at' => SORT_DESC])
                            ->asArray()
                            ->all()
                    )
                ),
            ],
            [['term_from', 'term_to'], 'date', 'format' => 'yyyy-M-d H:m:s'],
            [['timezone'], 'validateTimezone', 'skipOnEmpty' => false],
            [['id_from', 'id_to'], 'integer', 'min' => 1],
        ];
    }

    public function attributeLabels()
    {
        return [
            'screen_name'   => Yii::t('app', 'Screen Name'),
            'rule'          => Yii::t('app', 'Mode'),
            'map'           => Yii::t('app', 'Stage'),
            'weapon'        => Yii::t('app', 'Weapon'),
            'rank'          => Yii::t('app', 'Rank'),
            'result'        => Yii::t('app', 'Result'),
            'term'          => Yii::t('app', 'Term'),
            'term_from'     => Yii::t('app', 'Period From'),
            'term_to'       => Yii::t('app', 'Period To'),
            'id_from'       => Yii::t('app', 'ID From'),
            'id_to'         => Yii::t('app', 'ID To'),
        ];
    }

    public function load($data, $formName = null)
    {
        foreach (['id_from', 'id_to'] as $key) {
            if (isset($data[$key])) {
                $value = $data[$key];
                if (is_scalar($value) && trim($value) !== '') {
                    $this->$key = trim($value);
                }
            }
        }
        return parent::load($data, $formName);
    }

    public function validateWeapon(string $attr, $params) : void
    {
        $value = substr($this->$attr, 1);
        $method = [$params['modelClass'], 'findOne'];
        $isExist = !!call_user_func($method, ['key' => $value]);
        if (!$isExist) {
            $this->addError(
                $attr,
                Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => $this->getAttributeLabel($attr),
                ])
            );
        }
    }

    public function validateRepresentativeWeapon(string $attr, $params) : void
    {
        $value = substr($this->$attr, 1);
        $count = Weapon2::find()
            ->andWhere("{{weapon2}}.[[id]] = {{weapon2}}.[[main_group_id]]")
            ->andWhere(['key' => $value])
            ->count();
        if ($count < 1) {
            $this->addError(
                $attr,
                Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => $this->getAttributeLabel($attr),
                ])
            );
        }
    }

    public function validateRankGroup(string $attr, $params) : void
    {
        $value = substr($this->$attr, 1);
        $count = RankGroup2::find()
            ->andWhere(['key' => $value])
            ->count();
        if ($count < 1) {
            $this->addError(
                $attr,
                Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => $this->getAttributeLabel($attr),
                ])
            );
        }
    }

    public function validateTimezone($attr, $params)
    {
        $value = $this->$attr;
        if (is_scalar($value) && $value != '') {
            $exist = Timezone::find()->where(['identifier' => $value])->orderBy(null)->exists();
            if ($exist) {
                return;
            }
        }
        $this->$attr = Yii::$app->timeZone;
    }

    public function toPermLink($formName = false)
    {
        if ($formName === false) {
            $formName = $this->formName();
        }

        $ret = [];
        $push = function ($key, $value) use ($formName, &$ret) {
            if ($formName != '') {
                $key = sprintf('%s[%s]', $formName, $key);
            }
            $ret[$key] = $value;
        };

        foreach (['rule', 'map', 'weapon', 'rank', 'result', 'id_from', 'id_to'] as $key) {
            $value = $this->$key;
            if ((string)$value !== '') {
                $push($key, $value);
            }
        }

        $now = $_SERVER['REQUEST_TIME'] ?? time();
        $tz = Yii::$app->timeZone;
        switch ($this->term) {
            case 'this-period':
                $t = BattleHelper::periodToRange2(BattleHelper::calcPeriod2($now), 180);
                $push('term', 'term');
                $push('term_from', date('Y-m-d H:i:s', $t[0]));
                $push('term_to', date('Y-m-d H:i:s', $now));
                $push('timezone', $tz);
                break;

            case 'last-period':
                $t = BattleHelper::periodToRange2(BattleHelper::calcPeriod2($now) - 1, 180);
                $push('term', 'term');
                $push('term_from', date('Y-m-d H:i:s', $t[0]));
                $push('term_to', date('Y-m-d H:i:s', $t[1] - 1));
                $push('timezone', $tz);
                break;

            case '24h':
                $push('term', 'term');
                $push('term_from', date('Y-m-d H:i:s', $now - 86400));
                $push('term_to', date('Y-m-d H:i:s', $now));
                $push('timezone', $tz);
                break;

            case 'today':
                $push('term', 'term');
                $push('term_from', date('Y-m-d 00:00:00', $now));
                $push('term_to', date('Y-m-d H:i:s', $now));
                $push('timezone', $tz);
                break;

            case 'yesterday':
                $t = mktime(12, 0, 0, date('n', $now), date('j', $now) - 1, date('Y', $now));
                $push('term', 'term');
                $push('term_from', date('Y-m-d 00:00:00', $t));
                $push('term_to', date('Y-m-d 23:59:59', $t));
                $push('timezone', $tz);
                break;

            case 'term':
                $push('term', 'term');
                $push('term_from', date('Y-m-d H:i:s', strtotime($this->term_from)));
                $push('term_to', date('Y-m-d H:i:s', strtotime($this->term_to)));
                $push('timezone', $tz);
                break;

            default:
                if (preg_match('/^last-(\d+)-battles/', $this->term, $match)) {
                    $range = BattleHelper::getNBattlesRange2($this, (int)$match[1]);
                    if (!$range || $range['min_id'] < 1 || $range['max_id'] < 1) {
                        break;
                    }
                    $push('term', 'term');
                    $push('term_from', date('Y-m-d H:i:s', strtotime($range['min_at'])));
                    $push('term_to', date('Y-m-d H:i:s', strtotime($range['max_at'])));
                    $push('timezone', $tz);
                } elseif (preg_match('/^v\d+/', $this->term)) {
                    $push('term', $this->term);
                }
                break;
        }

        return $ret;
    }

    public function toQueryParams($formName = false)
    {
        if ($formName === false) {
            $formName = $this->formName();
        }

        $ret = [];
        $push = function ($key, $value) use ($formName, &$ret) {
            if ($formName != '' && $key !== 'screen_name') {
                $key = sprintf('%s[%s]', $formName, $key);
            }
            $ret[$key] = $value;
        };

        $termSubParams = ['term_from', 'term_to', 'timezone'];
        foreach ($this->attributes as $key => $value) {
            if (in_array($key, $termSubParams, true)) {
                continue;
            }
            if ((string)$value !== '') {
                $push($key, $value);
                if ($key === 'term') {
                    foreach ($termSubParams as $key2) {
                        $value2 = (string)$this->attributes[$key2];
                        if ($value2 !== '') {
                            $push($key2, $value2);
                        }
                    }
                }
            }
        }

        return $ret;
    }
}
