<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "accept_language".
 *
 * @property integer $id
 * @property string $rule
 * @property integer $language_id
 *
 * @property Language $language
 */
class AcceptLanguage extends ActiveRecord
{
    public static function find(): ActiveQuery
    {
        return new class (static::class) extends ActiveQuery {
            public function all($db = null)
            {
                if ($list = parent::all($db)) {
                    if ($list[0] instanceof AcceptLanguage) {
                        usort($list, [AcceptLanguage::class, 'compare']);
                    }
                }
                return $list;
            }
        };
    }

    public static function findMatched(string $test): ?self
    {
        foreach (static::find()->all() as $self) {
            if ($self->isMatch($test)) {
                return $self;
            }
        }

        return null;
    }

    public static function tableName()
    {
        return 'accept_language';
    }

    public static function compare(self $a, self $b): int
    {
        $ruleA = $a->rule;
        $ruleB = $b->rule;

        if ($ruleA === '*' || $ruleB === '*') {
            return ($ruleA === '*') ? 1 : -1;
        }

        return (strlen($ruleB) - strlen($ruleA))
            ?: strcmp(str_replace('*', chr(0), $ruleA), str_replace('*', chr(0), $ruleB));
    }

    public function isMatch(string $test): bool
    {
        $test = strtolower($test);

        if (preg_match('/^([a-z]+)\*$/', $this->rule, $match)) { // "ja*" form
            // test it as "ja or ja-*"
            return ($test === $match[1]) || StringHelper::matchWildcard($match[1] . '-*', $test);
        }

        return StringHelper::matchWildcard($this->rule, $test);
    }

    public function rules()
    {
        return [
            [['rule', 'language_id'], 'required'],
            [['language_id'], 'default', 'value' => null],
            [['language_id'], 'integer'],
            [['rule'], 'string', 'max' => 17],
            [['rule'], 'unique'],
            [['language_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Language::class,
                'targetAttribute' => ['language_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rule' => 'Rule',
            'language_id' => 'Language ID',
        ];
    }

    public function getLanguage(): ActiveQuery
    {
        return $this->hasOne(Language::class, ['id' => 'language_id']);
    }
}
