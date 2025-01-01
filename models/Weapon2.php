<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
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

use function array_filter;
use function array_map;
use function array_merge;
use function array_shift;
use function version_compare;

use const SORT_ASC;

/**
 * This is the model class for table "weapon2".
 *
 * @property integer $id
 * @property string $key
 * @property integer $type_id
 * @property integer $subweapon_id
 * @property integer $special_id
 * @property string $name
 * @property integer $canonical_id
 * @property integer $main_group_id
 * @property integer $splatnet
 *
 * @property Special2 $special
 * @property Subweapon2 $subweapon
 * @property Weapon2 $canonical
 * @property Weapon2 $mainReference
 * @property WeaponType2 $type
 * @property WeaponAttack2[] $weaponAttacks
 */
class Weapon2 extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'weapon2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'type_id', 'subweapon_id', 'special_id', 'name', 'canonical_id'], 'required'],
            [['main_group_id', 'main_power_up_id'], 'required'],
            [['type_id', 'subweapon_id', 'special_id', 'canonical_id', 'main_group_id'], 'default',
                'value' => null,
            ],
            [['splatnet', 'main_power_up_id'], 'default',
                'value' => null,
            ],
            [['type_id', 'subweapon_id', 'special_id', 'canonical_id', 'main_group_id'], 'integer'],
            [['splatnet', 'main_power_up_id'], 'integer'],
            [['key', 'name'], 'string', 'max' => 32],
            [['splatnet'], 'unique'],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['main_power_up_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => MainPowerUp2::class,
                'targetAttribute' => ['main_power_up_id' => 'id'],
            ],
            [['special_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Special2::class,
                'targetAttribute' => ['special_id' => 'id'],
            ],
            [['subweapon_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Subweapon2::class,
                'targetAttribute' => ['subweapon_id' => 'id'],
            ],
            [['canonical_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => self::class,
                'targetAttribute' => ['canonical_id' => 'id'],
            ],
            [['main_group_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => self::class,
                'targetAttribute' => ['main_group_id' => 'id'],
            ],
            [['type_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => WeaponType2::class,
                'targetAttribute' => ['type_id' => 'id'],
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
            'type_id' => 'Type ID',
            'subweapon_id' => 'Subweapon ID',
            'special_id' => 'Special ID',
            'name' => 'Name',
            'canonical_id' => 'Canonical ID',
            'main_group_id' => 'Main Group ID',
            'splatnet' => 'Splatnet',
            'main_power_up_id' => 'Main Power Up ID',
        ];
    }

    public function getSpecial(): ActiveQuery
    {
        return $this->hasOne(Special2::class, ['id' => 'special_id']);
    }

    public function getSubweapon(): ActiveQuery
    {
        return $this->hasOne(Subweapon2::class, ['id' => 'subweapon_id']);
    }

    public function getCanonical(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'canonical_id']);
    }

    public function getMainReference(): ActiveQuery
    {
        return $this->hasOne(self::class, ['id' => 'main_group_id']);
    }

    public function getType(): ActiveQuery
    {
        return $this->hasOne(WeaponType2::class, ['id' => 'type_id']);
    }

    public function getMainPowerUp(): ActiveQuery
    {
        return $this->hasOne(MainPowerUp2::class, ['id' => 'main_power_up_id']);
    }

    public function getWeaponAttacks(): ActiveQuery
    {
        return $this->hasMany(WeaponAttack2::class, ['weapon_id' => 'id']);
    }

    public function getWeaponAttack(SplatoonVersion2 $version): ?WeaponAttack2
    {
        $attacks = ArrayHelper::sort(
            array_filter(
                WeaponAttack2::find()
                    ->with(['version'])
                    ->andWhere(['weapon_id' => $this->id])
                    ->all(),
                fn (WeaponAttack2 $model): bool => version_compare(
                    $model->version->tag,
                    $version->tag,
                    '<=',
                ),
            ),
            fn (WeaponAttack2 $a, WeaponAttack2 $b): int => version_compare(
                $b->version->tag,
                $a->version->tag,
            ),
        );

        return $attacks ? array_shift($attacks) : null;
    }

    public function toJsonArray(): array
    {
        return [
            'key' => $this->key,
            'splatnet' => $this->splatnet,
            'type' => $this->type->toJsonArray(),
            'name' => Translator::translateToAll('app-weapon2', $this->name),
            'sub' => $this->subweapon->toJsonArray(),
            'special' => $this->special->toJsonArray(),
            'reskin_of' => $this->id === $this->canonical_id
                ? null
                : $this->canonical->key,
            'main_ref' => $this->id === $this->main_group_id
                ? $this->key
                : $this->mainReference->key,
            'main_power_up' => $this->mainPowerUp->toJsonArray(),
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
                'type' => static::oapiRef(WeaponType2::class),
                'name' => static::oapiRef(openapi\Name::class),
                'sub' => static::oapiRef(Subweapon2::class),
                'special' => static::oapiRef(Special2::class),
                'reskin_of' => array_merge(static::oapiKey(), [
                    'description' => Yii::t(
                        'app-apidoc2',
                        'If it is a weapon that only looks different, like the Hero series, ' .
                        'this points to the original weapon.',
                    ),
                    'nullable' => true,
                ]),
                'main_ref' => array_merge(static::oapiKey(), [
                    'description' => Yii::t('app-apidoc2', 'This points to the main weapon.'),
                ]),
                'main_power_up' => static::oapiRef(MainPowerUp2::class),
            ],
            'example' => $values[0]->toJsonArray(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            MainPowerUp2::class,
            Special2::class,
            Subweapon2::class,
            WeaponType2::class,
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
                    'heroshooter_replica',
                    'octoshooter_replica',
                    'sshooter',
                    'sshooter_becchu',
                    'sshooter_collabo',
                ],
                ])
                ->orderBy(['splatnet' => SORT_ASC])
                ->all(),
        );
    }
}
