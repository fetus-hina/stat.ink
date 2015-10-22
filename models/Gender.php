<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;

/**
 * This is the model class for table "gender".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Battle[] $battles
 * @property FestTitleGender[] $festTitleGenders
 * @property FestTitle[] $titles
 */
class Gender extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gender';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
            [['name'], 'string', 'max' => 16],
            [['name'], 'unique']
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
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::className(), ['gender_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFestTitleGenders()
    {
        return $this->hasMany(FestTitleGender::className(), ['gender_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitles()
    {
        return $this
            ->hasMany(FestTitle::className(), ['id' => 'title_id'])
            ->viaTable('fest_title_gender', ['gender_id' => 'id']);
    }

    public function toJsonArray()
    {
        return [
            'key' => strtolower($this->name),
            'iso5218' => $this->id,
            'name' => Translator::translateToAll('app', $this->name),
        ];
    }
}
