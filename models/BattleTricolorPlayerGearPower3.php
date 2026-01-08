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
 * This is the model class for table "battle_tricolor_player_gear_power3".
 *
 * @property integer $id
 * @property integer $player_id
 * @property integer $ability_id
 * @property integer $gear_power
 *
 * @property Ability3 $ability
 * @property BattleTricolorPlayer3 $player
 */
class BattleTricolorPlayerGearPower3 extends ActiveRecord
{
    public static function tableName()
    {
        return 'battle_tricolor_player_gear_power3';
    }

    #[Override]
    public function rules()
    {
        return [
            [['player_id', 'ability_id', 'gear_power'], 'required'],
            [['player_id', 'ability_id', 'gear_power'], 'default', 'value' => null],
            [['player_id', 'ability_id', 'gear_power'], 'integer'],
            [['player_id', 'ability_id'], 'unique', 'targetAttribute' => ['player_id', 'ability_id']],
            [['ability_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ability3::class, 'targetAttribute' => ['ability_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => BattleTricolorPlayer3::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    #[Override]
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'player_id' => 'Player ID',
            'ability_id' => 'Ability ID',
            'gear_power' => 'Gear Power',
        ];
    }

    public function getAbility(): ActiveQuery
    {
        return $this->hasOne(Ability3::class, ['id' => 'ability_id']);
    }

    public function getPlayer(): ActiveQuery
    {
        return $this->hasOne(BattleTricolorPlayer3::class, ['id' => 'player_id']);
    }
}
