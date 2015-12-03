<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "period_map".
 *
 * @property integer $id
 * @property integer $period
 * @property integer $rule_id
 * @property integer $map_id
 *
 * @property Map $map
 * @property Rule $rule
 */
class PeriodMap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'period_map';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['period', 'rule_id', 'map_id'], 'required'],
            [['period', 'rule_id', 'map_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'period' => 'Period',
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map::className(), ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::className(), ['id' => 'rule_id']);
    }
}
