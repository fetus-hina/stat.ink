<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use Yii;
use app\components\helpers\Battle as BattleHelper;
use yii\base\Model;

class Battle2FilterForm extends Model
{
    public $screen_name;

    public $rule;
    public $map;
    public $weapon;
    public $rank;
    public $result;
    public $has_disconnect;
    public $term;
    public $term_from;
    public $term_to;
    public $timezone;
    public $id_from; // old, for compatibility. Use filterIdRange.
    public $id_to; // old, for compatibility. Use filterIdRange.
    public $filter;
    public $with_team; // "good" or "bad", refs $filterWithPrincipalId

    private $filterTeam;
    private $filterIdRange; // [ from, to ]
    private $filterPeriod; // [ from, to ]
    private $filterWithPrincipalId;

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
                    'standard-gachi-asari',
                    'any-gachi-any',
                    'any-gachi-area',
                    'any-gachi-yagura',
                    'any-gachi-hoko',
                    'any-gachi-asari',
                    'any_squad-gachi-any',
                    'any_squad-gachi-area',
                    'any_squad-gachi-yagura',
                    'any_squad-gachi-hoko',
                    'any_squad-gachi-asari',
                    'squad_2-gachi-any',
                    'squad_2-gachi-area',
                    'squad_2-gachi-yagura',
                    'squad_2-gachi-hoko',
                    'squad_2-gachi-asari',
                    'squad_4-gachi-any',
                    'squad_4-gachi-area',
                    'squad_4-gachi-yagura',
                    'squad_4-gachi-hoko',
                    'squad_4-gachi-asari',
                    'any-fest-nawabari',
                    'fest_normal-fest-nawabari',
                    'standard-fest-nawabari',
                    'squad_4-fest-nawabari',
                    'private-private-any',
                    'private-private-nawabari',
                    'private-private-gachi',
                    'private-private-area',
                    'private-private-yagura',
                    'private-private-hoko',
                    'private-private-asari',
                ],
            ],
            [['map'], 'exist',
                'targetClass' => Map2::class,
                'targetAttribute' => 'key',
            ],
            [['weapon'], 'exist',
                'targetClass' => Weapon2::class,
                'targetAttribute' => 'key',
                'when' => fn (): bool => !in_array(substr($this->weapon, 0, 1), ['@', '+', '*', '~'], true),
            ],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => WeaponType2::class,
                ],
                'when' => fn (): bool => substr($this->weapon, 0, 1) === '@',
            ],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => Subweapon2::class,
                ],
                'when' => fn (): bool => substr($this->weapon, 0, 1) === '+',
            ],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => Special2::class,
                ],
                'when' => fn (): bool => substr($this->weapon, 0, 1) === '*',
            ],
            [['weapon'], 'validateRepresentativeWeapon',
                'when' => fn (): bool => substr($this->weapon, 0, 1) === '~',
            ],
            [['rank'], 'exist',
                'targetClass' => Rank2::class,
                'targetAttribute' => 'key',
                'when' => fn (): bool => substr($this->rank, 0, 1) !== '~',
            ],
            [['rank'], 'validateRankGroup',
                'when' => fn () => substr($this->rank, 0, 1) === '~',
            ],
            [['result'], 'boolean',
                'trueValue' => 'win',
                'falseValue' => 'lose',
            ],
            [['has_disconnect'], 'boolean',
                'trueValue' => 'yes',
                'falseValue' => 'no',
            ],
            [['term'], 'in',
                'range' => array_merge(
                    [
                        'this-period',
                        'last-period',
                        'last-2-periods',
                        'last-3-periods',
                        'last-4-periods',
                        '24h',
                        'today',
                        'yesterday',
                        'this-month-utc',
                        'last-month-utc',
                        'last-10-battles',
                        'last-20-battles',
                        'last-50-battles',
                        'last-100-battles',
                        'last-200-battles',
                        'this-fest',
                        'term',
                    ],
                    array_map(
                        fn (array $a): string => '~v' . $a['tag'],
                        SplatoonVersionGroup2::find()
                            ->asArray()
                            ->all(),
                    ),
                    array_map(
                        fn (array $a): string => 'v' . $a['tag'],
                        SplatoonVersion2::find()
                            ->asArray()
                            ->all(),
                    ),
                ),
            ],
            [['term_from', 'term_to'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['timezone'], 'validateTimezone', 'skipOnEmpty' => false],
            [['id_from', 'id_to'], 'integer', 'min' => 1],
            [['filter'], 'validateFilter', 'skipOnEmpty' => true],
            [['with_team'], 'string'],
            [['with_team'], 'in', 'range' => ['good', 'bad']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'screen_name' => Yii::t('app', 'Screen Name'),
            'rule' => Yii::t('app', 'Mode'),
            'map' => Yii::t('app', 'Stage'),
            'weapon' => Yii::t('app', 'Weapon'),
            'rank' => Yii::t('app', 'Rank'),
            'result' => Yii::t('app', 'Result'),
            'has_disconnect' => Yii::t('app', 'Connectivity'),
            'term' => Yii::t('app', 'Term'),
            'term_from' => Yii::t('app', 'Period From'),
            'term_to' => Yii::t('app', 'Period To'),
            'id_from' => Yii::t('app', 'ID From'),
            'id_to' => Yii::t('app', 'ID To'),
            'filter' => Yii::t('app', 'Filter'),
            'with_team' => Yii::t('app', 'Target Player\'s Team'),
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

    public function validateWeapon(string $attr, $params): void
    {
        $value = substr($this->$attr, 1);
        $method = [$params['modelClass'], 'findOne'];
        $isExist = !!call_user_func($method, ['key' => $value]);
        if (!$isExist) {
            $this->addError(
                $attr,
                Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => $this->getAttributeLabel($attr),
                ]),
            );
        }
    }

    public function validateRepresentativeWeapon(string $attr, $params): void
    {
        $value = substr($this->$attr, 1);
        $count = Weapon2::find()
            ->andWhere('{{weapon2}}.[[id]] = {{weapon2}}.[[main_group_id]]')
            ->andWhere(['key' => $value])
            ->count();
        if ($count < 1) {
            $this->addError(
                $attr,
                Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => $this->getAttributeLabel($attr),
                ]),
            );
        }
    }

    public function validateRankGroup(string $attr, $params): void
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
                ]),
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

    public function validateFilter($attr, $params)
    {
        $value = trim((string)($this->$attr));
        if ($value === '' || $this->hasErrors($attr)) {
            return;
        }

        try {
            foreach (explode(' ', $value) as $v) {
                $v = trim($v);
                if ($v === '') {
                    continue;
                }

                $pos = strpos($v, ':');
                if ($pos === false || $pos < 1) {
                    throw new Exception();
                }

                switch (substr($v, 0, $pos)) {
                    case 'team':
                        $v = substr($v, $pos + 1);
                        if (!$this->isValidTeamFilter($v)) {
                            throw new Exception();
                        }
                        $this->filterTeam = $v;
                        return;

                    case 'id':
                        if (!preg_match('/^(\d+)-(\d+)$/', substr($v, $pos + 1), $match)) {
                            throw new Exception();
                        }
                        $this->filterIdRange = [
                            (int)$match[1],
                            (int)$match[2],
                        ];
                        return;

                    case 'period':
                        $v = substr($v, $pos + 1);
                        if (preg_match('/^\d+$/', $v)) {
                            $this->filterPeriod = [(int)$v, (int)$v];
                        } elseif (preg_match('/^(\d+)-(\d+)$/', $v, $match)) {
                            $this->filterPeriod = [
                                (int)$match[1],
                                (int)$match[2],
                            ];
                        } else {
                            throw new Exception();
                        }
                        return;

                    case 'with':
                        $v = substr($v, $pos + 1);
                        if (preg_match('/^[0-9a-f]{16}$/', $v)) {
                            $this->filterWithPrincipalId = $v;
                        } else {
                            throw new Exception();
                        }
                        return;

                    default:
                        throw new Exception();
                }
            }
        } catch (\Throwable $e) {
            $this->addError($attr, Yii::t('yii', '{attribute} is invalid.', [
                'attribute' => $this->getAttributeLabel($attr),
            ]));
        }
    }

    protected function isValidTeamFilter(string $value): bool
    {
        return !!preg_match('/^[0-9a-zA-Z]+$/', $value);
    }

    public function toPermLink($formName = false)
    {
        if ($formName === false) {
            $formName = $this->formName();
        }

        $ret = [];
        $push = function ($key, $value) use ($formName, &$ret): void {
            if ($formName != '') {
                $key = sprintf('%s[%s]', $formName, $key);
            }
            $ret[$key] = $value;
        };

        $pushFilter = function ($key, $value) use ($formName, &$ret): void {
            $formKey = 'filter';
            if ($formName != '') {
                $formKey = sprintf('%s[filter]', $formName);
            }

            $values = array_filter(
                explode(' ', (string)($ret[$formKey] ?? '')),
            );
            $values = array_filter(
                $values,
                fn (string $_): bool => substr($_, 0, strlen($key) + 1) !== $key . ':',
            );
            $values[] = $key . ':' . $value;
            $ret[$formKey] = implode(' ', $values);
        };

        $copyKeys = [
            'rule',
            'map',
            'weapon',
            'rank',
            'result',
            'has_disconnect',
            'id_from',
            'id_to',
            'filter',
        ];
        foreach ($copyKeys as $key) {
            $value = $this->$key;
            if ((string)$value !== '') {
                $push($key, $value);
            }
        }

        if (
            preg_match('/^[0-9a-f]{16}$/', (string)$this->filterWithPrincipalId) &&
            in_array((string)$this->with_team, ['good', 'bad'])
        ) {
            $push('with_team', $this->with_team);
        }

        $now = $_SERVER['REQUEST_TIME'] ?? time();
        $tz = Yii::$app->timeZone;
        switch ($this->term) {
            case 'this-period':
                $pushFilter('period', (string)BattleHelper::calcPeriod2($now));
                break;

            case 'last-period':
                $pushFilter('period', (string)BattleHelper::calcPeriod2($now) - 1);
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

            case 'this-month-utc':
                $utcNow = (new DateTimeImmutable())
                    ->setTimezone(new DateTimeZone('Etc/UTC'))
                    ->setTimestamp($now);
                $thisMonth = (new DateTimeImmutable())
                    ->setTimezone(new DateTimeZone('Etc/UTC'))
                    ->setDate($utcNow->format('Y'), $utcNow->format('n'), 1)
                    ->setTime(0, 0, 0);
                $pushFilter('period', vsprintf('%d-%d', [
                    BattleHelper::calcPeriod2($thisMonth->getTimestamp()),
                    BattleHelper::calcPeriod2($utcNow->getTimestamp()),
                ]));
                break;

            case 'last-month-utc':
                $utcNow = (new DateTimeImmutable())
                    ->setTimezone(new DateTimeZone('Etc/UTC'))
                    ->setTimestamp($now);

                $lastMonthPeriod = BattleHelper::calcPeriod2(
                    (new DateTimeImmutable())
                        ->setTimezone(new DateTimeZone('Etc/UTC'))
                        ->setDate((int)$utcNow->format('Y'), (int)$utcNow->format('n') - 1, 1)
                        ->setTime(0, 0, 0)
                        ->getTimestamp(),
                );

                $thisMonthPeriod = BattleHelper::calcPeriod2(
                    (new DateTimeImmutable())
                        ->setTimezone(new DateTimeZone('Etc/UTC'))
                        ->setDate($utcNow->format('Y'), $utcNow->format('n'), 1)
                        ->setTime(0, 0, 0)
                        ->getTimestamp(),
                );
                $pushFilter('period', vsprintf('%d-%d', [
                    $lastMonthPeriod,
                    $thisMonthPeriod - 1,
                ]));
                break;

            case 'this-fest':
                $user = User::findOne(['screen_name' => $this->screen_name]);
                if ($user && ($_ = BattleHelper::getLastPlayedSplatfestPeriodRange2($user))) {
                    $pushFilter('period', vsprintf('%d-%d', $_));
                } else {
                    $pushFilter('period', '0');
                }
                break;

            case 'term':
                $from = strtotime($this->term_from);
                if ($from === false) {
                    $from = 0; // 1970-01-01 00:00:00 UTC
                }
                $to = strtotime($this->term_to);
                if ($to === false) {
                    $to = $_SERVER['REQUEST_TIME'] ?? time();
                }
                $push('term', 'term');
                $push('term_from', date('Y-m-d H:i:s', $from));
                $push('term_to', date('Y-m-d H:i:s', $to));
                $push('timezone', $tz);
                break;

            default:
                if (preg_match('/^last-(\d+)-battles/', $this->term, $match)) {
                    $range = BattleHelper::getNBattlesRange2($this, (int)$match[1]);
                    if (!$range || $range['min_id'] < 1 || $range['max_id'] < 1) {
                        break;
                    }
                    $pushFilter(
                        'id',
                        sprintf('%d-%d', (int)$range['min_id'], (int)$range['max_id']),
                    );
                } elseif (preg_match('/^last-(\d+)-periods/', $this->term, $match)) {
                    $currentPeriod = BattleHelper::calcPeriod2($now);
                    $pushFilter(
                        'period',
                        sprintf('%d-%d', $currentPeriod - $match[1] + 1, $currentPeriod),
                    );
                } elseif (preg_match('/^~?v\d+/', $this->term)) {
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

    public function getFilterTeam(): ?string
    {
        return $this->filterTeam;
    }

    public function getFilterIdRange(): ?array
    {
        return $this->filterIdRange;
    }

    public function getFilterPeriod(): ?array
    {
        return $this->filterPeriod;
    }

    public function getFilterWithPrincipalId(): ?string
    {
        return $this->filterWithPrincipalId;
    }
}
