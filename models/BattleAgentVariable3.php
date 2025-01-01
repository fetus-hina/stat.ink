<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "battle_agent_variable3".
 *
 * @property integer $battle_id
 * @property integer $variable_id
 *
 * @property Battle3 $battle
 * @property AgentVariable3 $variable
 */
class BattleAgentVariable3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'battle_agent_variable3';
    }

    public function rules()
    {
        return [
            [['battle_id', 'variable_id'], 'required'],
            [['battle_id', 'variable_id'], 'default', 'value' => null],
            [['battle_id', 'variable_id'], 'integer'],
            [['battle_id', 'variable_id'], 'unique', 'targetAttribute' => ['battle_id', 'variable_id']],
            [['variable_id'], 'exist', 'skipOnError' => true, 'targetClass' => AgentVariable3::class, 'targetAttribute' => ['variable_id' => 'id']],
            [['battle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Battle3::class, 'targetAttribute' => ['battle_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'battle_id' => 'Battle ID',
            'variable_id' => 'Variable ID',
        ];
    }

    public function getBattle(): ActiveQuery
    {
        return $this->hasOne(Battle3::class, ['id' => 'battle_id']);
    }

    public function getVariable(): ActiveQuery
    {
        return $this->hasOne(AgentVariable3::class, ['id' => 'variable_id']);
    }
}
