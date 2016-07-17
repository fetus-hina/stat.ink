<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use app\components\helpers\RandomFilename;

/**
 * This is the model class for table "battle_image".
 *
 * @property integer $id
 * @property integer $battle_id
 * @property integer $type_id
 * @property string $filename
 *
 * @property Battle $battle
 * @property BattleImageType $type
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

    public static function generateFilename()
    {
        while (true) {
            $name = RandomFilename::generate('jpg');
            $path = substr($name, 0, 2) . '/' . $name;
            if (!BattleImage::findOne(['filename' => $path])) {
                return $path;
            }
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
            [['filename'], 'string', 'max' => 64]
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
        return $this->hasOne(Battle::className(), ['id' => 'battle_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(BattleImageType::className(), ['id' => 'type_id']);
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
