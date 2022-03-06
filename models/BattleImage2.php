<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\RandomFilename;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "battle_image2".
 *
 * @property int $id
 * @property int $battle_id
 * @property int $type_id
 * @property string $filename
 * @property int $bucket_id
 *
 * @property Battle2 $battle
 * @property ImageBucket $bucket
 * @property BattleImageType $type
 *
 * @property-read string $url
 */
class BattleImage2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_image2';
    }

    public static function generateFilename(bool $checkDupe = true): string
    {
        while (true) {
            $path = RandomFilename::generate('jpg', 1);
            if ($checkDupe) {
                if (static::findOne(['filename' => $path])) {
                    continue;
                }
                if (BattleImage::findOne(['filename' => $path])) {
                    continue;
                }
            }
            return $path;
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['battle_id', 'type_id', 'filename'], 'required'],
            [['bucket_id'], 'default', 'value' => null],
            [['battle_id', 'type_id', 'bucket_id'], 'integer'],
            [['filename'], 'string', 'max' => 64],
            [['battle_id', 'type_id'], 'unique',
                'targetAttribute' => ['battle_id', 'type_id'],
                'message' => 'The combination of Battle ID and Type ID has already been taken.',
            ],
            [['filename'], 'unique'],
            [['battle_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Battle2::class,
                'targetAttribute' => ['battle_id' => 'id'],
            ],
            [['type_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => BattleImageType::class,
                'targetAttribute' => ['type_id' => 'id'],
            ],
            [['bucket_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => ImageBucket::class,
                'targetAttribute' => ['bucket_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'battle_id' => 'Battle ID',
            'bucket_id' => 'Bucket ID',
            'filename' => 'Filename',
            'id' => 'ID',
            'type_id' => 'Type ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattle()
    {
        return $this->hasOne(Battle2::class, ['id' => 'battle_id']);
    }

    public function getBucket(): ActiveQuery
    {
        return $this->hasOne(ImageBucket::class, ['id' => 'bucket_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(BattleImageType::class, ['id' => 'type_id']);
    }

    public function getUrl()
    {
        $path = Yii::getAlias('@imageurl') . '/' . $this->filename;
        return Url::to($path, true);
    }

    private $deleteFilename;

    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }
        $this->deleteFilename = $this->filename;
        return true;
    }

    public function afterDelete()
    {
        if ($this->deleteFilename) {
            $path = Yii::getAlias('@app/web/images') . '/' . $this->deleteFilename;
            foreach (['.jpg', '.lep'] as $ext) {
                $path2 = preg_replace('/\.jpg$/', $ext, $path);
                if (file_exists($path2) && is_file($path2)) {
                    unlink($path2);
                }
            }
        }
        return parent::afterDelete();
    }
}
