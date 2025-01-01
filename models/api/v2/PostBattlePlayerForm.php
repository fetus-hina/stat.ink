<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v2;

use app\components\behaviors\FixAttributesBehavior;
use app\components\behaviors\SplatnetNumberBehavior;
use app\components\behaviors\TrimAttributesBehavior;
use app\models\FestTitle;
use app\models\Rank2;
use app\models\Species2;
use app\models\Weapon2;
use yii\base\Model;

use function array_keys;

class PostBattlePlayerForm extends Model
{
    public $team;
    public $is_me;
    public $weapon;
    public $level;
    public $star_rank;
    public $rank;
    public $rank_in_team;
    public $kill;
    public $death;
    public $kill_or_assist;
    public $special;
    public $point;
    public $my_kill;
    public $name;
    public $species;
    public $gender;
    public $fest_title;
    public $splatnet_id;
    public $top_500;

    public function behaviors()
    {
        return [
            [
                'class' => TrimAttributesBehavior::class,
                'targets' => array_keys($this->attributes),
            ],
            [
                'class' => SplatnetNumberBehavior::class,
                'attribute' => 'weapon',
                'tableName' => '{{weapon2}}',
            ],
            [
                'class' => FixAttributesBehavior::class,
                'attributes' => [
                    'weapon' => [
                        'manueuver' => 'maneuver', // issue #221
                        'manueuver_collabo' => 'maneuver_collabo', // issue #221
                        'publo_hue' => 'pablo_hue', // issue #301
                    ],
                    'fest_title' => [
                        'friend' => 'fiend', // issue #44
                    ],
                    'species' => [
                        'inklings' => 'inkling',
                        'octolings' => 'octoling',
                    ],
                ],
            ],
        ];
    }

    public function rules()
    {
        return [
            [['team', 'is_me'], 'required'],
            [['team'], 'in', 'range' => [ 'my', 'his' ]],
            [['is_me'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            [['weapon'], 'exist',
                'targetClass' => Weapon2::class,
                'targetAttribute' => 'key',
            ],
            [['level'], 'integer', 'min' => 1, 'max' => 99],
            [['star_rank'], 'integer'],
            [['rank'], 'exist',
                'targetClass' => Rank2::class,
                'targetAttribute' => 'key',
            ],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['kill', 'death', 'my_kill'], 'integer', 'min' => 0],
            [['kill_or_assist', 'special'], 'integer', 'min' => 0],
            [['point'], 'integer', 'min' => 0],
            [['name'], 'string', 'max' => 10],
            [['species'], 'exist', 'skipOnError' => true,
                'targetClass' => Species2::class,
                'targetAttribute' => 'key',
            ],
            [['gender'], 'in', 'range' => ['boy', 'girl']],
            [['fest_title'], 'string'],
            [['fest_title'], 'exist', 'skipOnError' => true,
                'targetClass' => FestTitle::class,
                'targetAttribute' => 'key',
            ],
            [['splatnet_id'], 'string', 'max' => 16],
            [['top_500'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
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
