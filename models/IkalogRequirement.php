<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

/**
 * This is the model class for table "ikalog_requirement".
 *
 * @property integer $id
 * @property string $from
 * @property string $version_date
 */
class IkalogRequirement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ikalog_requirement';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'version_date'], 'required'],
            [['from'], 'safe'],
            [['version_date'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from' => 'From',
            'version_date' => 'Version Date',
        ];
    }
}
