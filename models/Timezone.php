<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "timezone".
 *
 * @property integer $id
 * @property string $zone
 */
class Timezone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timezone';
    }

    public static function find()
    {
        return parent::find()->orderBy('{{timezone}}.[[zone]] ASC');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['zone'], 'required'],
            [['zone'], 'string', 'max' => 64],
            [['zone'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'zone' => 'Zone',
        ];
    }
}
