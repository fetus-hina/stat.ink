<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v1;

use app\models\Ability;
use app\models\Gear;
use yii\base\Model;

abstract class BaseGearForm extends Model
{
    public $gear;
    public $primary_ability;
    public $secondary_abilities;

    // subclass should return one of "headgear", "clothing" or "shoes".
    abstract public function getType();

    public function rules()
    {
        return [
            [['gear'], 'exist',
                'targetClass' => Gear::class,
                'targetAttribute' => 'key'],
            [['gear'], 'validateGearType'],
            [['gear'], 'fixPrimaryAbility'],
            [['primary_ability'], 'exist',
                'targetClass' => Ability::class,
                'targetAttribute' => 'key'],
            [['secondary_abilities'], 'validateSecondaryAbilities'],
        ];
    }

    // ギアが明示されたときに、装着部位が正しいかを検証する
    // （アタマのギアがクツについていないか）
    public function validateGearType($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        if (!$loaded = $this->getGearModel()) {
            return;
        }
        if ($loaded->type->key !== $this->getType()) {
            $this->addError($attribute, sprintf(
                'Gear type mismatch. input=%s, require=%s',
                $loaded->type->key,
                $this->getType(),
            ));
        }
    }

    // ギアが明示された時に、メインのギアパワーを設定/修正する
    public function fixPrimaryAbility($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        if (!$loaded = $this->getGearModel()) {
            return;
        }
        if (!$loaded->ability) {
            return;
        }
        $this->primary_ability = $loaded->ability->key;
    }

    // サブのギアパワーを検証する。
    // 配列で1～3要素の Ability::key になっていなければならない
    // ただし、nullは許容する(null=locked)
    public function validateSecondaryAbilities($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        $values = $this->$attribute;
        if (!is_array($values)) {
            $this->addError($attribute, "{$attribute} must be an array of key-string.");
            return;
        }
        if (count($values) < 1 || count($values) > 3) {
            $this->addError($attribute, "{$attribute} must be contain 1-3 values, " . count($values) . " given.");
            return;
        }
        foreach ($values as $i => $value) {
            $value = (string)$value;
            if ($value == '') {
                continue;
            }
            $ability = Ability::findOne(['key' => $value]);
            if (!$ability) {
                $this->addError($attribute, "[{$i}] {$value} is invalid key-string.");
            }
        }
    }

    private $gearModel = false;
    public function getGearModel()
    {
        if ($this->gearModel === false) {
            $this->gearModel = $this->gear == ''
                ? null
                : Gear::findOne(['key' => (string)$this->gear]);
        }
        return $this->gearModel;
    }
}
