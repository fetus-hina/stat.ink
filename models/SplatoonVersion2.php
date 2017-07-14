<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "splatoon_version2".
 *
 * @property integer $id
 * @property string $tag
 * @property string $name
 * @property string $released_at
 */
class SplatoonVersion2 extends ActiveRecord
{
    public static function findCurrentVersion($at = null) : ?self
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
            [['tag', 'name', 'released_at'], 'required'],
            [['released_at'], 'safe'],
            [['tag'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
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
}
