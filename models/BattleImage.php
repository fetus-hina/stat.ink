<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\RandomFilename;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Url;

use function file_exists;
use function is_file;
use function preg_replace;
use function unlink;

/**
 * This is the model class for table "battle_image".
 *
 * @property integer $id
 * @property integer $battle_id
 * @property integer $type_id
 * @property string $filename
 * @property integer $bucket_id
 *
 * @property Battle $battle
 * @property ImageBucket $bucket
 * @property BattleImageType $type
 *
 * @property-read string $url
 */
class BattleImage extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_image';
    }

    public static function generateFilename(bool $checkDupe = true): string
    {
        while (true) {
            $path = RandomFilename::generate('jpg', 1);
            if ($checkDupe) {
                if (self::findOne(['filename' => $path])) {
                    continue;
                }
                if (BattleImage2::findOne(['filename' => $path])) {
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
            [['bucket_id'], 'default', 'value' => null],
            [['battle_id', 'type_id', 'filename'], 'required'],
            [['battle_id', 'type_id', 'bucket_id'], 'integer'],
            [['filename'], 'string', 'max' => 64],
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
     * @return ActiveQuery
     */
    public function getBattle()
    {
        return $this->hasOne(Battle::class, ['id' => 'battle_id']);
    }

    public function getBucket(): ActiveQuery
    {
        return $this->hasOne(ImageBucket::class, ['id' => 'bucket_id']);
    }

    /**
     * @return ActiveQuery
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
