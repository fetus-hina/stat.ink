<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatfest2_theme".
 *
 * @property int $id
 * @property string $name
 *
 * @property Battle2[] $battle2s
 * @property Battle2[] $battle2s0
 */
class Splatfest2Theme extends ActiveRecord
{
    public static function findOrCreate(string $name): ?self
    {
        if ($model = static::findOne(['name' => $name])) {
            return $model;
        }
        if (!self::lockForFindOrCreate()) {
            return null;
        }
        try {
            // maybe created in another process while getting a lock
            if ($model = static::findOne(['name' => $name])) {
                return $model;
            }

            $model = Yii::createObject([
                'class' => static::class,
                'name' => $name,
            ]);
            if (!$model->save()) {
                // WTF
                return null;
            }
            return $model;
        } finally {
            self::freeForFindOrCreate();
        }
    }

    private static function lockForFindOrCreate()
    {
        $timeout = microtime(true) + 30.0;
        while (microtime(true) <= $timeout) {
            if (Yii::$app->pgMutex->acquire(self::class)) {
                return true;
            }
            usleep(1);
        }
        return false;
    }

    private static function freeForFindOrCreate()
    {
        Yii::$app->pgMutex->release(self::class);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'splatfest2_theme';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 32],
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
    public function getBattle2s()
    {
        return $this->hasMany(Battle2::class, ['my_team_fest_theme_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattle2s0()
    {
        return $this->hasMany(Battle2::class, ['his_team_fest_theme_id' => 'id']);
    }
}
