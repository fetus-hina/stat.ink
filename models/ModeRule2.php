<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "mode_rule2".
 *
 * @property integer $mode_id
 * @property integer $rule_id
 *
 * @property Mode2 $mode
 * @property Rule2 $rule
 */
class ModeRule2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mode_rule2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mode_id', 'rule_id'], 'required'],
            [['mode_id', 'rule_id'], 'integer'],
            [['mode_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Mode2::class, 'targetAttribute' => ['mode_id' => 'id'],
            ],
            [['rule_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Rule2::class, 'targetAttribute' => ['rule_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mode_id' => 'Mode ID',
            'rule_id' => 'Rule ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMode()
    {
        return $this->hasOne(Mode2::class, ['id' => 'mode_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRule()
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }
}
