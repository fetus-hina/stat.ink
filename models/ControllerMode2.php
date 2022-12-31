<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "controller_mode2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Playstyle2[] $playstyles
 * @property NsMode2[] $nsModes
 */
class ControllerMode2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'controller_mode2';
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
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlaystyles()
    {
        return $this->hasMany(Playstyle2::class, ['controller_mode_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNsModes()
    {
        return $this->hasMany(NsMode2::class, ['id' => 'ns_mode_id'])
            ->viaTable('playstyle2', ['controller_mode_id' => 'id']);
    }
}
