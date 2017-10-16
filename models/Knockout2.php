<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "knockout2".
 *
 * @property integer $rule_id
 * @property integer $map_id
 * @property integer $rank_id
 * @property integer $battles
 * @property integer $knockouts
 * @property double $avg_game_time
 * @property double $avg_knockout_time
 *
 * @property Map2 $map
 * @property Rank2 $rank
 * @property Rule2 $rule
 */
class Knockout2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'knockout2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rule_id', 'map_id', 'rank_id', 'battles', 'knockouts'], 'required'],
            [['avg_game_time', 'avg_knockout_time'], 'required'],
            [['rule_id', 'map_id', 'rank_id', 'battles', 'knockouts'], 'default', 'value' => null],
            [['rule_id', 'map_id', 'rank_id', 'battles', 'knockouts'], 'integer'],
            [['avg_game_time', 'avg_knockout_time'], 'number'],
            [['rule_id', 'map_id', 'rank_id'], 'unique',
                'targetAttribute' => ['rule_id', 'map_id', 'rank_id'],
            ],
            [['map_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Map2::class,
                'targetAttribute' => ['map_id' => 'id'],
            ],
            [['rank_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rank2::class,
                'targetAttribute' => ['rank_id' => 'id'],
            ],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rule_id' => 'Rule ID',
            'map_id' => 'Map ID',
            'rank_id' => 'Rank ID',
            'battles' => 'Battles',
            'knockouts' => 'Knockouts',
            'avg_game_time' => 'Avg Game Time',
            'avg_knockout_time' => 'Avg Knockout Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(Map2::class, ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRank()
    {
        return $this->hasOne(Rank2::class, ['id' => 'rank_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }
}
