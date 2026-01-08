<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v2;

use Yii;
use app\models\Ability2;
use app\models\Brand2;
use app\models\GearType;
use app\models\openapi\Util as OapiUtil;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function implode;

use const SORT_ASC;

class GearGetForm extends Model
{
    use OapiUtil;

    public $type;
    public $brand;
    public $ability;

    public function rules()
    {
        return [
            [['type', 'brand', 'ability'], 'string'],
            [['type'], 'exist',
                'targetClass' => GearType::class,
                'targetAttribute' => 'key',
            ],
            [['brand'], 'exist',
                'targetClass' => Brand2::class,
                'targetAttribute' => 'key',
            ],
            [['ability'], 'exist',
                'targetClass' => Ability2::class,
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

    public function filterQuery(ActiveQuery $query)
    {
        return $query
            ->innerJoinWith(['type', 'brand'])
            ->joinWith(['ability'])
            ->andFilterWhere(['{{gear_type}}.[[key]]' => $this->type])
            ->andFilterWhere(['{{brand2}}.[[key]]' => $this->brand])
            ->andFilterWhere(['{{ability2}}.[[key]]' => $this->ability]);
    }

    public static function oapiParameters(): array
    {
        return [
            [
                'in' => 'query',
                'name' => 'type',
                'required' => false,
                'schema' => [
                    'type' => 'string',
                    'enum' => ArrayHelper::getColumn(
                        GearType::find()->orderBy(['key' => SORT_ASC])->all(),
                        'key',
                    ),
                ],
                'description' => implode("\n\n", [
                    Html::encode(Yii::t('app-apidoc2', 'Filter by gear category')),
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc2', 'Gear category'),
                        'app-gear',
                        GearType::find()->orderBy(['key' => SORT_ASC])->all(),
                    ),
                ]),
            ],
            [
                'in' => 'query',
                'name' => 'brand',
                'required' => false,
                'schema' => [
                    'type' => 'string',
                    'enum' => ArrayHelper::getColumn(
                        Brand2::find()->orderBy(['key' => SORT_ASC])->all(),
                        'key',
                    ),
                ],
                'description' => implode("\n\n", [
                    Html::encode(Yii::t('app-apidoc2', 'Filter by brand')),
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc2', 'Brand'),
                        'app-brand2',
                        Brand2::find()->orderBy(['key' => SORT_ASC])->all(),
                    ),
                ]),
            ],
            [
                'in' => 'query',
                'name' => 'ability',
                'required' => false,
                'schema' => [
                    'type' => 'string',
                    'enum' => ArrayHelper::getColumn(
                        Ability2::find()->orderBy(['key' => SORT_ASC])->all(),
                        'key',
                    ),
                ],
                'description' => implode("\n\n", [
                    Html::encode(Yii::t('app-apidoc2', 'Filter by primary ability')),
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc2', 'Ability'),
                        'app-ability2',
                        Ability2::find()->orderBy(['key' => SORT_ASC])->all(),
                    ),
                ]),
            ],
        ];
    }
}
