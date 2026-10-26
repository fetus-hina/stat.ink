<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\api\v1;

use app\models\Rank;
use app\models\Weapon;
use yii\base\Model;

class PostBattlePlayerForm extends Model
{
    /** @var mixed */
    public $team;
    /** @var mixed */
    public $is_me;
    /** @var mixed */
    public $weapon;
    /** @var mixed */
    public $rank;
    /** @var mixed */
    public $level;
    /** @var mixed */
    public $rank_in_team;
    /** @var mixed */
    public $kill;
    /** @var mixed */
    public $death;
    /** @var mixed */
    public $point;
    /** @var mixed */
    public $my_kill;

    public function rules()
    {
        return [
            [['team', 'is_me'], 'required'],
            [['team'], 'in', 'range' => [ 'my', 'his' ]],
            [['is_me'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            [['weapon'], 'exist',
                'targetClass' => Weapon::class,
                'targetAttribute' => 'key',
            ],
            [['rank'], 'exist',
                'targetClass' => Rank::class,
                'targetAttribute' => 'key',
            ],
            [['level'], 'integer', 'min' => 1, 'max' => 50],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['kill', 'death', 'my_kill'], 'integer', 'min' => 0],
            [['point'], 'integer', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        ];
    }
}
