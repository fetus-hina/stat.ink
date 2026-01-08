<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "link_mode".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property integer $rank
 *
 * @property User[] $users
 */
class LinkMode extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'link_mode';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name', 'rank'], 'required'],
            [['rank'], 'default', 'value' => null],
            [['rank'], 'integer'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 64],
            [['key'], 'unique'],
            [['rank'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
            'rank' => 'Rank',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['link_mode_id' => 'id']);
    }
}
