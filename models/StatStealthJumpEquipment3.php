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
 * This is the model class for table "stat_stealth_jump_equipment3".
 *
 * @property integer $season_id
 * @property string $x_power
 * @property integer $players
 * @property integer $equipments
 *
 * @property Season3 $season
 */
class StatStealthJumpEquipment3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'stat_stealth_jump_equipment3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['season_id', 'x_power', 'players', 'equipments'], 'required'],
            [['season_id', 'players', 'equipments'], 'default', 'value' => null],
            [['season_id', 'players', 'equipments'], 'integer'],
            [['x_power'], 'number'],
            [['season_id', 'x_power'], 'unique', 'targetAttribute' => ['season_id', 'x_power']],
            [['season_id'], 'exist', 'skipOnError' => true, 'targetClass' => Season3::class, 'targetAttribute' => ['season_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'season_id' => 'Season ID',
            'x_power' => 'X Power',
            'players' => 'Players',
            'equipments' => 'Equipments',
        ];
    }

    public function getSeason(): ActiveQuery
    {
        return $this->hasOne(Season3::class, ['id' => 'season_id']);
    }
}
