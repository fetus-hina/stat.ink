<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "knockout".
 *
 * @property integer $map_id
 * @property integer $rule_id
 * @property integer $battles
 * @property integer $knockouts
 *
 * @property Map $map
 * @property Rule $rule
 */
class Knockout extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'knockout';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['map_id', 'rule_id', 'battles', 'knockouts'], 'required'],
            [['map_id', 'rule_id', 'battles', 'knockouts'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'map_id' => 'Map ID',
            'rule_id' => 'Rule ID',
            'battles' => 'Battles',
            'knockouts' => 'Knockouts',
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
}
