<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "language".
 *
 * @property integer $id
 * @property string $lang
 * @property string $name
 * @property string $name_en
 *
 * @property LanguageCharset[] $languageCharsets
 * @property Charset[] $charsets
 * @property Slack[] $slacks
 */
class Language extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lang', 'name', 'name_en'], 'required'],
            [['lang'], 'string', 'max' => 5],
            [['name', 'name_en'], 'string', 'max' => 32],
            [['lang'], 'unique'],
            [['name_en'], 'unique'],
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
            'lang' => 'Lang',
            'name' => 'Name',
            'name_en' => 'Name En',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguageCharsets()
    {
        return $this->hasMany(LanguageCharset::class, ['language_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharsets()
    {
        return $this->hasMany(Charset::class, ['id' => 'charset_id'])
            ->viaTable('language_charset', ['language_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSlacks()
    {
        return $this->hasMany(Slack::class, ['language_id' => 'id']);
    }
}
