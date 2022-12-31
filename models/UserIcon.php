<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Base32\Base32;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "user_icon".
 *
 * @property integer $user_id
 * @property string $filename
 *
 * @property string $url
 * @property string $absUrl
 *
 * @property User $user
 */
class UserIcon extends ActiveRecord
{
    public const ICON_WIDTH = 500;
    public const ICON_HEIGHT = 500;

    private $mode;
    private $imageResource;

    public static function createNew(int $userId, string $binary)
    {
        $gd = static::resizeImage($binary);
        $obj = Yii::createObject([
            'class' => static::class,
            'user_id' => $userId,
            'filename' => static::createNewFileName(),
        ]);
        $obj->mode = 'new';
        $obj->imageResource = $gd;
        static::getDb()->on(Connection::EVENT_COMMIT_TRANSACTION, [$obj, 'onCommit']);
        return $obj;
    }

    protected static function createNewFileName()
    {
        retry:
        $filename = \strtolower(\rtrim(Base32::encode(\random_bytes(16)), '='));
        $filepath = sprintf('%s/%s.png', substr($filename, 0, 2), $filename);
        if (static::find()->where(['filename' => $filepath])->count() > 0) {
            goto retry;
        }
        return $filepath;
    }

    protected static function resizeImage(string $binary)
    {
        if (!$in = @\imagecreatefromstring($binary)) {
            throw new \Exception();
        }
        $out = \imagecreatetruecolor(static::ICON_WIDTH, static::ICON_HEIGHT);
        $inSize = \min(\imagesx($in), \imagesy($in));
        $inX = (int)(\imagesx($in) / 2 - $inSize / 2);
        $inY = (int)(\imagesy($in) / 2 - $inSize / 2);
        \imagefill($out, 0, 0, 0xffffff);
        \imagesavealpha($out, false);
        \imagealphablending($out, true);
        \imagecopyresampled(
            $out,
            $in,
            0,
            0,
            $inX,
            $inY,
            static::ICON_WIDTH,
            static::ICON_HEIGHT,
            $inSize,
            $inSize,
        );
        imagedestroy($in);
        return $out;
        // }}}
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_icon';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'filename'], 'required'],
            [['user_id'], 'integer'],
            [['filename'], 'string', 'max' => 64],
            [['filename'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'filename' => 'Filename',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getUrl()
    {
        return Yii::getAlias('@web/profile-images') . '/' . $this->filename;
    }

    public function getAbsUrl()
    {
        return Url::to($this->url, true);
    }

    public function afterDelete()
    {
        $this->mode = 'delete';
        static::getDb()->on(Connection::EVENT_COMMIT_TRANSACTION, [$this, 'onCommit']);
        parent::afterDelete();
    }

    public function onCommit()
    {
        // create/delete file on commit event
        switch ($this->mode) {
            case 'new':
                if ($this->imageResource) {
                    $realPath = Yii::getAlias('@app/web/profile-images') . '/' . $this->filename;
                    FileHelper::createDirectory(\dirname($realPath));
                    imagepng($this->imageResource, $realPath, 9, PNG_ALL_FILTERS);
                    imagedestroy($this->imageResource);
                    $this->imageResource = null;
                }
                break;

            case 'delete':
                if ($this->filename) {
                    $realPath = Yii::getAlias('@app/web/profile-images') . '/' . $this->filename;
                    if (\file_exists($realPath)) {
                        @unlink($realPath);
                    }
                }
                break;
        }
    }
}
