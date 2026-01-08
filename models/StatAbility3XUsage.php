<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_ability3_x_usage".
 *
 * @property integer $season_id
 * @property integer $rule_id
 * @property integer $range_id
 * @property integer $players
 * @property integer $key_players
 * @property double $key_avg_gp
 * @property double $key_sd_gp
 * @property integer $_players
 * @property double $_avg_gp
 * @property double $_sd_gp
 *
 * @property StatWeapon3XUsageRange $range
 * @property Rule3 $rule
 * @property Season3 $season
 */
class StatAbility3XUsage extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_ability3_x_usage';
    }

    #[Override]
    public function rules()
    {
        return [
            [['key_avg_gp', 'key_sd_gp', '_avg_gp', '_sd_gp'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'range_id', 'players', 'key_players', '_players'], 'required'],
            [['season_id', 'rule_id', 'range_id', 'players', 'key_players', '_players'], 'default', 'value' => null],
            [['season_id', 'rule_id', 'range_id', 'players', 'key_players', '_players'], 'integer'],
            [['key_avg_gp', 'key_sd_gp', '_avg_gp', '_sd_gp'], 'number'],
            [['season_id', 'rule_id', 'range_id'], 'unique', 'targetAttribute' => ['season_id', 'rule_id', 'range_id']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
            [['range_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatWeapon3XUsageRange::class, 'targetAttribute' => ['range_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'rule_id' => 'Rule ID',
            'range_id' => 'Range ID',
            'players' => 'Players',
            'key_players' => 'Key Players',
            'key_avg_gp' => 'Key Avg Gp',
            'key_sd_gp' => 'Key Sd Gp',
            '_players' => 'Players',
            '_avg_gp' => 'Avg Gp',
            '_sd_gp' => 'Sd Gp',
        ];
    }

    public function getRange(): ActiveQuery
    {
        return $this->hasOne(StatWeapon3XUsageRange::class, ['id' => 'range_id']);
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
