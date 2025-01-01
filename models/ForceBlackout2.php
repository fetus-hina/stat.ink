<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "force_blackout2".
 *
 * @property string $splatnet_id
 * @property string $note
 */
class ForceBlackout2 extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'force_blackout2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['splatnet_id'], 'required'],
            [['note'], 'string'],
            [['splatnet_id'], 'string', 'max' => 16],
            [['splatnet_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'splatnet_id' => 'Splatnet ID',
            'note' => 'Note',
        ];
    }
}
