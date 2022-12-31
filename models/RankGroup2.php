<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use app\components\helpers\Translator;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rank_group2".
 *
 * @property integer $id
 * @property integer $rank
 * @property string $key
 * @property string $name
 *
 * @property Rank2[] $ranks
 */
class RankGroup2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rank_group2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rank', 'key', 'name'], 'required'],
            [['rank'], 'integer'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['name'], 'unique'],
            [['rank'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rank' => 'Rank',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRanks()
    {
        return $this->hasMany(Rank2::class, ['group_id' => 'id']);
    }

    public function toJsonArray(): array
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-rank2', $this->name),
        ];
    }
}
