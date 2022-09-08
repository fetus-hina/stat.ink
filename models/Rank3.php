<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "rank3".
 *
 * @property integer $id
 * @property string $key
 * @property integer $group_id
 * @property string $name
 * @property integer $rank
 *
 * @property Battle3[] $battle3s
 * @property Battle3[] $battle3s0
 * @property RankGroup3 $group
 */
class Rank3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'rank3';
    }

    public function rules()
    {
        return [
            [['key', 'group_id', 'name', 'rank'], 'required'],
            [['group_id', 'rank'], 'default', 'value' => null],
            [['group_id', 'rank'], 'integer'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => RankGroup3::class, 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'group_id' => 'Group ID',
            'name' => 'Name',
            'rank' => 'Rank',
        ];
    }

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['rank_before_id' => 'id']);
    }

    public function getBattle3s0(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['rank_after_id' => 'id']);
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(RankGroup3::class, ['id' => 'group_id']);
    }
}
