<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Throwable;
use Yii;
use app\components\helpers\CriticalSection;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function mb_substr;
use function trim;

/**
 * This is the model class for table "team_nickname2".
 *
 * @property integer $id
 * @property string $name
 *
 * @property Battle2[] $battle2s
 * @property Battle2[] $battle2s0
 */
class TeamNickname2 extends ActiveRecord
{
    public static function findOrCreate(string $name): ?self
    {
        $name = mb_substr(trim($name), 0, 128, 'UTF-8');

        if ($model = static::findOne(['name' => $name])) {
            return $model;
        }

        try {
            $lock = Yii::createObject([
                'class' => CriticalSection::class,
                'name' => self::class,
                'timeout' => 0,
                'mutex' => Yii::$app->pgMutex,
            ])->enter();
            try {
                if ($model = static::findOne(['name' => $name])) {
                    return $model;
                }

                $model = Yii::createObject([
                    'class' => static::class,
                    'name' => $name,
                ]);
                if ($model->save()) {
                    return $model;
                }
            } finally {
                unset($lock);
            }
        } catch (Throwable $e) {
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'team_nickname2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 128],
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
     * @return ActiveQuery
     */
    public function getBattle2s()
    {
        return $this->hasMany(Battle2::class, ['my_team_nickname_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getBattle2s0()
    {
        return $this->hasMany(Battle2::class, ['his_team_nickname_id' => 'id']);
    }
}
