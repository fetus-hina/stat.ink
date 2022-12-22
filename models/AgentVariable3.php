<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "agent_variable3".
 *
 * @property integer $id
 * @property string $key
 * @property string $value
 *
 * @property BattleAgentVariable3[] $battleAgentVariable3s
 * @property Battle3[] $battles
 * @property Salmon3[] $salmon
 * @property SalmonAgentVariable3[] $salmonAgentVariable3s
 */
class AgentVariable3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'agent_variable3';
    }

    public function rules()
    {
        return [
            [['key', 'value'], 'required'],
            [['key'], 'string', 'max' => 63],
            [['value'], 'string', 'max' => 255],
            [['key', 'value'], 'unique', 'targetAttribute' => ['key', 'value']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'value' => 'Value',
        ];
    }

    public function getBattleAgentVariable3s(): ActiveQuery
    {
        return $this->hasMany(BattleAgentVariable3::class, ['variable_id' => 'id']);
    }

    public function getBattles(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['id' => 'battle_id'])->viaTable('battle_agent_variable3', ['variable_id' => 'id']);
    }

    public function getSalmon(): ActiveQuery
    {
        return $this->hasMany(Salmon3::class, ['id' => 'salmon_id'])->viaTable('salmon_agent_variable3', ['variable_id' => 'id']);
    }

    public function getSalmonAgentVariable3s(): ActiveQuery
    {
        return $this->hasMany(SalmonAgentVariable3::class, ['variable_id' => 'id']);
    }
}
