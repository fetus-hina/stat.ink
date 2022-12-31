<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "agent_attribute".
 *
 * @property integer $id
 * @property string $name
 * @property boolean $is_automated
 * @property string $link_url
 */
class AgentAttribute extends ActiveRecord
{
    public static function tableName()
    {
        return 'agent_attribute';
    }

    public function rules()
    {
        return [
            [['name', 'is_automated'], 'required'],
            [['is_automated'], 'boolean'],
            [['name'], 'string', 'max' => 64],
            [['link_url'], 'string', 'max' => 256],
            [['name'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'is_automated' => 'Is Automated',
            'link_url' => 'Link Url',
        ];
    }
}
