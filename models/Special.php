<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\helpers\Translator;

/**
 * This is the model class for table "special".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 */
class Special extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'special';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key', 'name'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    public function toJsonArray()
    {
        return [
            'key' => $this->key,
            'name' => Translator::translateToAll('app-special', $this->name),
        ];
    }
}
