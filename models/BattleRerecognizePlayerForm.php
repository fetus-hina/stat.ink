<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models;

use yii\base\Model;

/**
 * @property-read Weapon|null $weaponModel
 */
class BattleRerecognizePlayerForm extends Model
{
    /**
     * @var string|null
     */
    public $is_me;
    /**
     * @var string|null
     */
    public $team;
    /**
     * @var int|string|null
     */
    public $rank_in_team;
    /**
     * @var int|string|null
     */
    public $level;
    /**
     * @var string|null
     */
    public $rank;
    /**
     * @var string|null
     */
    public $weapon;
    /**
     * @var int|string|null
     */
    public $kill;
    /**
     * @var int|string|null
     */
    public $death;
    /**
     * @var int|string|null
     */
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
