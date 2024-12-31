<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatoon_version3".
 *
 * @property integer $id
 * @property string $tag
 * @property integer $group_id
 * @property string $name
 * @property string $release_at
 *
 * @property Battle3[] $battle3s
 * @property SplatoonVersionGroup3 $group
 */
class SplatoonVersion3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'splatoon_version3';
    }

    public function rules()
    {
        return [
            [['tag', 'group_id', 'name', 'release_at'], 'required'],
            [['group_id'], 'default', 'value' => null],
            [['group_id'], 'integer'],
            [['release_at'], 'safe'],
            [['tag'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => SplatoonVersionGroup3::class, 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag' => 'Tag',
            'group_id' => 'Group ID',
            'name' => 'Name',
            'release_at' => 'Release At',
        ];
    }

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['version_id' => 'id']);
    }

    public function getGroup(): ActiveQuery
    {
        return $this->hasOne(SplatoonVersionGroup3::class, ['id' => 'group_id']);
    }
}
