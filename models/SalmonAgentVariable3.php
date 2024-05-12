<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_agent_variable3".
 *
 * @property integer $salmon_id
 * @property integer $variable_id
 *
 * @property Salmon3 $salmon
 * @property AgentVariable3 $variable
 */
class SalmonAgentVariable3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_agent_variable3';
    }

    public function rules()
    {
        return [
            [['salmon_id', 'variable_id'], 'required'],
            [['salmon_id', 'variable_id'], 'default', 'value' => null],
            [['salmon_id', 'variable_id'], 'integer'],
            [['salmon_id', 'variable_id'], 'unique', 'targetAttribute' => ['salmon_id', 'variable_id']],
            [['variable_id'], 'exist', 'skipOnError' => true, 'targetClass' => AgentVariable3::class, 'targetAttribute' => ['variable_id' => 'id']],
            [['salmon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Salmon3::class, 'targetAttribute' => ['salmon_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'salmon_id' => 'Salmon ID',
            'variable_id' => 'Variable ID',
        ];
    }

    public function getSalmon(): ActiveQuery
    {
        return $this->hasOne(Salmon3::class, ['id' => 'salmon_id']);
    }

    public function getVariable(): ActiveQuery
    {
        return $this->hasOne(AgentVariable3::class, ['id' => 'variable_id']);
    }
}
