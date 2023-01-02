<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_event3_alias".
 *
 * @property integer $id
 * @property string $key
 * @property integer $event_id
 *
 * @property SalmonEvent3 $event
 */
class SalmonEvent3Alias extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_event3_alias';
    }

    public function rules()
    {
        return [
            [['key', 'event_id'], 'required'],
            [['event_id'], 'default', 'value' => null],
            [['event_id'], 'integer'],
            [['key'], 'string', 'max' => 32],
            [['key'], 'unique'],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => SalmonEvent3::class, 'targetAttribute' => ['event_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'event_id' => 'Event ID',
        ];
    }

    public function getEvent(): ActiveQuery
    {
        return $this->hasOne(SalmonEvent3::class, ['id' => 'event_id']);
    }
}
