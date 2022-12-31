<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "season3".
 *
 * @property integer $id
 * @property string $key
 * @property string $name
 * @property string $start_at
 * @property string $end_at
 * @property string $term
 *
 * @property Knockout3[] $knockout3s
 * @property Rule3[] $rules
 * @property StatSpecialUse3[] $statSpecialUse3s
 * @property StatXPowerDistrib3[] $statXPowerDistrib3s
 * @property StatXPowerDistribAbstract3[] $statXPowerDistribAbstract3s
 */
class Season3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'season3';
    }

    public function rules()
    {
        return [
            [['key', 'name', 'start_at', 'end_at', 'term'], 'required'],
            [['start_at', 'end_at'], 'safe'],
            [['term'], 'string'],
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
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'term' => 'Term',
        ];
    }

    public function getKnockout3s(): ActiveQuery
    {
        return $this->hasMany(Knockout3::class, ['season_id' => 'id']);
    }

    public function getRules(): ActiveQuery
    {
        return $this->hasMany(Rule3::class, ['id' => 'rule_id'])->viaTable('stat_x_power_distrib_abstract3', ['season_id' => 'id']);
    }

    public function getStatSpecialUse3s(): ActiveQuery
    {
        return $this->hasMany(StatSpecialUse3::class, ['season_id' => 'id']);
    }

    public function getStatXPowerDistrib3s(): ActiveQuery
    {
        return $this->hasMany(StatXPowerDistrib3::class, ['season_id' => 'id']);
    }

    public function getStatXPowerDistribAbstract3s(): ActiveQuery
    {
        return $this->hasMany(StatXPowerDistribAbstract3::class, ['season_id' => 'id']);
    }
}
