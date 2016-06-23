<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models\api\v1;

use Yii;
use yii\base\Model;
use app\models\Rank;
use app\models\Weapon;

class PostBattlePlayerForm extends Model
{
    public $team;
    public $is_me;
    public $weapon;
    public $rank;
    public $level;
    public $rank_in_team;
    public $kill;
    public $death;
    public $point;
    public $my_kill;

    public function rules()
    {
        return [
            [['team', 'is_me'], 'required'],
            [['team'], 'in', 'range' => [ 'my', 'his' ]],
            [['is_me'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            [['weapon'], 'exist',
                'targetClass' =>  Weapon::className(),
                'targetAttribute' => 'key'],
            [['rank'], 'exist',
                'targetClass' => Rank::className(),
                'targetAttribute' => 'key'],
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
