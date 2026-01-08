<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Override;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stat_special_use_count3".
 *
 * @property integer $season_id
 * @property integer $special_id
 * @property integer $rule_id
 * @property integer $use_count
 * @property integer $sample_size
 * @property integer $win
 *
 * @property Rule3 $rule
 * @property Season3 $season
 * @property Special3 $special
 */
class StatSpecialUseCount3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_special_use_count3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['season_id', 'special_id', 'rule_id', 'use_count', 'sample_size', 'win'], 'required'],
            [['season_id', 'special_id', 'rule_id', 'use_count', 'sample_size', 'win'], 'default', 'value' => null],
            [['season_id', 'special_id', 'rule_id', 'use_count', 'sample_size', 'win'], 'integer'],
            [['season_id', 'special_id', 'rule_id', 'use_count'], 'unique', 'targetAttribute' => ['season_id', 'special_id', 'rule_id', 'use_count']],
            [['rule_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rule3::class, 'targetAttribute' => ['rule_id' => 'id']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
            [['special_id'], 'exist', 'skipOnError' => true, 'targetClass' => Special3::class, 'targetAttribute' => ['special_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'special_id' => 'Special ID',
            'rule_id' => 'Rule ID',
            'use_count' => 'Use Count',
            'sample_size' => 'Sample Size',
            'win' => 'Win',
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

    public function getSpecial(): ActiveQuery
    {
        return $this->hasOne(Special3::class, ['id' => 'special_id']);
    }
}
