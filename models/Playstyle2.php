<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "playstyle2".
 *
 * @property integer $ns_mode_id
 * @property integer $controller_mode_id
 *
 * @property ControllerMode2 $controllerMode
 * @property NsMode2 $nsMode
 */
class Playstyle2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'playstyle2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ns_mode_id', 'controller_mode_id'], 'required'],
            [['ns_mode_id', 'controller_mode_id'], 'integer'],
            [['controller_mode_id'], 'exist', 'skipOnError' => true,
                'targetClass' => ControllerMode2::class,
                'targetAttribute' => ['controller_mode_id' => 'id'],
            ],
            [['ns_mode_id'], 'exist', 'skipOnError' => true,
                'targetClass' => NsMode2::class,
                'targetAttribute' => ['ns_mode_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ns_mode_id' => 'Ns Mode ID',
            'controller_mode_id' => 'Controller Mode ID',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getControllerMode()
    {
        return $this->hasOne(ControllerMode2::class, ['id' => 'controller_mode_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getNsMode()
    {
        return $this->hasOne(NsMode2::class, ['id' => 'ns_mode_id']);
    }
}
