<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use function array_merge;

use const SORT_ASC;

/**
 * This is the model class for table "death_reason".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $key
 * @property string $name
 * @property string $weapon_id
 *
 * @property BattleDeathReason[] $battleDeathReasons
 * @property Battle[] $battles
 * @property DeathReasonType $type
 * @property Weapon $weapon
 */
class DeathReason extends ActiveRecord
{
    use openapi\Util;

    public static function find()
    {
        return parent::find()->with('type');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'death_reason';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_id', 'weapon_id'], 'integer'],
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
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
            'type_id' => 'Type ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBattleDeathReasons()
    {
        return $this->hasMany(BattleDeathReason::class, ['reason_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBattles()
    {
        return $this
            ->hasMany(Battle::class, ['id' => 'battle_id'])
            ->viaTable('battle_death_reason', ['reason_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(DeathReasonType::class, ['id' => 'type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon::class, ['id' => 'type_id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'name' => $this->getTranslatedNameList(),
            'type' => $this->type
                ? $this->type->toJsonArray()
                : [
                    'key' => null,
                    'name' => Translator::translateToAll('app-death', 'Unknown'),
                ],
        ];
    }

    public function getTranslatedNameList()
    {
        if ($this->type) {
            switch ($this->type->key) {
                case 'main':
                    return Translator::translateToAll('app-weapon', $this->name);

                case 'sub':
                    return Translator::translateToAll('app-subweapon', $this->name);

                case 'special':
                    return Translator::translateToAll('app-special', $this->name);
            }
        }
        return Translator::translateToAll('app-death', $this->name);
    }

    public function getTranslatedName()
    {
        if ($this->type) {
            switch ($this->type->key) {
                case 'main':
                    return Yii::t('app-weapon', $this->name);

                case 'sub':
                    return Yii::t('app-subweapon', $this->name);

                case 'special':
                    return Yii::t('app-special', $this->name);
            }
        }
        return Yii::t('app-death', $this->name);
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->orderBy([
                'type_id' => SORT_ASC,
                'key' => SORT_ASC,
            ])
            ->all();
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc1', 'Death reason information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc1', 'Death Reason'),
                        function (self $model): string {
                            $map = [
                                'main' => 'app-weapon',
                                'sub' => 'app-subweapon',
                                'special' => 'app-special',
                            ];
                            $type = ArrayHelper::getValue($model, 'type.key');
                            return $map[$type] ?? 'app-death';
                        },
                        $values,
                    ),
                    ArrayHelper::getColumn($values, 'key', false),
                ),
                'name' => static::oapiRef(openapi\Name::class),
                'type' => array_merge(DeathReasonType::openApiSchema(), [
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
        ];
    }

    public static function openapiExample(): array
    {
        $model = static::find()
            ->where(['key' => 'hoko_shot'])
            ->limit(1)
            ->one();
        return [
            $model->toJsonArray(),
        ];
    }
}
