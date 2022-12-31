<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatoon_version2".
 *
 * @property integer $id
 * @property string $tag
 * @property string $name
 * @property string $released_at
 * @property integer $group_id
 *
 * @property Battle2[] $battles
 * @property SplatoonVersionGroup2 $group
 * @property StatWeapon2Result[] $statWeapon2Results
 */
class SplatoonVersion2 extends ActiveRecord
{
    public static function findCurrentVersion($at = null): ?self
    {
        if ($at === null) {
            $at = (int)($_SERVER['REQUEST_TIME'] ?? time());
        }
        if (is_int($at)) {
            $at = gmdate('Y-m-d\TH:i:sP', $at);
        } elseif ($at instanceof \DateTimeInterface) {
            $at = $at->format('Y-m-d\TH:i:sP');
        }
        return static::find()
            ->andWhere(['<=', 'released_at', $at])
            ->orderBy('[[released_at]] DESC')
            ->limit(1)
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'splatoon_version2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag', 'name', 'released_at', 'group_id'], 'required'],
            [['released_at'], 'safe'],
            [['group_id'], 'default', 'value' => null],
            [['group_id'], 'integer'],
            [['tag'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
            [['tag'], 'unique'],
            [['group_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SplatoonVersionGroup2::class,
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
            'tag' => 'Tag',
            'name' => 'Name',
            'released_at' => 'Released At',
            'group_id' => 'Group ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle2::class, ['version_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(SplatoonVersionGroup2::class, ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatWeapon2Results()
    {
        return $this->hasMany(StatWeapon2Result::class, ['version_id' => 'id']);
    }
}
