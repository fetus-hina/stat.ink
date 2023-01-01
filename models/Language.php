<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "language".
 *
 * @property integer $id
 * @property string $lang
 * @property string $name
 * @property string $name_en
 * @property integer $support_level_id
 *
 * @property SupportLevel $supportLevel
 * @property LanguageCharset[] $languageCharsets
 * @property Charset[] $charsets
 * @property Slack[] $slacks
 * @property User[] $users
 *
 * @property-read string[] $htmlClasses
 */
class Language extends ActiveRecord
{
    public static function find(): ActiveQuery
    {
        return new class (static::class) extends ActiveQuery {
            public function standard(): self
            {
                return $this->andWhere(['and',
                    ['not', ['like', '{{language}}.[[lang]]', '%@%', false]],
                ]);
            }
        };
    }

    public static function tableName()
    {
        return 'language';
    }

    public function rules()
    {
        return [
            [['lang', 'name', 'name_en', 'support_level_id'], 'required'],
            [['support_level_id'], 'default', 'value' => null],
            [['support_level_id'], 'integer'],
            [['lang', 'name', 'name_en'], 'string', 'max' => 32],
            [['lang'], 'unique'],
            [['name_en'], 'unique'],
            [['name'], 'unique'],
            [['support_level_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SupportLevel::class,
                'targetAttribute' => ['support_level_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lang' => 'Lang',
            'name' => 'Name',
            'name_en' => 'Name En',
            'support_level_id' => 'Support Level ID',
        ];
    }

    public function getLanguageId(): string
    {
        return vsprintf('%s-%s', $this->splitLangId());
    }

    public function getLanguageCode(): string
    {
        return $this->splitLangId()[0];
    }

    public function getCountryCode(): string
    {
        return strtolower($this->splitLangId()[1]);
    }

    private function splitLangId(): array
    {
        if (!preg_match('/^(\w+)[-_](\w+)/', $this->lang, $match)) {
            throw new \Exception('Invalid language format: ' . $this->lang);
        }

        return [
            $match[1],
            $match[2],
        ];
    }

    /**
     * @return string[]
     */
    public function getHtmlClasses(): array
    {
        return [
            'lang-' . \strtolower($this->getLanguageCode()), // lang-ja
            'lang-' . \strtolower($this->getLanguageId()), // lang-ja-jp
        ];
    }

    public function getSupportLevel(): ActiveQuery
    {
        return $this->hasOne(SupportLevel::class, ['id' => 'support_level_id']);
    }

    public function getLanguageCharsets(): ActiveQuery
    {
        return $this->hasMany(LanguageCharset::class, ['language_id' => 'id']);
    }

    public function getCharsets(): ActiveQuery
    {
        return $this->hasMany(Charset::class, ['id' => 'charset_id'])
            ->viaTable('language_charset', ['language_id' => 'id']);
    }

    public function getSlacks(): ActiveQuery
    {
        return $this->hasMany(Slack::class, ['language_id' => 'id']);
    }

    public function getUsers(): ActiveQuery
    {
        return $this->hasMany(User::class, ['default_language_id' => 'id']);
    }
}
