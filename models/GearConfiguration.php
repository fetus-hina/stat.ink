<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "gear_configuration".
 *
 * @property integer $id
 * @property string $finger_print
 * @property integer $gear_id
 * @property integer $primary_ability_id
 *
 * @property Ability $primaryAbility
 * @property Gear $gear
 * @property GearConfigurationSecondary[] $secondaries
 */
class GearConfiguration extends \yii\db\ActiveRecord
{
    public static function generateFingerPrint($gearId, $primaryAbilityId, array $secondaryAbitilyIdList)
    {
        $data = [];
        if ($gearId > 0) {
            $data['gear'] = (int)$gearId;
        }
        if ($primaryAbilityId > 0) {
            $data['primary'] = (int)$primaryAbilityId;
        }
        if (count($secondaryAbitilyIdList)) {
            $data['secondary'] = [];
            foreach ($secondaryAbitilyIdList as $id) {
                if ($id > 0) {
                    $data['secondary'][] = (int)$id;
                } else {
                    $data['secondary'][] = 'none';
                }
            }
        }
        return rtrim(
            base64_encode(
                hash(
                    'sha256',
                    http_build_query($data, '', '&'),
                    true
                )
            ),
            '='
        );
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gear_configuration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['finger_print'], 'required'],
            [['gear_id', 'primary_ability_id'], 'integer'],
            [['finger_print'], 'string', 'max' => 43],
            [['finger_print'], 'unique'],
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
     * @return \yii\db\ActiveQuery
     */
    public function getPrimaryAbility()
    {
        return $this->hasOne(Ability::class, ['id' => 'primary_ability_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGear()
    {
        return $this->hasOne(Gear::class, ['id' => 'gear_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecondaries()
    {
        return $this->hasMany(GearConfigurationSecondary::class, ['config_id' => 'id']);
    }

    public function toJsonArray()
    {
        return [
            'gear' => $this->gear ? $this->gear->toJsonArray() : null,
            'primary_ability' => $this->primaryAbility ? $this->primaryAbility->toJsonArray() : null,
            'secondary_abilities' => $this->secondaries
                ? array_map(
                    function ($o) {
                        return $o->toJsonArray();
                    },
                    $this->secondaries
                )
                : null,
        ];
        return null;
    }
}
