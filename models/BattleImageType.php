<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "battle_image_type".
 *
 * @property integer $id
 * @property string $name
 *
 * @property BattleImage[] $battleImages
 */
class BattleImageType extends \yii\db\ActiveRecord
{
    const ID_JUDGE = 1;
    const ID_RESULT = 2;
    const ID_GEAR = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'battle_image_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
            [['name'], 'string', 'max' => 16],
            [['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBattleImages()
    {
        return $this->hasMany(BattleImage::className(), ['type_id' => 'id']);
    }
}
