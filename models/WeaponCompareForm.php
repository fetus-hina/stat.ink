<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\validators\WeaponKeyValidator;
use app\models\Rule;
use yii\base\Model;

class WeaponCompareForm extends Model
{
    public $weapon1;
    public $weapon2;
    public $weapon3;
    public $weapon4;
    public $weapon5;
    public $rule1;
    public $rule2;
    public $rule3;
    public $rule4;
    public $rule5;


    public function formName()
    {
        return 'cmp';
    }

    public function rules()
    {
        $weapons = ['weapon1', 'weapon2', 'weapon3', 'weapon4', 'weapon5'];
        $rules = ['rule1', 'rule2', 'rule3', 'rule4', 'rule5'];
        return [
            [$weapons, WeaponKeyValidator::class],
            [$rules, 'safe',
                'when' => function ($model, $attribute) {
                    return $model->$attribute === '@gachi';
                },
            ],
            [$rules, 'exist',
                'targetClass' => Rule::class,
                'targetAttribute' => 'key',
                'when' => function ($model, $attribute) {
                    return $model->$attribute !== '@gachi';
                },
            ],
        ];
    }

    public function attributeLabels()
    {
        $ret = [];
        foreach (range(1, 5) as $i) {
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
        $index = 0;
        $push = function ($key, $value) use ($formName, &$ret) {
            if ($formName != '') {
                $key = sprintf('%s[%s]', $formName, $key);
            }
            $ret[$key] = $value;
        };

        foreach (range(1, 5) as $i) {
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
