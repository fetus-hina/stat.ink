<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use app\components\behaviors\CompressBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "battle_events2".
 *
 * @property integer $id
 * @property string $events
 *
 * @property Battle2 $battle2
 */
class BattleEvents2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_events2';
    }

    public function behaviors()
    {
        return [
            [
                'class' => CompressBehavior::class,
                'attribute' => 'events',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'events'], 'required'],
            [['id'], 'integer'],
            [['events'], 'string'],
            [['id'], 'exist', 'skipOnError' => true,
                'targetClass' => Battle2::class,
                'targetAttribute' => ['id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'events' => 'Events',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBattle2()
    {
        return $this->hasOne(Battle2::class, ['id' => 'id']);
    }
}
