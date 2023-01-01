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

use function array_map;
use function array_merge;
use function call_user_func;
use function count;
use function is_callable;
use function sprintf;
use function str_starts_with;
use function strcmp;
use function strnatcasecmp;
use function strtotime;
use function substr;

/**
 * This is the model class for table "map2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $short_name
 * @property integer $area
 * @property string $release_at
 * @property integer $splatnet
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
        Yii::beginProfile(__METHOD__, self::class);
        try {
            $query = self::find();
            if ($callback && is_callable($callback)) {
                call_user_func($callback, $query);
            }
            return ArrayHelper::map(
                self::sort($query->all()),
                'key',
                fn (self $row): string => Yii::t('app-map2', $row->name),
            );
        } finally {
            Yii::endProfile(__METHOD__, self::class);
        }
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
                    new DateTimeZone('Etc/UTC'),
                )
                : null,
        ];
    }

    /**
     * @param self[] $list
     * @return self[]
     */
    public static function sort(array $list): array
    {
        $profile = sprintf('Sort %d elements', count($list));
        Yii::beginProfile($profile, __METHOD__);
        try {
            return ArrayHelper::sort(
                $list,
                fn (self $a, self $b): int => self::compare($a, $b),
            );
        } finally {
            Yii::endProfile($profile, __METHOD__);
        }
    }

    public static function compare(self $a, self $b): int
    {
        return self::getCompareClass($a) <=> self::getCompareClass($b)
            ?: strnatcasecmp(Yii::t('app-map2', $a->name), Yii::t('app-map2', $b->name))
            ?: strnatcasecmp($a->name, $b->name)
            ?: strcmp($a->key, $b->key);
    }

    private static function getCompareClass(self $self): int
    {
        if (str_starts_with($self->key, 'mystery')) {
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
                        $values,
                    ),
                    ArrayHelper::getColumn($values, 'key', false),
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
                    ->andWhere(['key' => [
                        'battera',
                        'kombu',
                    ]])
                    ->all(),
            ),
        );
    }
}
