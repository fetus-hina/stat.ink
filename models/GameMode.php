<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "game_mode".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Rule[] $rules
 */
class GameMode extends \yii\db\ActiveRecord
{
    use SafeFindOneTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'game_mode';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 32],
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRules()
    {
        return $this->hasMany(Rule::className(), ['mode_id' => 'id']);
    }
}
