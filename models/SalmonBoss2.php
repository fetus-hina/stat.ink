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

use function array_map;

use const SORT_ASC;

/**
 * This is the model class for table "salmon_boss2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $splatnet
 * @property string $splatnet_str
 */
class SalmonBoss2 extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salmon_boss2';
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
            [['name'], 'string', 'max' => 32],
            [['splatnet_str'], 'string', 'max' => 64],
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
            'splatnet_str' => 'Splatnet Str',
        ];
    }

    public function toJsonArray(): array
    {
        return [
            'key' => $this->key,
            'splatnet' => $this->splatnet,
            'splatnet_str' => $this->splatnet_str,
            'name' => Translator::translateToAll('app-salmon-boss2', $this->name),
        ];
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->orderBy(['key' => SORT_ASC])
            ->all();

        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Boss information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc2', 'Boss'),
                        'app-salmon-boss2',
                        $values,
                    ),
                    ArrayHelper::getColumn($values, 'key', false),
                ),
                'splatnet' => static::oapiRef(openapi\SplatNet2ID::class),
                'splatnet_str' => [
                    'type' => 'string',
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'SplatNet specified ID'),
                ],
                'name' => static::oapiRef(openapi\Name::class),
            ],
            'example' => $values[0]->toJsonArray(),
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
        return array_map(
            fn (self $model): array => $model->toJsonArray(),
            static::find()
                ->orderBy(['key' => SORT_ASC])
                ->all(),
        );
    }
}
