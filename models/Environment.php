<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "environment".
 *
 * @property integer $id
 * @property string $sha256sum
 * @property string $text
 *
 * @property Battle[] $battles
 * @property User[] $users
 */
class Environment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'environment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sha256sum', 'text'], 'required'],
            [['text'], 'string'],
            [['sha256sum'], 'string', 'max' => 43],
            [['sha256sum'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sha256sum' => 'Sha256sum',
            'text' => 'Text',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::class, ['env_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['env_id' => 'id']);
    }
}
