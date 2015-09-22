<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "color".
 *
 * @property integer $id
 * @property string $name
 * @property string $leader
 *
 * @property OfficialWinData[] $officialWinDatas
 * @property OfficialData[] $datas
 * @property Team[] $teams
 * @property Fest[] $fests
 * @property Mvp[] $mvps
 */
class Color extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'color';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'leader'], 'required'],
            [['name', 'leader'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'leader' => 'Leader',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfficialWinDatas()
    {
        return $this->hasMany(OfficialWinData::className(), ['color_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDatas()
    {
        return $this->hasMany(OfficialData::className(), ['id' => 'data_id'])
            ->viaTable('official_win_data', ['color_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeams()
    {
        return $this->hasMany(Team::className(), ['color_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFests()
    {
        return $this->hasMany(Fest::className(), ['id' => 'fest_id'])->viaTable('team', ['color_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMvps()
    {
        return $this->hasMany(Mvp::className(), ['color_id' => 'id']);
    }
}
