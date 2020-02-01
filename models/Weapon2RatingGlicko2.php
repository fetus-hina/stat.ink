<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "weapon2_rating_glicko2".
 *
 * @property integer $rule_id
 * @property integer $weapon_id
 * @property integer $period
 * @property double $rating
 * @property double $deviation
 *
 * @property Rule2 $rule
 * @property Weapon2 $weapon
 */
class Weapon2RatingGlicko2 extends ActiveRecord
{
    public static function tableName()
    {
        return 'weapon2_rating_glicko2';
    }

    public function rules()
    {
        return [
            [['rule_id', 'weapon_id', 'period', 'rating', 'deviation'], 'required'],
            [['rule_id', 'weapon_id', 'period'], 'default', 'value' => null],
            [['rule_id', 'weapon_id', 'period'], 'integer'],
            [['rating', 'deviation'], 'number'],
            [['rule_id', 'weapon_id', 'period'], 'unique',
                'targetAttribute' => ['rule_id', 'weapon_id', 'period'],
            ],
            [['rule_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Rule2::class,
                'targetAttribute' => ['rule_id' => 'id'],
            ],
            [['weapon_id'], 'exist',
                'skipOnError' => true,
                'targetClass' => Weapon2::class,
                'targetAttribute' => ['weapon_id' => 'id'],
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'rule_id' => 'Rule ID',
            'weapon_id' => 'Weapon ID',
            'period' => 'Period',
            'rating' => 'Rating',
            'deviation' => 'Deviation',
        ];
    }

    public function getRule(): ActiveQuery
    {
        return $this->hasOne(Rule2::class, ['id' => 'rule_id']);
    }

    public function getWeapon(): ActiveQuery
    {
        return $this->hasOne(Weapon2::class, ['id' => 'weapon_id']);
    }
}
