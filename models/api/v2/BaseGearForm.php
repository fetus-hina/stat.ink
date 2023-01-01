<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v2;

use app\components\behaviors\SplatnetNumberBehavior;
use app\models\Ability2;
use app\models\Gear2;
use app\models\GearType;
use yii\base\Model;
use yii\db\Query;

abstract class BaseGearForm extends Model
{
    public $gear;
    public $primary_ability;
    public $secondary_abilities;

    // subclass should return one of "headgear", "clothing" or "shoes".
    abstract public function getType();

    public function behaviors()
    {
        $type = GearType::findOne(['key' => $this->getType()]);

        return [
            [
                'class' => SplatnetNumberBehavior::class,
                'attribute' => 'gear',
                'tableName' => '{{gear2}}',
                'modifier' => function (Query $query) use ($type) {
                    $query->andWhere(['type_id' => $type->id]);
                },
            ],
        ];
    }

    public function rules()
    {
        return [
            [['gear'], 'exist',
                'targetClass' => Gear2::class,
                'targetAttribute' => 'key',
                'message' => 'Unknown key-string "{value}"',
            ],
            [['gear'], 'validateGearType'],
            [['primary_ability'], 'exist',
                'targetClass' => Ability2::class,
                'targetAttribute' => 'key',
                'message' => 'Unknown key-string "{value}"',
            ],
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

    // サブのギアパワーを検証する。
    // 配列で0～3要素の Ability2::key になっていなければならない
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
        if (count($values) > 3) {
            $this->addError($attribute, "{$attribute} must be contain 0-3 values, " . count($values) . ' given.');
            return;
        }
        foreach ($values as $i => $value) {
            $value = (string)$value;
            if ($value == '') {
                continue;
            }
            $ability = Ability2::findOne(['key' => $value]);
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
                : Gear2::findOne(['key' => (string)$this->gear]);
        }
        return $this->gearModel;
    }
}
