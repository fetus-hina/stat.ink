<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function array_map;
use function array_merge;

use const SORT_ASC;

/**
 * This is the model class for table "gear2".
 *
 * @property integer $id
 * @property string $key
 * @property integer $type_id
 * @property integer $brand_id
 * @property string $name
 * @property integer $ability_id
 * @property integer $splatnet
 *
 * @property Ability2 $ability
 * @property Brand2 $brand
 * @property GearType $type
 */
class Gear2 extends ActiveRecord
{
    use openapi\Util;

    private $translatedName;

    /*
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'type_id', 'brand_id', 'name'], 'required'],
            [['type_id', 'brand_id', 'ability_id', 'splatnet'], 'default', 'value' => null],
            [['type_id', 'brand_id', 'ability_id', 'splatnet'], 'integer'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['splatnet'], 'unique'],
            [['ability_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Ability2::class,
                'targetAttribute' => ['ability_id' => 'id'],
            ],
            [['brand_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Brand2::class,
                'targetAttribute' => ['brand_id' => 'id'],
            ],
            [['type_id'], 'exist', 'skipOnError' => true,
                'targetClass' => GearType::class,
                'targetAttribute' => ['type_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'type_id' => 'Type ID',
            'brand_id' => 'Brand ID',
            'name' => 'Name',
            'ability_id' => 'Ability ID',
            'splatnet' => 'Splatnet',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAbility()
    {
        return $this->hasOne(Ability2::class, ['id' => 'ability_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBrand()
    {
        return $this->hasOne(Brand2::class, ['id' => 'brand_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(GearType::class, ['id' => 'type_id']);
    }

    public function getTranslatedName(): string
    {
        if ($this->translatedName === null) {
            $this->translatedName = Yii::t('app-gear2', $this->name);
        }
        return $this->translatedName;
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'type' => $this->type->toJsonArray(),
            'brand' => $this->brand ? $this->brand->toJsonArray() : null,
            'name' => Translator::translateToAll('app-gear2', $this->name),
            'primary_ability' => $this->ability ? $this->ability->toJsonArray() : null,
            'splatnet' => $this->splatnet,
        ];
    }

    public static function openApiSchema(): array
    {
        $row = static::find()
            ->orderBy(['key' => SORT_ASC])
            ->limit(1)
            ->one();

        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Gear information'),
            'properties' => [
                'key' => static::oapiKey(),
                'type' => static::oapiRef(GearType::class),
                'brand' => static::oapiRef(Brand2::class),
                'name' => static::oapiRef(openapi\Name::class),
                'primary_ability' => array_merge(Ability2::openApiSchema(), [
                    'nullable' => true,
                ]),
                'splatnet' => static::oapiRef(openapi\SplatNet2ID::class),
            ],
            'example' => [
                $row->toJsonArray(),
            ],
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            Ability2::class,
            Brand2::class,
            GearType::class,
            openapi\Name::class,
            openapi\SplatNet2ID::class,
        ];
    }

    public static function openapiExample(): array
    {
        return array_map(
            fn (self $model): array => $model->toJsonArray(),
            static::find()
                ->orderBy(['key' => SORT_ASC])
                ->limit(5)
                ->all(),
        );
    }
}
