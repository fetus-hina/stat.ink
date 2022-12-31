<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "battle_image_result3".
 *
 * @property integer $battle_id
 * @property integer $bucket_id
 * @property string $filename
 *
 * @property Battle3 $battle
 * @property ImageBucket $bucket
 */
class BattleImageResult3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'battle_image_result3';
    }

    public function rules()
    {
        return [
            [['battle_id', 'filename'], 'required'],
            [['battle_id', 'bucket_id'], 'default', 'value' => null],
            [['battle_id', 'bucket_id'], 'integer'],
            [['filename'], 'string', 'max' => 64],
            [['filename'], 'unique'],
            [['battle_id'], 'unique'],
            [['battle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Battle3::class, 'targetAttribute' => ['battle_id' => 'id']],
            [['bucket_id'], 'exist', 'skipOnError' => true, 'targetClass' => ImageBucket::class, 'targetAttribute' => ['bucket_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'battle_id' => 'Battle ID',
            'bucket_id' => 'Bucket ID',
            'filename' => 'Filename',
        ];
    }

    public function getBattle(): ActiveQuery
    {
        return $this->hasOne(Battle3::class, ['id' => 'battle_id']);
    }

    public function getBucket(): ActiveQuery
    {
        return $this->hasOne(ImageBucket::class, ['id' => 'bucket_id']);
    }
}
