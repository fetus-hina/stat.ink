<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function implode;

/**
 * This is the model class for table "stat_weapon_map_trend".
 *
 * @property integer $rule_id
 * @property integer $map_id
 * @property integer $weapon_id
 * @property integer $battles
 *
 * @property Map $map
 * @property Rule $rule
 * @property Weapon $weapon
 */
class StatWeaponMapTrend extends ActiveRecord
{
    use openapi\Util;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_weapon_map_trend';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rule_id', 'map_id', 'weapon_id', 'battles'], 'required'],
            [['rule_id', 'map_id', 'weapon_id', 'battles'], 'integer'],
            [['map_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Map::class,
                'targetAttribute' => ['map_id' => 'id'],
            ],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon::class,
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
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
            'weapon_id' => 'Weapon ID',
            'battles' => 'Battles',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map::class, ['id' => 'map_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::class, ['id' => 'rule_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getWeapon()
    {
        return $this->hasOne(Weapon::class, ['id' => 'weapon_id']);
    }

    public static function openApiSchema(): array
    {
        return [
            'type' => 'object',
            'description' => implode("\n", [
                Yii::t('app-apidoc1', 'Trend information'),
                '',
                Yii::t('app-apidoc1', 'Weapons that were not used will not be output.'),
            ]),
            'properties' => [
                'rank' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'description' => Yii::t(
                        'app-apidoc1',
                        'Set in order from 1 in descending order of usage',
                    ),
                ],
                'use_pct' => [
                    'type' => 'number',
                    'description' => Yii::t(
                        'app-apidoc1',
                        'Use rate (%)',
                    ),
                ],
                'weapon' => static::oapiRef(Weapon::class),
            ],
            'example' => [
                static::openapiExample(),
            ],
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            Weapon::class,
            openapi\Name::class,
        ];
    }

    public static function openapiExample(): array
    {
        $weapon = Weapon::find()
            ->where(['key' => 'wakaba'])
            ->limit(1)
            ->one();
        return [
            'rank' => 1,
            'use_pct' => 1.23,
            'weapon' => $weapon->toJsonArray(),
        ];
    }
}
