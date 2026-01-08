<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
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
use function call_user_func;
use function is_callable;

use const SORT_ASC;

/**
 * This is the model class for table "rule2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $short_name
 *
 * @property ModeRule2[] $modeRules
 * @property Mode2[] $modes
 */
class Rule2 extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rule2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name', 'short_name'], 'required'],
            [['key', 'short_name'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['short_name'], 'unique'],
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
            'short_name' => 'Short Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getModeRules()
    {
        return $this->hasMany(ModeRule2::class, ['rule_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getModes()
    {
        return $this->hasMany(Mode2::class, ['id' => 'mode_id'])->viaTable('mode_rule2', ['rule_id' => 'id']);
    }

    public function toJsonArray(): array
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-rule2', $this->name),
        ];
    }

    public static function getSortedAll(
        ?string $mode,
        $queryCallback = null,
        $valueCallback = null,
    ): array {
        $query = static::find()->orderBy(['rule2.id' => SORT_ASC]);
        if ($mode) {
            $query->innerJoinWith('modes')
                ->andWhere(['mode2.key' => $mode]);
        }

        if ($queryCallback && is_callable($queryCallback)) {
            call_user_func($queryCallback, $query);
        }

        if ($valueCallback === null) {
            $valueCallback = fn (self $row): string => Yii::t('app-rule2', ArrayHelper::getValue($row, 'name'));
        }

        return ArrayHelper::map($query->all(), 'key', $valueCallback);
    }

    public static function openApiSchema(): array
    {
        $values = static::find()->orderBy(['id' => SORT_ASC])->all();
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Mode information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc2', 'Mode'),
                        'app-rule2',
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
        return array_map(
            fn (self $model): array => $model->toJsonArray(),
            static::find()
                ->orderBy(['id' => SORT_ASC])
                ->all(),
        );
    }
}
