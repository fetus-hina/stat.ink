<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use const SORT_ASC;

/**
 * This is the model class for table "salmon_event2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $splatnet
 */
class SalmonEvent2 extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salmon_event2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 64],
            [['splatnet'], 'string', 'max' => 32],
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

    public function toJsonArray(): array
    {
        return [
            'key' => $this->key,
            'splatnet' => $this->splatnet,
            'name' => Translator::translateToAll('app-salmon-event2', $this->name),
        ];
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->orderBy(['key' => SORT_ASC])
            ->all();

        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Event information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc2', 'Event'),
                        'app-salmon-event2',
                        $values,
                    ),
                    ArrayHelper::getColumn($values, 'key', false),
                ),
                'splatnet' => static::oapiRef(openapi\SplatNet2ID::class),
                'name' => static::oapiRef(openapi\Name::class),
            ],
            'example' => static::openapiExample(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            openapi\Name::class,
            openapi\SplatNet2ID::class,
        ];
    }

    public static function openapiExample(): array
    {
        return static::findOne(['key' => 'cohock_charge'])->toJsonArray();
    }
}
