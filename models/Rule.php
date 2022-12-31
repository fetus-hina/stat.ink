<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "rule".
 *
 * @property integer $id
 * @property integer $mode_id
 * @property string $key
 * @property string $name
 *
 * @property Battle[] $battles
 * @property GameMode $mode
 * @property SplapiRule[] $splapiRules
 * @property StatWeapon[] $statWeapons
 * @property Weapon[] $weapons
 * @property StatWeaponBattleCount $statWeaponBattleCount
 */
final class Rule extends \yii\db\ActiveRecord
{
    use SafeFindOneTrait;
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mode_id', 'key', 'name', 'short_name'], 'required'],
            [['mode_id'], 'integer'],
            [['key', 'short_name'], 'string', 'max' => 16],
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
            'mode_id' => 'Mode ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::class, ['rule_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMode()
    {
        return $this->hasOne(GameMode::class, ['id' => 'mode_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSplapiRules()
    {
        return $this->hasMany(SplapiRule::class, ['rule_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatWeapons()
    {
        return $this->hasMany(StatWeapon::class, ['rule_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapons()
    {
        return $this->hasMany(Weapon::class, ['id' => 'weapon_id'])->viaTable('stat_weapon', ['rule_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatWeaponBattleCount()
    {
        return $this->hasOne(StatWeaponBattleCount::class, ['rule_id' => 'id']);
    }

    public function toJsonArray(): array
    {
        return [
            'key' => $this->key,
            'mode' => $this->mode->toJsonArray(),
            'name' => Translator::translateToAll('app-rule', $this->name),
        ];
    }

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->with('mode')
            ->orderBy(['id' => SORT_ASC])
            ->all();
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc1', 'Mode information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc1', 'Mode'),
                        'app-rule',
                        $values,
                    ),
                    ArrayHelper::getColumn($values, 'key', false),
                ),
                'mode' => static::oapiRef(GameMode::class),
                'name' => static::oapiRef(openapi\Name::class),
            ],
            'example' => $values[0]->toJsonArray(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            GameMode::class,
            openapi\Name::class,
        ];
    }

    public static function openapiExample(): array
    {
        $values = static::find()
            ->with('mode')
            ->orderBy(['id' => SORT_ASC])
            ->all();
        return array_map(
            fn (self $model): array => $model->toJsonArray(),
            $values,
        );
    }
}
