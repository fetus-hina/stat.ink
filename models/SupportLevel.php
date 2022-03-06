<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "support_level".
 *
 * @property int $id
 * @property string $name
 *
 * @property Language[] $languages
 */
class SupportLevel extends ActiveRecord
{
    public const FULL = 1;
    public const ALMOST = 2;
    public const PARTIAL = 3;
    public const FEW = 4;
    public const MACHINE = 5;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'support_level';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'default', 'value' => null],
            [['id'], 'integer'],
            [['name'], 'string', 'max' => 32],
            [['id'], 'unique'],
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
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasMany(Language::class, ['support_level_id' => 'id']);
    }
}
