<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use DateTime;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

use function gmdate;
use function is_int;
use function time;

/**
 * This is the model class for table "splatoon_version".
 *
 * @property integer $id
 * @property string $tag
 * @property string $name
 * @property string $released_at
 *
 * @property Battle[] $battles
 */
class SplatoonVersion extends ActiveRecord
{
    public static function findCurrentVersion($at = null)
    {
        if ($at === null) {
            $at = (int)($_SERVER['REQUEST_TIME'] ?? time());
        }
        if (is_int($at)) {
            $at = gmdate('Y-m-d\TH:i:sP', $at);
        } elseif ($at instanceof DateTime) {
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
        return 'splatoon_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tag', 'name', 'released_at'], 'required'],
            [['released_at'], 'safe'],
            [['tag', 'name'], 'string', 'max' => 32],
            [['tag'], 'unique'],
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
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getBattles()
    {
        return $this->hasMany(Battle::class, ['version_id' => 'id']);
    }
}
