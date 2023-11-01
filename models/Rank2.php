<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use app\components\helpers\Translator;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rank2".
 *
 * @property integer $id
 * @property integer $group_id
 * @property integer $rank
 * @property string $key
 * @property string $name
 * @property integer $int_base
 *
 * @property Battle2[] $battles
 * @property RankGroup2 $group
 */
class Rank2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rank2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'rank', 'key', 'name', 'int_base'], 'required'],
            [['group_id', 'rank', 'int_base'], 'integer'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['rank'], 'unique'],
            [['group_id'], 'exist', 'skipOnError' => true,
                'targetClass' => RankGroup2::class,
                'targetAttribute' => ['group_id' => 'id'],
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
            'group_id' => 'Group ID',
            'rank' => 'Rank',
            'key' => 'Key',
            'name' => 'Name',
            'int_base' => 'Int Base',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle2::class, ['rank_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(RankGroup2::class, ['id' => 'group_id']);
    }

    public function toJsonArray(): array
    {
        return [
            'key' => $this->key,
            'zone' => $this->group->toJsonArray(),
            'name' => Translator::translateToAll('app-rank2', $this->name),
        ];
    }

    public static function parseRankNumber(int $rankNumber): ?array
    {
        $numberInRank = $rankNumber % 100;
        $rankNumber = $rankNumber - $numberInRank;
        $model = self::findOne(['int_base' => $rankNumber]);
        if (!$model) {
            return null;
        }

        if ($model->key !== 's+') {
            return [$model->name, null];
        }

        return [$model->name, $numberInRank];
    }
}
