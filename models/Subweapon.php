<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
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
 * This is the model class for table "subweapon".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Weapon[] $weapons
 */
class Subweapon extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subweapon';
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
            [['name'], 'unique'],
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
    public function getWeapons()
    {
        return $this->hasMany(Weapon::class, ['subweapon_id' => 'id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-subweapon', $this->name),
        ];
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->orderBy(['key' => SORT_ASC])
            ->all();
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc1', 'Sub weapon information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc1', 'Sub Weapon'),
                        'app-subweapon',
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
