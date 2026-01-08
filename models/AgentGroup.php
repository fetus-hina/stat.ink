<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "agent_group".
 *
 * @property integer $id
 * @property string $name
 *
 * @property AgentGroupMap[] $agentGroupMaps
 */
class AgentGroup extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agent_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAgentGroupMaps()
    {
        return $this->hasMany(AgentGroupMap::class, ['group_id' => 'id'])
            ->orderBy('{{agent_group_map}}.[[agent_name]] ASC');
    }
}
