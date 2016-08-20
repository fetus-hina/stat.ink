<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;

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
class Rule extends \yii\db\ActiveRecord
{
    use SafeFindOneTrait;

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
            [['name'], 'unique']
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
        return $this->hasMany(Battle::className(), ['rule_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMode()
    {
        return $this->hasOne(GameMode::className(), ['id' => 'mode_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSplapiRules()
    {
        return $this->hasMany(SplapiRule::className(), ['rule_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatWeapons()
    {
        return $this->hasMany(StatWeapon::className(), ['rule_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapons()
    {
        return $this->hasMany(Weapon::className(), ['id' => 'weapon_id'])->viaTable('stat_weapon', ['rule_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatWeaponBattleCount()
    {
        return $this->hasOne(StatWeaponBattleCount::className(), ['rule_id' => 'id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'mode' => [
                'key' => $this->mode->key,
                'name' => Translator::translateToAll('app-rule', $this->mode->name),
            ],
            'name' => Translator::translateToAll('app-rule', $this->name),
        ];
    }
}
