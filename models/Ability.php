<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use function array_map;

use const SORT_ASC;

/**
 * This is the model class for table "ability".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Brand[] $strengthBrands
 * @property Brand[] $weaknessBrands
 */
class Ability extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ability';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique'],
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
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getStrengthBrands()
    {
        return $this->hasMany(Brand::class, ['strength_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWeaknessBrands()
    {
        return $this->hasMany(Brand::class, ['weakness_id' => 'id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-ability', $this->name),
        ];
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->orderBy(['key' => SORT_ASC])
            ->all();
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc1', 'Ability information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc1', 'Ability'),
                        'app-ability',
                        $values,
                    ),
                    ArrayHelper::getColumn($values, 'key', false),
                ),
                'name' => static::oapiRef(openapi\Name::class),
            ],
            'example' => $values[0]->toJsonArray(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            openapi\Name::class,
        ];
    }

    public static function openapiExample(): array
    {
        $models = static::find()
            ->orderBy(['key' => SORT_ASC])
            ->all();
        return array_map(
            fn ($model) => $model->toJsonArray(),
            $models,
        );
    }
}
