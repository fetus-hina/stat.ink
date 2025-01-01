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
 * This is the model class for table "gear_type".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Gear[] $gears
 * @property Gear2[] $gear2s
 */
class GearType extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
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
    public function getGears()
    {
        return $this->hasMany(Gear::class, ['type_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGear2s()
    {
        return $this->hasMany(Gear2::class, ['type_id' => 'id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-gear', $this->name),
        ];
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->orderBy(['id' => SORT_ASC])
            ->all();
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc1', 'Gear type information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc1', 'Gear Type'),
                        'app-gear',
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
            ->orderBy(['id' => SORT_ASC])
            ->all();
        return array_map(
            fn ($model) => $model->toJsonArray(),
            $models,
        );
    }
}
