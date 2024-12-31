<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "event3".
 *
 * @property integer $id
 * @property string $internal_id
 * @property string $name
 * @property string $desc
 * @property string $regulation
 *
 * @property Battle3[] $battle3s
 * @property EventSchedule3[] $eventSchedule3s
 */
class Event3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'event3';
    }

    public function rules()
    {
        return [
            [['internal_id', 'name'], 'required'],
            [['name', 'desc', 'regulation'], 'string'],
            [['internal_id'], 'string', 'max' => 128],
            [['internal_id'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'internal_id' => 'Internal ID',
            'name' => 'Name',
            'desc' => 'Desc',
            'regulation' => 'Regulation',
        ];
    }

    public function getBattle3s(): ActiveQuery
    {
        return $this->hasMany(Battle3::class, ['event_id' => 'id']);
    }

    public function getEventSchedule3s(): ActiveQuery
    {
        return $this->hasMany(EventSchedule3::class, ['event_id' => 'id']);
    }
}
