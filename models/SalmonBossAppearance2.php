<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_boss_appearance2".
 *
 * @property integer $salmon_id
 * @property integer $boss_id
 * @property integer $count
 *
 * @property Salmon2 $salmon
 * @property SalmonBoss2 $boss
 */
class SalmonBossAppearance2 extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salmon_boss_appearance2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['salmon_id', 'boss_id', 'count'], 'required'],
            [['salmon_id', 'boss_id', 'count'], 'default', 'value' => null],
            [['salmon_id', 'boss_id', 'count'], 'integer'],
            [['salmon_id', 'boss_id'], 'unique', 'targetAttribute' => ['salmon_id', 'boss_id']],
            [['salmon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Salmon2::class,
                'targetAttribute' => ['salmon_id' => 'id'],
            ],
            [['boss_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonBoss2::class,
                'targetAttribute' => ['boss_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'salmon_id' => 'Salmon ID',
            'boss_id' => 'Boss ID',
            'count' => 'Count',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getSalmon()
    {
        return $this->hasOne(Salmon2::class, ['id' => 'salmon_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBoss()
    {
        return $this->hasOne(SalmonBoss2::class, ['id' => 'boss_id']);
    }

    public function toJsonArray(): array
    {
        return [
            'boss' => $this->boss->toJsonArray(),
            'count' => (int)$this->count,
        ];
    }

    public static function openApiSchema(): array
    {
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Boss information'),
            'properties' => [
                'boss' => static::oapiRef(SalmonBoss2::class),
                'count' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'minimum' => 0,
                    'description' => Yii::t('app-apidoc2', 'How many appearance'),
                ],
            ],
            'example' => static::openapiExample(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            SalmonBoss2::class,
        ];
    }

    public static function openapiExample(): array
    {
        $boss = SalmonBoss2::findOne(['key' => 'steelhead']);
        return [
            'boss' => $boss->toJsonArray(),
            'count' => 42,
        ];
    }
}
