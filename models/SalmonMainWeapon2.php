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
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use function array_map;
use function implode;
use function sprintf;

use const SORT_ASC;

/**
 * This is the model class for table "salmon_main_weapon2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $splatnet
 * @property integer $weapon_id
 *
 * @property Weapon2 $weapon
 */
class SalmonMainWeapon2 extends ActiveRecord
{
    use openapi\Util;

    public static function find()
    {
        return new class (static::class) extends ActiveQuery {
            public function sorted(): self
            {
                $kumaFirst = sprintf('(CASE %s END)', implode(' ', [
                    'WHEN {{salmon_main_weapon2}}.[[weapon_id]] IS NULL THEN 0',
                    'ELSE 1',
                ]));

                $this
                    ->joinWith([
                        'weapon',
                        'weapon.type',
                    ])
                    ->orderBy([
                        $kumaFirst => SORT_ASC,
                        '{{weapon_type2}}.[[category_id]]' => SORT_ASC,
                        '{{weapon_type2}}.[[rank]]' => SORT_ASC,
                        '{{salmon_main_weapon2}}.[[key]]' => SORT_ASC,
                    ]);

                return $this;
            }
        };
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salmon_main_weapon2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['splatnet', 'weapon_id'], 'default', 'value' => null],
            [['splatnet', 'weapon_id'], 'integer'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
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
            'weapon_id' => 'Weapon ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }

    public function toJsonArray(): array
    {
        return [
            'key' => $this->key,
            'splatnet' => $this->splatnet,
            'name' => Translator::translateToAll('app-weapon2', $this->name),
        ];
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->orderBy(['key' => SORT_ASC])
            ->all();

        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Weapon information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc2', 'Weapon'),
                        'app-weapon2',
                        $values,
                    ),
                    ArrayHelper::getColumn($values, 'key', false),
                ),
                'splatnet' => static::oapiRef(openapi\SplatNet2ID::class),
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
                ->andWhere(['key' => [
                    'sshooter',
                    'splatroller',
                ]])
                ->sorted()
                ->orderBy(['splatnet' => SORT_ASC])
                ->all(),
        );
    }
}
