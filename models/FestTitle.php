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
 * @property string $name
 *
 * @property Battle[] $battles
 * @property FestTitleGender[] $festTitleGenders
 * @property Gender[] $genders
 */
class FestTitle extends \yii\db\ActiveRecord
{
    public static function find()
    {
        return parent::find()->with('festTitleGenders');
    }

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
            [['id', 'key', 'name'], 'required'],
            [['id'], 'integer'],
            [['key'], 'string', 'max' => 16],
            [['key'], 'unique'],
            [['name'], 'string', 'max' => 32],
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

    public function getName(Gender $gender = null)
    {
        // 性別不明なとき
        if ($gender === null) {
            return $this->name;
        }
        return $this->getFestTitleGenders()->andWhere(['gender_id' => $gender->id])->one()->name;
    }

    public function toJsonArray(Gender $gender = null)
    {
        return [
            'key' => $this->key,
            'name' => (function () use ($gender) {
                if ($gender === null) {
                    return Translator::translateToAll('app-fest', $this->name);
                }
                $genders = array_filter($this->festTitleGenders, function ($row) use ($gender) {
                    return $row->gender_id == $gender->id;
                });
                if (count($genders) !== 1) {
                    return Translator::translateToAll('app-fest', $this->name);
                }
                return Translator::translateToAll('app-fest', array_shift($genders)->name, ['***', '***']);
            })(),
        ];
    }
}
