<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use app\components\helpers\Translator;
use yii\helpers\ArrayHelper;

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
        return $this->hasMany(Battle::class, ['fest_title_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFestTitleGenders()
    {
        return $this->hasMany(FestTitleGender::class, ['title_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGenders()
    {
        return $this
            ->hasMany(Gender::class, ['id' => 'gender_id'])
            ->viaTable('fest_title_gender', ['title_id' => 'id']);
    }

    public function getName(?Gender $gender = null)
    {
        // 性別不明なとき
        if ($gender === null) {
            return $this->name;
        }
        if (!$festTitleGender = $this->getFestTitleGender($gender)) {
            return $this->name;
        }
        return $festTitleGender->name;
    }

    private function getFestTitleGender(Gender $gender): ?FestTitleGender
    {
        // フェスの称号は全件とっても大した件数ではないので全部取得してキャッシュする
        static $cache = null;
        if (!$cache) {
            $cache = ArrayHelper::map(
                FestTitleGender::find()->orderBy(['title_id' => SORT_ASC, 'gender_id' => SORT_ASC])->all(),
                'gender_id',
                fn (FestTitleGender $model): FestTitleGender => $model,
                'title_id',
            );
        }
        return $cache[$this->id][$gender->id] ?? null;
    }

    public function toJsonArray(?Gender $gender = null, ?string $theme = null)
    {
        return [
            'key' => $this->key,
            'name' => (function () use ($gender, $theme) {
                if ($gender === null) {
                    return Translator::translateToAll('app-fest', $this->name);
                }
                $genders = array_filter($this->festTitleGenders, fn ($row) => $row->gender_id == $gender->id);
                if (count($genders) !== 1) {
                    return Translator::translateToAll('app-fest', $this->name);
                }
                return Translator::translateToAll('app-fest', array_shift($genders)->name, [
                    $theme ?? '***',
                    $theme ?? '***',
                ]);
            })(),
        ];
    }
}
