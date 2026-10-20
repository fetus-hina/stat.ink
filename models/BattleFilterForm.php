<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

use Override;
use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\components\helpers\TypeHelper;
use yii\base\Model;
use yii\db\ActiveRecord;

use function array_map;
use function array_merge;
use function date;
use function in_array;
use function is_scalar;
use function mktime;
use function preg_match;
use function sprintf;
use function strtotime;
use function substr;
use function time;
use function trim;

class BattleFilterForm extends Model
{
    /**
     * @var string|null
     */
    public $screen_name;

    /**
     * @var string|null
     */
    public $lobby;
    /**
     * @var string|null
     */
    public $rule;
    /**
     * @var string|null
     */
    public $map;
    /**
     * @var string|null
     */
    public $weapon;
    /**
     * @var string|null
     */
    public $rank;
    /**
     * @var string|null
     */
    public $result;
    /**
     * @var string|null
     */
    public $term;
    /**
     * @var string|null
     */
    public $term_from;
    /**
     * @var string|null
     */
    public $term_to;
    /**
     * @var string|null
     */
    public $timezone;
    /**
     * @var string|null
     */
    public $id_from;
    /**
     * @var string|null
     */
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
            [['lobby'], 'exist',
                'targetClass' => Lobby::class,
                'targetAttribute' => 'key',
            ],
            [['rule'], 'exist',
                'targetClass' => Rule::class,
                'targetAttribute' => 'key',
                'when' => fn () => substr($this->rule, 0, 1) !== '@',
            ],
            [['rule'], 'validateGameMode',
                'when' => fn () => substr($this->rule, 0, 1) === '@',
            ],
            [['map'], 'exist',
                'targetClass' => Map::class,
                'targetAttribute' => 'key',
            ],
            [['weapon'], 'exist',
                'targetClass' => Weapon::class,
                'targetAttribute' => 'key',
                'when' => fn () => !in_array(substr($this->weapon, 0, 1), ['@', '+', '*', '~'], true),
            ],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => WeaponType::class,
                ],
                'when' => fn () => substr($this->weapon, 0, 1) === '@',
            ],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => Subweapon::class,
                ],
                'when' => fn () => substr($this->weapon, 0, 1) === '+',
            ],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => Special::class,
                ],
                'when' => fn () => substr($this->weapon, 0, 1) === '*',
            ],
            [['weapon'], 'validateRepresentativeWeapon',
                'when' => fn () => substr($this->weapon, 0, 1) === '~',
            ],
            [['rank'], 'exist',
                'targetClass' => Rank::class, 'targetAttribute' => 'key',
                'when' => fn () => substr($this->rank, 0, 1) !== '~',
            ],
            [['rank'], 'validateRankGroup',
                'when' => fn () => substr($this->rank, 0, 1) === '~',
            ],
            [['result'], 'boolean', 'trueValue' => 'win', 'falseValue' => 'lose'],
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
                        fn ($a) => 'v' . $a['tag'],
                        SplatoonVersion::find()->asArray()->all(),
                    ),
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
            'screen_name' => Yii::t('app', 'Screen Name'),
            'lobby' => Yii::t('app', 'Lobby'),
            'rule' => Yii::t('app', 'Mode'),
            'map' => Yii::t('app', 'Stage'),
            'weapon' => Yii::t('app', 'Weapon'),
            'rank' => Yii::t('app', 'Rank'),
            'result' => Yii::t('app-results', 'Result'),
            'term' => Yii::t('app', 'Term'),
            'term_from' => Yii::t('app-filter', 'Period From'),
            'term_to' => Yii::t('app-filter', 'Period To'),
            'id_from' => Yii::t('app-filter', 'ID From'),
            'id_to' => Yii::t('app-filter', 'ID To'),
        ];
    }

    /**
     * @param array<mixed> $data
     */
    #[Override]
    public function load($data, $formName = null)
    {
        foreach (['id_from', 'id_to'] as $key) {
            if (isset($data[$key])) {
                $value = $data[$key];
                if (is_scalar($value) && trim((string)$value) !== '') {
                    $this->$key = trim((string)$value);
                }
            }
        }
        return parent::load($data, $formName);
    }

    public function validateGameMode(string $attr, mixed $params): void
    {
        $value = substr($this->$attr, 1);
        $isExist = !!GameMode::findOne(['key' => $value]);
        if (!$isExist) {
            $this->addError(
                $attr,
                Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => $this->getAttributeLabel($attr),
                ]),
            );
        }
    }

    /**
     * @param array{modelClass: class-string<ActiveRecord>} $params
     */
    public function validateWeapon(string $attr, mixed $params): void
    {
        $value = substr($this->$attr, 1);
        $isExist = !!$params['modelClass']::findOne(['key' => $value]);
        if (!$isExist) {
            $this->addError(
                $attr,
                Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => $this->getAttributeLabel($attr),
                ]),
            );
        }
    }

    public function validateRepresentativeWeapon(string $attr, mixed $params): void
    {
        $value = substr($this->$attr, 1);
        $count = Weapon::find()
            ->andWhere('{{weapon}}.[[id]] = {{weapon}}.[[main_group_id]]')
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

    public function validateRankGroup(string $attr, mixed $params): void
    {
        $value = substr($this->$attr, 1);
        $count = RankGroup::find()
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

    public function validateTimezone(string $attr, mixed $params): void
    {
        $value = $this->$attr;
        if (is_scalar($value) && $value != '') {
            $c = Timezone::find()->where(['identifier' => $value])->orderBy(null)->count();
            if ($c == 1) {
                return;
            }
        }
        $this->$attr = Yii::$app->timeZone;
    }

    /**
     * @return array<string, mixed>
     */
    public function toPermLink(string|false $formName = false): array
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

        foreach (['lobby', 'rule', 'map', 'weapon', 'rank', 'result', 'id_from', 'id_to'] as $key) {
            $value = $this->$key;
            if ((string)$value !== '') {
                $push($key, $value);
            }
        }

        $now = $_SERVER['REQUEST_TIME'] ?? time();
        $tz = Yii::$app->timeZone;
        switch ($this->term) {
            case 'this-period':
                $t = BattleHelper::periodToRange(BattleHelper::calcPeriod($now), 180);
                $push('term', 'term');
                $push('term_from', date('Y-m-d H:i:s', $t[0]));
                $push('term_to', date('Y-m-d H:i:s', $now));
                $push('timezone', $tz);
                break;

            case 'last-period':
                $t = BattleHelper::periodToRange(BattleHelper::calcPeriod($now - 14400), 180);
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
                $t = TypeHelper::int(mktime(12, 0, 0, (int)date('n', $now), (int)date('j', $now) - 1, (int)date('Y', $now)));
                $push('term', 'term');
                $push('term_from', date('Y-m-d 00:00:00', $t));
                $push('term_to', date('Y-m-d 23:59:59', $t));
                $push('timezone', $tz);
                break;

            case 'term':
                $push('term', 'term');
                $push('term_from', date('Y-m-d H:i:s', $this->term_from |> strtotime(...) |> TypeHelper::int(...)));
                $push('term_to', date('Y-m-d H:i:s', $this->term_to |> strtotime(...) |> TypeHelper::int(...)));
                $push('timezone', $tz);
                break;

            default:
                if (preg_match('/^last-(\d+)-battles/', $this->term, $match)) {
                    $range = BattleHelper::getNBattlesRange($this, (int)$match[1]);
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

    /**
     * @return array<string, mixed>
     */
    public function toQueryParams(string|false $formName = false): array
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
