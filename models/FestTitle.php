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
 * This is the model class for table "fest_title".
 *
 * @property integer $id
 * @property string $key
 *
 * @property Battle[] $battles
 * @property FestTitleGender[] $festTitleGenders
 * @property Gender[] $genders
 */
class FestTitle extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fest_title';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'key'], 'required'],
            [['id'], 'integer'],
            [['key'], 'string', 'max' => 16],
            [['key'], 'unique']
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
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::className(), ['fest_title_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFestTitleGenders()
    {
        return $this->hasMany(FestTitleGender::className(), ['title_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGenders()
    {
        return $this
            ->hasMany(Gender::className(), ['id' => 'gender_id'])
            ->viaTable('fest_title_gender', ['title_id' => 'id']);
    }

    public function getName(Gender $gender)
    {
        return $this->getFestTitleGenders()->andWhere(['gender_id' => $gender->id])->one()->name;
    }

    public function toJsonArray(Gender $gender)
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll(
                'app',
                $this->getFestTitleGenders()->andWhere(['gender_id' => $gender->id])->one()->name,
                [ '***', '***' ]
            ),
        ];
    }
}
