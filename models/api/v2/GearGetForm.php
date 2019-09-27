<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v2;

use Yii;
use app\models\Ability2;
use app\models\Brand2;
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
            [['type', 'brand', 'ability'], 'string'],
            [['type'], 'exist',
                'targetClass' => GearType::class,
                'targetAttribute' => 'key'],
            [['brand'], 'exist',
                'targetClass' => Brand2::class,
                'targetAttribute' => 'key'],
            [['ability'], 'exist',
                'targetClass' => Ability2::class,
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
        return $query
            ->joinWith(['type', 'brand', 'ability'])
            ->andFilterWhere(['{{gear_type}}.[[key]]' => $this->type])
            ->andFilterWhere(['{{brand2}}.[[key]]' => $this->brand])
            ->andFilterWhere(['{{ability2}}.[[key]]' => $this->ability]);
    }
}
