<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "stat_weapon_vs_weapon".
 *
 * @property integer $version_id
 * @property integer $rule_id
 * @property integer $weapon_id_1
 * @property integer $weapon_id_2
 * @property integer $battle_count
 * @property integer $win_count
 *
 * @property SplatoonVersion $version
 * @property Rule $rule
 * @property Weapon $weaponId1
 * @property Weapon $weaponId2
 */
class StatWeaponVsWeapon extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function find()
    {
        return new class (get_called_class()) extends ActiveQuery {
            public function weapon($weapon): ActiveQuery
            {
                return $this->weaponImpl(
                    ($weapon instanceof Weapon) ? $weapon->id : (int)$weapon,
                );
            }

            private function weaponImpl(int $weaponId): ActiveQuery
            {
                return $this->andWhere(['or', [
                    '{{stat_weapon_vs_weapon}}.[[weapon_id_1]]' => $weaponId,
                    '{{stat_weapon_vs_weapon}}.[[weapon_id_2]]' => $weaponId,
                ]]);
            }
        };
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stat_weapon_vs_weapon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version_id', 'rule_id', 'weapon_id_1', 'weapon_id_2', 'battle_count', 'win_count'], 'required'],
            [['version_id', 'rule_id', 'weapon_id_1', 'weapon_id_2', 'battle_count', 'win_count'], 'integer'],
            [['version_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SplatoonVersion::class,
                'targetAttribute' => ['version_id' => 'id']],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule::class,
                'targetAttribute' => ['version_id' => 'id']],
            [['weapon_id_1'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon::class,
                'targetAttribute' => ['weapon_id_1' => 'id']],
            [['weapon_id_2'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon::class,
                'targetAttribute' => ['weapon_id_2' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'version_id' => 'Version ID',
            'rule_id' => 'Rule ID',
            'weapon_id_1' => 'Weapon Id 1',
            'weapon_id_2' => 'Weapon Id 2',
            'battle_count' => 'Battle Count',
            'win_count' => 'Win Count',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVersion()
    {
        return $this->hasOne(SplatoonVersion::class, ['id' => 'version_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::class, ['id' => 'rule_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeaponId1()
    {
        return $this->hasOne(Weapon::class, ['id' => 'weapon_id_1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeaponId2()
    {
        return $this->hasOne(Weapon::class, ['id' => 'weapon_id_2']);
    }
}
