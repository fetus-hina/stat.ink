<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_event3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 *
 * @property Salmon3UserStatsEvent[] $salmon3UserStatsEvents
 * @property SalmonEvent3Alias[] $salmonEvent3Aliases
 * @property SalmonWave3[] $salmonWave3s
 * @property StatSalmon3TideEvent[] $statSalmon3TideEvents
 */
class SalmonEvent3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'salmon_event3';
    }

    public function rules()
    {
        return [
            [['key', 'name'], 'required'],
            [['key'], 'string', 'max' => 32],
            [['name'], 'string', 'max' => 64],
            [['key'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => 'Key',
            'name' => 'Name',
        ];
    }

    public function getSalmon3UserStatsEvents(): ActiveQuery
    {
        return $this->hasMany(Salmon3UserStatsEvent::class, ['event_id' => 'id']);
    }

    public function getSalmonEvent3Aliases(): ActiveQuery
    {
        return $this->hasMany(SalmonEvent3Alias::class, ['event_id' => 'id']);
    }

    public function getSalmonWave3s(): ActiveQuery
    {
        return $this->hasMany(SalmonWave3::class, ['event_id' => 'id']);
    }

    public function getStatSalmon3TideEvents(): ActiveQuery
    {
        return $this->hasMany(StatSalmon3TideEvent::class, ['event_id' => 'id']);
    }
}
