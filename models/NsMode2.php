<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ns_mode2".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Playstyle2[] $playstyles
 * @property ControllerMode2[] $controllerModes
 */
class NsMode2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ns_mode2';
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
     * @return ActiveQuery
     */
    public function getPlaystyles()
    {
        return $this->hasMany(Playstyle2::class, ['ns_mode_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getControllerModes()
    {
        return $this->hasMany(ControllerMode2::class, ['id' => 'controller_mode_id'])
            ->viaTable('playstyle2', ['ns_mode_id' => 'id']);
    }
}
