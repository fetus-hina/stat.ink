<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "ikalog_version".
 *
 * @property integer $id
 * @property string $revision
 * @property string $summary
 * @property string $at
 *
 * @property WinikalogVersion[] $winikalogVersions
 */
class IkalogVersion extends \yii\db\ActiveRecord
{
    public static function findOneByRevision($rev)
    {
        $regex = sprintf('^%s.+', preg_quote($rev, ''));
        return static::find()
            ->andWhere(['~*', '{{ikalog_version}}.[[revision]]', $regex])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ikalog_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['revision', 'at'], 'required'],
            [['summary'], 'string'],
            [['at'], 'safe'],
            [['revision'], 'string', 'max' => 40],
            [['revision'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'revision' => 'Revision',
            'summary' => 'Summary',
            'at' => 'At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWinikalogVersions()
    {
        return $this->hasMany(WinikalogVersion::class, ['revision_id' => 'id']);
    }
}
