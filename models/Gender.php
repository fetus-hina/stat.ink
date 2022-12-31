<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;
use yii\helpers\ArrayHelper;

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
    use openapi\Util;

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
            [['name'], 'unique'],
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
        return $this->hasMany(Battle::class, ['gender_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFestTitleGenders()
    {
        return $this->hasMany(FestTitleGender::class, ['gender_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTitles()
    {
        return $this
            ->hasMany(FestTitle::class, ['id' => 'title_id'])
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

    public static function openApiSchema(): array
    {
        $values = static::find()
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc2', 'Gender information'),
            'properties' => [
                'key' => static::oapiKey(
                    static::oapiKeyValueTable(
                        Yii::t('app-apidoc2', 'Gender'),
                        'app',
                        $values,
                        fn (self $model): string => strtolower($model->name),
                    ),
                    ArrayHelper::getColumn(
                        $values,
                        fn (self $model): string => strtolower($model->name),
                        false,
                    ),
                ),
                'iso5218' => [
                    'type' => 'integer',
                    'format' => 'int32',
                    'description' => Yii::t('app-apidoc2', 'Sex code defined in ISO 5218'),
                ],
                'name' => static::oapiRef(openapi\Name::class),
            ],
            'example' => $values[0]->toJsonArray(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [
            openapi\Name::class,
        ];
    }

    public static function openApiExample(): array
    {
        return array_map(
            fn (self $model): array => $model->toJsonArray(),
            static::find()
                ->orderBy(['id' => SORT_ASC])
                ->all(),
        );
    }
}
