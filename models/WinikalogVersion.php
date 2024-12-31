<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "winikalog_version".
 *
 * @property integer $id
 * @property integer $revision_id
 * @property string $build_at
 *
 * @property IkalogVersion $revision
 */
class WinikalogVersion extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'winikalog_version';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['revision_id'], 'integer'],
            [['build_at'], 'required'],
            [['build_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'revision_id' => 'Revision ID',
            'build_at' => 'Build At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getRevision()
    {
        return $this->hasOne(IkalogVersion::class, ['id' => 'revision_id']);
    }
}
