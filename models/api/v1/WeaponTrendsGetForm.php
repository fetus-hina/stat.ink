<?php

/**
 * @copyright Copyright (C) 2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\api\v1;

use app\models\Map;
use app\models\Rule;
use yii\base\Model;

/**
 * @property-read int|null $ruleId
 * @property-read int|null $mapId
 */
final class WeaponTrendsGetForm extends Model
{
    public $rule;
    public $map;

    public function rules()
    {
        return [
            [['rule', 'map'], 'required'],
            [['rule'], 'exist',
                'targetClass' => Rule::class,
                'targetAttribute' => 'key',
            ],
            [['map'], 'exist',
                'targetClass' => Map::class,
                'targetAttribute' => 'key',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'rule' => 'Mode(rule) Key',
            'map' => 'Stage(map) Key',
        ];
    }

    public function getRuleId(): ?int
    {
        return Rule::findOne(['key' => $this->rule])->id ?? null;
    }

    public function getMapId(): ?int
    {
        return Map::findOne(['key' => $this->map])->id ?? null;
    }
}
