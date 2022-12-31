<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ability2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $splatnet
 * @property boolean $primary_only
 *
 * @property Brand2[] $strengthBrands
 * @property Brand2[] $weaknessBrands
 * @property Gear2[] $gears
 */
class Ability2 extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ability2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['splatnet'], 'default', 'value' => null],
            [['splatnet'], 'integer'],
            [['primary_only'], 'boolean'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['splatnet'], 'unique'],
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
            'splatnet' => 'Splatnet',
            'primary_only' => 'Primary Only',
        ];
    }

    public function getStrengthBrands(): ActiveQuery
    {
        return $this->hasMany(Brand2::class, ['strength_id' => 'id']);
    }

    public function getWeaknessBrands(): ActiveQuery
    {
        return $this->hasMany(Brand2::class, ['weakness_id' => 'id']);
    }

    public function getGears(): ActiveQuery
    {
        return $this->hasMany(Gear2::class, ['ability_id' => 'id']);
    }

    public function toJsonArray(): array
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-ability2', $this->name),
        ];
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->orderBy(['key' => SORT_ASC])
            ->all();
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Ability information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc2', 'Ability'),
                        'app-ability2',
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
