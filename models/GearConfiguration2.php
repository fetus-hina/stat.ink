<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

use function array_map;
use function base64_encode;
use function hash;
use function rtrim;

/**
 * This is the model class for table "gear_configuration2".
 *
 * @property integer $id
 * @property string $finger_print
 * @property integer $gear_id
 * @property integer $primary_ability_id
 *
 * @property Ability2 $primaryAbility
 * @property Gear2 $gear
 * @property GearConfigurationSecondary2[] $secondaries
 */
class GearConfiguration2 extends ActiveRecord
{
    public static function generateFingerPrint(
        ?int $gearId,
        ?int $primaryAbilityId,
        array $secondaryAbitilyIdList,
    ): string {
        $data = [
            'gear' => $gearId > 0 ? (int)$gearId : null,
            'primary' => $primaryAbilityId > 0 ? (int)$primaryAbilityId : null,
            'secondary' => array_map(
                fn ($id): ?int => $id > 0 ? (int)$id : null,
                $secondaryAbitilyIdList,
            ),
        ];
        return rtrim(
            base64_encode(
                hash('sha256', Json::encode($data), true),
            ),
            '=',
        );
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_configuration2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['finger_print'], 'required'],
            [['gear_id', 'primary_ability_id'], 'default', 'value' => null],
            [['gear_id', 'primary_ability_id'], 'integer'],
            [['finger_print'], 'string', 'max' => 43],
            [['finger_print'], 'unique'],
            [['primary_ability_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Ability2::class,
                'targetAttribute' => ['primary_ability_id' => 'id'],
            ],
            [['gear_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Gear2::class,
                'targetAttribute' => ['gear_id' => 'id'],
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
            'finger_print' => 'Finger Print',
            'gear_id' => 'Gear ID',
            'primary_ability_id' => 'Primary Ability ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPrimaryAbility()
    {
        return $this->hasOne(Ability2::class, ['id' => 'primary_ability_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGear()
    {
        return $this->hasOne(Gear2::class, ['id' => 'gear_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSecondaries()
    {
        return $this->hasMany(GearConfigurationSecondary2::class, ['config_id' => 'id']);
    }

    public function toJsonArray()
    {
        return [
            'gear' => $this->gear
                ? $this->gear->toJsonArray()
                : null,
            'primary_ability' => $this->primaryAbility
                ? $this->primaryAbility->toJsonArray()
                : null,
            'secondary_abilities' => $this->secondaries
                ? array_map(
                    fn (?GearConfigurationSecondary2 $o) => $o ? $o->toJsonArray() : null,
                    $this->secondaries,
                )
                : null,
        ];
    }
}
