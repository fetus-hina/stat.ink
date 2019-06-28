<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "agent_attribute".
 *
 * @property integer $id
 * @property string $name
 * @property boolean $is_automated
 */
class AgentAttribute extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agent_attribute';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'is_automated'], 'required'],
            [['is_automated'], 'boolean'],
            [['name'], 'string', 'max' => 64],
            [['name'], 'unique'],
            [['link_url'], 'string', 'max' => 256],
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
            'is_automated' => 'Is Automated',
            'link_url' => 'Link URL',
        ];
    }
}
