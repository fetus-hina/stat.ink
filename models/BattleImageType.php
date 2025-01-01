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
 * This is the model class for table "battle_image_type".
 *
 * @property integer $id
 * @property string $name
 *
 * @property BattleImage[] $battleImages
 */
class BattleImageType extends ActiveRecord
{
    public const ID_JUDGE = 1;
    public const ID_RESULT = 2;
    public const ID_GEAR = 3;

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
            [['name'], 'unique'],
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
     * @return ActiveQuery
     */
    public function getBattleImages()
    {
        return $this->hasMany(BattleImage::class, ['type_id' => 'id']);
    }
}
