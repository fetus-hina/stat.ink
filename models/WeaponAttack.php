<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "weapon_attack".
 *
 * @property integer $id
 * @property integer $main_weapon_id
 * @property integer $version_id
 * @property string $damage
 *
 * @property SplatoonVersion $version
 * @property Weapon $mainWeapon
 */
class WeaponAttack extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'weapon_attack';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['main_weapon_id', 'damage'], 'required'],
            [['main_weapon_id', 'version_id'], 'integer'],
            [['damage'], 'number'],
            [['main_weapon_id', 'version_id'], 'unique',
                'targetAttribute' => ['main_weapon_id', 'version_id'],
                'message' => 'The combination of Main Weapon ID and Version ID has already been taken.'],
            [['version_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SplatoonVersion::className(),
                'targetAttribute' => ['version_id' => 'id']],
            [['main_weapon_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Weapon::className(),
                'targetAttribute' => ['main_weapon_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'main_weapon_id' => 'Main Weapon ID',
            'version_id' => 'Version ID',
            'damage' => 'Damage',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVersion()
    {
        return $this->hasOne(SplatoonVersion::className(), ['id' => 'version_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainWeapon()
    {
        return $this->hasOne(Weapon::className(), ['id' => 'main_weapon_id']);
    }
}
