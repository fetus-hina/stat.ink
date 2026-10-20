<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\api\v1;

use app\models\Ability;
use app\models\Brand;
use app\models\GearType;
use yii\base\Model;
use yii\db\ActiveQuery;

class GearGetForm extends Model
{
    /** @var mixed */
    public $type;
    /** @var mixed */
    public $brand;
    /** @var mixed */
    public $ability;

    public function rules()
    {
        return [
            [['type'], 'exist',
                'targetClass' => GearType::class,
                'targetAttribute' => 'key',
            ],
            [['brand'], 'exist',
                'targetClass' => Brand::class,
                'targetAttribute' => 'key',
            ],
            [['ability'], 'exist',
                'targetClass' => Ability::class,
                'targetAttribute' => 'key',
            ],
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

    public function filterQuery(ActiveQuery $query): ActiveQuery
    {
        if ($this->type) {
            $query
                ->innerJoinWith('type')
                ->andWhere(['{{gear_type}}.[[key]]' => $this->type]);
        }
        if ($this->brand) {
            $query
                ->innerJoinWith('brand')
                ->andWhere(['{{brand}}.[[key]]' => $this->brand]);
        }
        if ($this->ability) {
            $query
                ->innerJoinWith('ability')
                ->andWhere(['{{ability}}.[[key]]' => $this->ability]);
        }
        return $query;
    }
}
