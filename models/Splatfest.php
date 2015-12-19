<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "splatfest".
 *
 * @property integer $id
 * @property integer $region_id
 * @property string $name
 * @property string $start_at
 * @property string $end_at
 *
 * @property Region $region
 * @property SplatfestMap[] $splatfestMaps
 */
class Splatfest extends \yii\db\ActiveRecord
{
    public static function findCurrentFest()
    {
        $t = gmdate('Y-m-d\TH:i:sP', (int)(@$_SERVER['REQUEST_TIME'] ?: time()));
        //$t = gmdate('Y-m-d\TH:i:sP', strtotime('2015-11-22 00:00:00+09'));
        return static::find()
            ->innerJoinWith('region', false)
            ->andWhere(['and',
                ['<=', '{{splatfest}}.[[start_at]]', $t],
                ['>',  '{{splatfest}}.[[end_at]]', $t],
            ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'splatfest';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['region_id', 'name', 'start_at', 'end_at'], 'required'],
            [['region_id'], 'integer'],
            [['start_at', 'end_at'], 'safe'],
            [['name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'region_id' => 'Region ID',
            'name' => 'Name',
            'start_at' => 'Start At',
            'end_at' => 'End At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSplatfestMaps()
    {
        return $this->hasMany(SplatfestMap::className(), ['splatfest_id' => 'id']);
    }
}
