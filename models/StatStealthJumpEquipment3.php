<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_stealth_jump_equipment3".
 *
 * @property integer $season_id
 * @property integer $rule_id
 * @property string $x_power
 * @property integer $players
 * @property integer $equipments
 *
 * @property Rule3 $rule
 * @property Season3 $season
 */
class StatStealthJumpEquipment3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_stealth_jump_equipment3';
    }

    public function rules()
    {
        return [
            [['season_id', 'rule_id', 'x_power', 'players', 'equipments'], 'required'],
            [['season_id', 'rule_id', 'players', 'equipments'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'players', 'equipments'], 'integer'],
            [['x_power'], 'number'],
            [['season_id', 'rule_id', 'x_power'], 'unique', 'targetAttribute' => ['season_id', 'rule_id', 'x_power']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'rule_id' => 'Rule ID',
            'x_power' => 'X Power',
            'players' => 'Players',
            'equipments' => 'Equipments',
        ];
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule3::class, ['id' => 'rule_id']);
    }

    public function getSeason(): ActiveQuery
    {
        return $this->hasOne(Season3::class, ['id' => 'season_id']);
    }
}
