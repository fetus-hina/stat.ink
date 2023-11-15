<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_title3_alias".
 *
 * @property integer $id
 * @property string $key
 * @property integer $title_id
 *
 * @property SalmonTitle3 $title
 */
class SalmonTitle3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_title3_alias';
    }

    public function rules()
    {
        return [
            [['key', 'title_id'], 'required'],
            [['title_id'], 'default', 'value' => null],
            [['title_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['title_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonTitle3::class, 'targetAttribute' => ['title_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'title_id' => 'Title ID',
        ];
    }

    public function getTitle(): ActiveQuery
    {
        return $this->hasOne(SalmonTitle3::class, ['id' => 'title_id']);
    }
}
