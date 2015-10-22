<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\base\Model;
use app\components\helpers\db\Now;
use app\models\Battle;
use app\models\GameMode;
use app\models\Lobby;
use app\models\Map;
use app\models\Rank;
use app\models\Rule;
use app\models\Special;
use app\models\Subweapon;
use app\models\User;
use app\models\Weapon;

class BattleFilterForm extends Model
{
    public $screen_name;

    public $lobby;
    public $rule;
    public $map;
    public $weapon;
    public $result;

    public function formName()
    {
        return 'filter';
    }

    public function rules()
    {
        return [
            [['screen_name'], 'exist',
                'targetClass' => User::className(),
                'targetAttribute' => 'screen_name'],
            [['lobby'], 'exist',
                'targetClass' => Lobby::className(),
                'targetAttribute' => 'key'],
            [['rule'], 'exist',
                'targetClass' => Rule::className(),
                'targetAttribute' => 'key',
                'when' => function () {
                    return substr($this->rule, 0, 1) !== '@';
                }],
            [['rule'], 'validateGameMode',
                'when' => function () {
                    return substr($this->rule, 0, 1) === '@';
                }],
            [['map'], 'exist',
                'targetClass' => Map::className(),
                'targetAttribute' => 'key'],
            [['weapon'], 'exist',
                'targetClass' => Weapon::className(),
                'targetAttribute' => 'key',
                'when' => function () {
                    return !in_array(substr($this->weapon, 0, 1), ['@', '+', '*'], true);
                }],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => WeaponType::className(),
                ],
                'when' => function () {
                    return substr($this->weapon, 0, 1) === '@';
                }],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => Subweapon::className(),
                ],
                'when' => function () {
                    return substr($this->weapon, 0, 1) === '+';
                }],
            [['weapon'], 'validateWeapon',
                'params' => [
                    'modelClass' => Special::className(),
                ],
                'when' => function () {
                    return substr($this->weapon, 0, 1) === '*';
                }],
            [['result'], 'boolean', 'trueValue' => 'win', 'falseValue' => 'lose'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'screen_name'   => Yii::t('app', 'Screen Name'),
            'lobby'         => Yii::t('app', 'Game Mode'),
            'rule'          => Yii::t('app', 'Rule'),
            'map'           => Yii::t('app', 'Map'),
            'weapon'        => Yii::t('app', 'Weapon'),
            'result'        => Yii::t('app', 'Result'),
        ];
    }

    public function validateGameMode($attr, $params)
    {
        $value = substr($this->$attr, 1);
        $isExist = !!GameMode::findOne(['key' => $value]);
        if (!$isExist) {
            $this->addError(
                $attr,
                Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => $this->getAttributeLabel($attr),
                ])
            );
        }
    }

    public function validateWeapon($attr, $params)
    {
        $value = substr($this->$attr, 1);
        $method = [$params['modelClass'], 'findOne'];
        $isExist = !!call_user_func($method, ['key' => $value]);
        if (!$isExist) {
            $this->addError(
                $attr,
                Yii::t('yii', '{attribute} is invalid.', [
                    'attribute' => $this->getAttributeLabel($attr),
                ])
            );
        }
    }
}
