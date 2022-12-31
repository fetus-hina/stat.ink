<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v1;

use Yii;
use app\models\Ability;
use app\models\Brand;
use app\models\GearType;
use yii\base\Model;
use yii\db\ActiveQuery;

class GearGetForm extends Model
{
    public $type;
    public $brand;
    public $ability;

    public function rules()
    {
        return [
            [['type'], 'exist',
                'targetClass' => GearType::class,
                'targetAttribute' => 'key'],
            [['brand'], 'exist',
                'targetClass' => Brand::class,
                'targetAttribute' => 'key'],
            [['ability'], 'exist',
                'targetClass' => Ability::class,
                'targetAttribute' => 'key'],
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

    public function filterQuery(ActiveQuery $query)
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
