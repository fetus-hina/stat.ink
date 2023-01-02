<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use function array_map;
use function array_merge;

use const SORT_ASC;

/**
 * This is the model class for table "salmon_title2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $splatnet
 */
class SalmonTitle2 extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salmon_title2';
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
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 64],
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
            'splatnet' => 'Splatnet',
        ];
    }

    public function getTranslatedName(?Gender $gender = null): string
    {
        $text = $this->name;
        if ($gender) {
            if ($gender->id == 1) {
                $text = "{boy}{$text}";
            } else {
                $text = "{girl}{$text}";
            }
        }

        return Yii::t('app-salmon-title2', $text, [
            'boy' => '',
            'girl' => '',
        ]);
    }

    public function toJsonArray(?Gender $gender = null): array
    {
        $text = $this->name;
        if ($gender) {
            if ($gender->id == 1) {
                $text = "{boy}{$text}";
            } else {
                $text = "{girl}{$text}";
            }
        }

        return [
            'key' => $this->key,
            'splatnet' => $this->splatnet,
            'name' => Translator::translateToAll('app-salmon-title2', $text, [
                'boy' => '',
                'girl' => '',
            ]),
            'generic_name' => Translator::translateToAll('app-salmon-title2', $this->name),
        ];
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Salmon Run title information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc2', 'Title'),
                        'app-salmon-title2',
                        $values,
                    ),
                    ArrayHelper::getColumn($values, 'key', false),
                ),
                'splatnet' => static::oapiRef(openapi\SplatNet2ID::class),
                'name' => array_merge(openapi\Name::openApiSchema(), [
                    'description' => Yii::t('app-apidoc2', 'Salmon Run title (consider gender)'),
                ]),
                'generic_name' => array_merge(openapi\Name::openApiSchema(), [
                    'description' => Yii::t(
                        'app-apidoc2',
                        'Salmon Run title (doesn\'t consider gender)',
                    ),
                ]),
            ],
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            openapi\SplatNet2ID::class,
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
