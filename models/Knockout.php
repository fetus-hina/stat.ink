<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "knockout".
 *
 * @property int $map_id
 * @property int $rule_id
 * @property int $battles
 * @property int $knockouts
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
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map::class, ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule::class, ['id' => 'rule_id']);
    }
}
