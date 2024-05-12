<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "translate_message".
 *
 * @property integer $id
 * @property string $language
 * @property string $translation
 *
 * @property TranslateSourceMessage $id0
 */
class TranslateMessage extends ActiveRecord
{
    public static function tableName()
    {
        return 'translate_message';
    }

    public function rules()
    {
        return [
            [['id', 'language', 'translation'], 'required'],
            [['id'], 'default', 'value' => null],
            [['id'], 'integer'],
            [['translation'], 'string'],
            [['language'], 'string', 'max' => 16],
            [['language', 'id'], 'unique', 'targetAttribute' => ['language', 'id']],
            [['id', 'language'], 'unique', 'targetAttribute' => ['id', 'language']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => TranslateSourceMessage::class, 'targetAttribute' => ['id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'language' => 'Language',
            'translation' => 'Translation',
        ];
    }

    public function getId0(): ActiveQuery
    {
        return $this->hasOne(TranslateSourceMessage::class, ['id' => 'id']);
    }
}
