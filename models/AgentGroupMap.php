<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "agent_group_map".
 *
 * @property integer $group_id
 * @property string $agent_name
 *
 * @property AgentGroup $group
 */
class AgentGroupMap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agent_group_map';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'agent_name'], 'required'],
            [['group_id'], 'integer'],
            [['agent_name'], 'string', 'max' => 64],
            [['group_id'], 'exist', 'skipOnError' => true,
                'targetClass' => AgentGroup::class,
                'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'group_id' => 'Group ID',
            'agent_name' => 'Agent Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(AgentGroup::className(), ['id' => 'group_id']);
    }
}
