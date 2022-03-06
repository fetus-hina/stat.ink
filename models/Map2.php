<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use DateTimeZone;
use Yii;
use app\components\helpers\DateTimeFormatter;
use app\components\helpers\Translator;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "map2".
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string $short_name
 * @property int $area
 * @property string $release_at
 * @property int $splatnet
 */
class Map2 extends ActiveRecord
{
    use openapi\Util;

    public static function find(): ActiveQuery
    {
        return new class (static::class) extends ActiveQuery {
            public function excludeMystery(): self
            {
                return $this->andWhere(['and',
                    ['not', ['like', 'key', 'mystery%', false]],
                ]);
            }
        };
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'map2';
    }

    public static function getSortedMap($callback = null): array
    {
        $query = static::find();
        if ($callback && is_callable($callback)) {
            call_user_func($callback, $query);
        }
        return ArrayHelper::map(
            static::sort($query->all()),
            'key',
            fn (self $row): string => Yii::t('app-map2', $row->name)
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name', 'short_name'], 'required'],
            [['area', 'splatnet'], 'integer'],
            [['release_at'], 'safe'],
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
            'area' => 'Area',
            'release_at' => 'Release At',
            'splatnet' => 'SplatNet ID',
        ];
    }

    public function toJsonArray(): array
    {
        $t = $this->release_at ? strtotime($this->release_at) : null;
        return [
            'key' => $this->key,
            'splatnet' => $this->splatnet,
            'name' => Translator::translateToAll('app-map2', $this->name),
            'short_name' => Translator::translateToAll('app-map2', $this->short_name),
            'area' => $this->area,
            'release_at' => $t
                ? DateTimeFormatter::unixTimeToJsonArray(
                    $t,
                    new DateTimeZone('Etc/UTC')
                )
                : null,
        ];
    }

    public static function sort(array $list): array
    {
        usort($list, [static::class, 'compare']);
        return $list;
    }

    public static function compare(self $a, self $b): int
    {
        return static::getCompareClass($a) <=> static::getCompareClass($b)
            ?: strnatcasecmp(Yii::t('app-map2', $a->name), Yii::t('app-map2', $b->name))
            ?: strnatcasecmp($a->name, $b->name)
            ?: strcmp($a->key, $b->key);
    }

    private static function getCompareClass(self $self): int
    {
        if (substr($self->key, 0, 7) === 'mystery') {
            if ($self->key === 'mystery') {
                return 1;
            }

            return 1 + (int)substr($self->key, 8);
        }

        return 0;
    }

    public static function openApiSchema(): array
    {
        $values = static::sort(static::find()->all());
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Stage information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc2', 'Stage'),
                        'app-map2',
                        $values
                    ),
                    ArrayHelper::getColumn($values, 'key', false)
                ),
                'splatnet' => static::oapiRef(openapi\SplatNet2ID::class),
                'name' => static::oapiRef(openapi\Name::class),
                'short_name' => static::oapiRef(openapi\ShortName::class),
                'area' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'nullable' => true,
                    'description' => Yii::t('app-apidoc2', 'Total area'),
                ],
                'release_at' => array_merge(openapi\DateTime::openApiSchema(), [
                    'description' => Yii::t('app-apidoc2', 'Date and time when ready to play'),
                    'nullable' => true,
                ]),
            ],
            'example' => $values[0]->toJsonArray(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            openapi\Name::class,
            openapi\ShortName::class,
        ];
    }

    public static function openapiExample(): array
    {
        return array_map(
            fn (self $model): array => $model->toJsonArray(),
            static::sort(
                static::find()
                    ->andWhere([
                        'key' => [
                            'battera',
                            'kombu',
                        ],
                    ])
                    ->all()
            )
        );
    }
}
