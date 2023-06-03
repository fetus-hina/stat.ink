<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "translate_source_message".
 *
 * @property integer $id
 * @property string $category
 * @property string $message
 *
 * @property TranslateMessage[] $translateMessages
 */
class TranslateSourceMessage extends ActiveRecord
{
    public static function tableName()
    {
        return 'translate_source_message';
    }

    public function rules()
    {
        return [
            [['category', 'message'], 'required'],
            [['message'], 'string'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category' => 'Category',
            'message' => 'Message',
        ];
    }

    public function getTranslateMessages(): ActiveQuery
    {
        return $this->hasMany(TranslateMessage::class, ['id' => 'id']);
    }
}
