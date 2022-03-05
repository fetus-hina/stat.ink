<?php

/**
 * @copyright Copyright (C) 2016-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use yii\base\Model;

/**
 * @property-read Weapon|null $weaponModel
 */
class BattleRerecognizePlayerForm extends Model
{
    public $is_me;
    public $team;
    public $rank_in_team;
    public $level;
    public $rank;
    public $weapon;
    public $kill;
    public $death;
    public $point;

    public function rules()
    {
        return [
            [['is_me', 'team', 'rank_in_team'], 'required'],
            [['is_me'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            [['team'], 'in', 'range' => ['my', 'his']],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['level'], 'integer', 'min' => 1, 'max' => 50],
            [['rank'], 'in', 'range' => ['c-', 'c', 'c+', 'b-', 'b', 'b+', 'a-', 'a', 'a+', 's', 's+']],
            [['weapon'], 'exist',
                'targetClass' => Weapon::class,
                'targetAttribute' => 'key',
            ],
            [['kill', 'death'], 'integer', 'min' => 0, 'max' => 99],
            [['point'], 'integer', 'min' => 0],
        ];
    }

    public function getWeaponModel(): ?Weapon
    {
        return $this->weapon
            ? Weapon::findOne(['key' => $this->weapon])
            : null;
    }
}
