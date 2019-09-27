<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use app\components\helpers\RandomFilename;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "battle_image2".
 *
 * @property integer $id
 * @property integer $battle_id
 * @property integer $type_id
 * @property string $filename
 *
 * @property Battle2 $battle
 * @property BattleImageType $type
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

    public static function generateFilename(): string
    {
        while (true) {
            $name = RandomFilename::generate('jpg');
            $path = substr($name, 0, 2) . '/' . $name;
            if (static::findOne(['filename' => $path])) {
                continue;
            }
            if (BattleImage::findOne(['filename' => $path])) {
                continue;
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
            [['battle_id', 'type_id'], 'integer'],
            [['filename'], 'string', 'max' => 64],
            [['battle_id', 'type_id'], 'unique',
                'targetAttribute' => ['battle_id', 'type_id'],
                'message' => 'The combination of Battle ID and Type ID has already been taken.',
            ],
            [['filename'], 'unique'],
            [['battle_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Battle2::class,
                'targetAttribute' => ['battle_id' => 'id'],
            ],
            [['type_id'], 'exist', 'skipOnError' => true,
                'targetClass' => BattleImageType::class,
                'targetAttribute' => ['type_id' => 'id'],
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
            'battle_id' => 'Battle ID',
            'type_id' => 'Type ID',
            'filename' => 'Filename',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattle()
    {
        return $this->hasOne(Battle2::class, ['id' => 'battle_id']);
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
