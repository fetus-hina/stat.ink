<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "charset".
 *
 * @property integer $id
 * @property string $name
 * @property string $php_name
 * @property integer $substitute
 * @property boolean $is_unicode
 * @property integer $order
 *
 * @property LanguageCharset[] $languageCharsets
 * @property Language[] $languages
 */
class Charset extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'charset';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'php_name', 'order'], 'required'],
            [['substitute', 'order'], 'integer'],
            [['is_unicode'], 'boolean'],
            [['name', 'php_name'], 'string', 'max' => 32],
            [['order'], 'unique'],
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
            'php_name' => 'Php Name',
            'substitute' => 'Substitute',
            'is_unicode' => 'Is Unicode',
            'order' => 'Order',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getLanguageCharsets()
    {
        return $this->hasMany(LanguageCharset::class, ['charset_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasMany(Language::class, ['id' => 'language_id'])
            ->viaTable('language_charset', ['charset_id' => 'id']);
    }
}
