<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
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

    #[Override]
    public function rules()
    {
        return [
            [['category', 'message'], 'required'],
            [['message'], 'string'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    #[Override]
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
