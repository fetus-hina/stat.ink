<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\validators\WeaponKeyValidator;
use yii\base\InvalidParamException;
use yii\base\Model;

/**
 * @property string|null $rule1
 * @property string|null $rule2
 * @property string|null $rule3
 * @property string|null $rule4
 * @property string|null $rule5
 * @property string|null $rule6
 * @property string|null $rule7
 * @property string|null $rule8
 * @property string|null $rule9
 * @property string|null $rule10
 * @property string|null $rule11
 * @property string|null $rule12
 * @property string|null $rule13
 * @property string|null $rule14
 * @property string|null $rule15
 * @property string|null $rule16
 * @property string|null $rule17
 * @property string|null $rule18
 * @property string|null $rule19
 * @property string|null $rule20
 * @property string|null $weapon1
 * @property string|null $weapon2
 * @property string|null $weapon3
 * @property string|null $weapon4
 * @property string|null $weapon5
 * @property string|null $weapon6
 * @property string|null $weapon7
 * @property string|null $weapon8
 * @property string|null $weapon9
 * @property string|null $weapon10
 * @property string|null $weapon11
 * @property string|null $weapon12
 * @property string|null $weapon13
 * @property string|null $weapon14
 * @property string|null $weapon15
 * @property string|null $weapon16
 * @property string|null $weapon17
 * @property string|null $weapon18
 * @property string|null $weapon19
 * @property string|null $weapon20
 */
final class WeaponCompareForm extends Model
{
    public const NUMBER = 20;

    private $attributes = [];

    public function hasAttribute(string $name): bool
    {
        if (!preg_match('/^(?:weapon|rule)(\d+)$/', $name, $match)) {
            return false;
        }
        return $match[1] >= 1 && $match[1] <= static::NUMBER;
    }

    public function getAttribute(string $name)
    {
        if (!$this->hasAttribute($name)) {
            throw new InvalidParamException(static::class . ' has no attribute named "' . $name . '".');
        }
        return $this->attributes[$name] ?? null;
    }

    public function setAttribute(string $name, $value): self
    {
        if (!$this->hasAttribute($name)) {
            throw new InvalidParamException(static::class . ' has no attribute named "' . $name . '".');
        }
        $this->attributes[$name] = $value;
        return $this;
    }

    public function __get($name)
    {
        if ($this->hasAttribute($name)) {
            return $this->getAttribute($name);
        } else {
            return parent::__get($name);
        }
    }

    public function __set($name, $value)
    {
        if ($this->hasAttribute($name)) {
            $this->setAttribute($name, $value);
        } else {
            parent::__set($name, $value);
        }
    }

    public function __isset($name): bool
    {
        if ($this->hasAttribute($name)) {
            return $this->getAttribute($name) !== null;
        } else {
            return parent::__isset($name);
        }
    }

    public function __unset($name)
    {
        if ($this->hasAttribute($name)) {
            $this->setAttribute($name, null);
        } else {
            parent::__unset($name);
        }
    }

    public function formName()
    {
        return 'cmp';
    }

    public function rules()
    {
        $weapons = array_map(fn ($i) => "weapon{$i}", range(1, static::NUMBER));
        $rules = array_map(fn ($i) => "rule{$i}", range(1, static::NUMBER));
        return [
            [$weapons, WeaponKeyValidator::class],
            [$rules, 'safe',
                'when' => fn ($model, $attribute) => $model->$attribute === '@gachi',
            ],
            [$rules, 'exist',
                'targetClass' => Rule::class,
                'targetAttribute' => 'key',
                'when' => fn ($model, $attribute) => $model->$attribute !== '@gachi',
            ],
        ];
    }

    public function attributeLabels()
    {
        $ret = [];
        foreach (range(1, static::NUMBER) as $i) {
            $ret["weapon{$i}"] = Yii::t('app', 'Weapon') . ' ' . $i;
            $ret["rule{$i}"] = Yii::t('app', 'Rule') . ' ' . $i;
        }
        return $ret;
    }

    public function toQueryParams($formName = false)
    {
        if ($formName === false) {
            $formName = $this->formName();
        }

        $ret = [];
        $index = 1;
        $push = function ($key, $value) use ($formName, &$ret) {
            if ($formName != '') {
                $key = sprintf('%s[%s]', $formName, $key);
            }
            $ret[$key] = $value;
        };

        foreach (range(1, static::NUMBER) as $i) {
            $weaponKey = "weapon{$i}";
            $ruleKey = "rule{$i}";
            if (trim($this->$weaponKey) !== '') {
                $push("weapon{$index}", $this->$weaponKey);
                if (trim($this->$ruleKey) !== '') {
                    $push("rule{$index}", $this->$ruleKey);
                }
                ++$index;
            }
        }

        return $ret;
    }
}
