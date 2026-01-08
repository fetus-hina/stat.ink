<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatfest_map".
 *
 * @property integer $id
 * @property integer $splatfest_id
 * @property integer $map_id
 *
 * @property Map $map
 * @property Splatfest $splatfest
 */
class SplatfestMap extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'splatfest_map';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['splatfest_id', 'map_id'], 'required'],
            [['splatfest_id', 'map_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'splatfest_id' => 'Splatfest ID',
            'map_id' => 'Map ID',
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
    public function getSplatfest()
    {
        return $this->hasOne(Splatfest::class, ['id' => 'splatfest_id']);
    }
}
