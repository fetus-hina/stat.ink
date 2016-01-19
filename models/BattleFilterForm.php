<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\helpers\db\Now;
use app\components\helpers\Battle as BattleHelper;
use app\models\{
    Battle,
    GameMode,
    Lobby,
    Map,
    Rank,
    Rule,
    Special,
    Subweapon,
    Timezone,
    User,
    Weapon
};

class BattleFilterForm extends Model
{
    public $screen_name;

    public $lobby;
    public $rule;
    public $map;
    public $weapon;
    public $result;
    public $term;
    public $term_from;
    public $term_to;
    public $timezone;

    public function formName()
    {
        return 'filter';
    }

    public function rules()
    {
        return [
            [['screen_name'], 'exist',
                'targetClass' => User::class,
                'targetAttribute' => 'screen_name'],
            [['lobby'], 'exist',
                'targetClass' => Lobby::class,
                'targetAttribute' => 'key'],
            [['rule'], 'exist',
                'targetClass' => Rule::class,
                'targetAttribute' => 'key',
                'when' => function () {
                    return substr($this->rule, 0, 1) !== '@';
                }],
            [['rule'], 'validateGameMode',
                'when' => function () {
                    return substr($this->rule, 0, 1) === '@';
                }],
            [['map'], 'exist',
                'targetClass' => Map::class,
                'targetAttribute' => 'key'],
            [['weapon'], 'exist',
                'targetClass' => Weapon::class,
                'targetAttribute' => 'key',
                'when' => function () {
                    return !in_array(substr($this->weapon, 0, 1), ['@', '+', '*'], true);
                }],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => WeaponType::class,
                ],
                'when' => function () {
                    return substr($this->weapon, 0, 1) === '@';
                }],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => Subweapon::class,
                ],
                'when' => function () {
                    return substr($this->weapon, 0, 1) === '+';
                }],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => Special::class,
                ],
                'when' => function () {
                    return substr($this->weapon, 0, 1) === '*';
                }],
            [['result'], 'boolean', 'trueValue' => 'win', 'falseValue' => 'lose'],
            [['term'], 'in', 'range' => [
                'this-period',
                'last-period',
                '24h',
                'today',
                'yesterday',
                'term',
            ]],
            [['term_from', 'term_to'], 'date', 'format' => 'yyyy-M-d H:m:s'],
            [['timezone'], 'validateTimezone', 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'screen_name'   => Yii::t('app', 'Screen Name'),
            'lobby'         => Yii::t('app', 'Lobby'),
            'rule'          => Yii::t('app', 'Mode'),
            'map'           => Yii::t('app', 'Stage'),
            'weapon'        => Yii::t('app', 'Weapon'),
            'result'        => Yii::t('app', 'Result'),
            'term'          => Yii::t('app', 'Term'),
            'term_from'     => Yii::t('app', 'Period From'),
            'term_to'       => Yii::t('app', 'Period To'),
        ];
    }

    public function validateGameMode($attr, $params)
    {
        $value = substr($this->$attr, 1);
        $isExist = !!GameMode::findOne(['key' => $value]);
        if (!$isExist) {
            $this->addError(
                $attr,
                Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => $this->getAttributeLabel($attr),
                ])
            );
        }
    }

    public function validateWeapon($attr, $params)
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

    public function validateTimezone($attr, $params)
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

        foreach (['lobby', 'rule', 'map', 'weapon', 'result'] as $key) {
            $value = $this->$key;
            if ((string)$value !== '') {
                $push($key, $value);
            }
        }

        $now = @$_SERVER['REQUEST_TIME'] ?: time();
        $tz = Yii::$app->timeZone;
        switch ($this->term) {
            case 'this-period':
                $t = BattleHelper::periodToRange(BattleHelper::calcPeriod($now), 180);
                $push('term', 'term');
                $push('term_from', date('Y-m-d H:i:s', $t[0]));
                $push('term_to', date('Y-m-d H:i:s', $t[1] - 1));
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
                $push('term_to', date('Y-m-d 23:59:59', $now));
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

        foreach ($this->attributes as $key => $value) {
            if ((string)$value !== '') {
                $push($key, $value);
            }
        }

        return $ret;
    }
}
