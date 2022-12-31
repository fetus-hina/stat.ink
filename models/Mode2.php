<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "mode2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property ModeRule2[] $modeRules
 * @property Rule2[] $rules
 */
class Mode2 extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mode2';
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
     * @return \yii\db\ActiveQuery
     */
    public function getModeRules()
    {
        return $this->hasMany(ModeRule2::class, ['mode_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRules()
    {
        return $this->hasMany(Rule2::class, ['id' => 'rule_id'])
            ->viaTable('mode_rule2', ['mode_id' => 'id']);
    }

    public function toJsonArray($withRules = true): array
    {
        $ret = [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-rule2', $this->name),
        ];
        if ($withRules) {
            $ret['rules'] = array_map(
                function (Rule2 $rule): array {
                    return $rule->toJsonArray();
                },
                $this->rules,
            );
        }
        return $ret;
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->orderBy(['key' => SORT_ASC])
            ->all();
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
                'rules' => [
                    'type' => 'array',
                    'items' => static::oapiRef(Rule2::class),
                ],
            ],
            'example' => $values[0]->toJsonArray(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            openapi\Name::class,
            Rule2::class,
        ];
    }

    public static function openapiExample(): array
    {
        return array_map(
            function (self $model): array {
                return $model->toJsonArray();
            },
            static::find()
                ->with(['rules'])
                ->orderBy(['key' => SORT_ASC])
                ->all(),
        );
    }
}
