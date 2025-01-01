<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "fest_title_gender".
 *
 * @property integer $title_id
 * @property integer $gender_id
 * @property string $name
 *
 * @property FestTitle $title
 * @property Gender $gender
 */
class FestTitleGender extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fest_title_gender';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title_id', 'gender_id', 'name'], 'required'],
            [['title_id', 'gender_id'], 'integer'],
            [['name'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title_id' => 'Title ID',
            'gender_id' => 'Gender ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getTitle()
    {
        return $this->hasOne(FestTitle::class, ['id' => 'title_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGender()
    {
        return $this->hasOne(Gender::class, ['id' => 'gender_id']);
    }
}
