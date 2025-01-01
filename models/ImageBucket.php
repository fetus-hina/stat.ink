<?php

/**
 * @copyright Copyright (C) 2021-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "image_bucket".
 *
 * @property integer $id
 * @property string $name
 *
 * @property BattleImage2[] $battleImage2s
 * @property BattleImage[] $battleImages
 */
class ImageBucket extends ActiveRecord
{
    public static function tableName()
    {
        return 'image_bucket';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 63],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public function getBattleImage2s(): ActiveQuery
    {
        return $this->hasMany(BattleImage2::class, ['bucket_id' => 'id']);
    }

    public function getBattleImages(): ActiveQuery
    {
        return $this->hasMany(BattleImage::class, ['bucket_id' => 'id']);
    }
}
