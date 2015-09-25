<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\components\helpers\db\Now;
use app\models\Battle;
use app\models\BattleNawabari;
use app\models\BattleGachi;
use app\models\GameMode;
use app\models\Map;
use app\models\Rank;
use app\models\Rule;
use app\models\User;
use app\models\Weapon;

class BattleFilterForm extends Model
{
    public $screen_name;

    public $rule;
    public $map;
    public $weapon;

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
                    return substr($this->weapon, 0, 1) !== '@';
                }],
            [['weapon'], 'validateWeaponType',
                'when' => function () {
                    return substr($this->weapon, 0, 1) === '@';
                }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'screen_name' => 'ログイン名',
            'rule' => 'ルール',
            'map' => 'マップ',
            'weapon' => 'ブキ',
        ];
    }

    public function validateGameMode($attr, $params)
    {
        $value = substr($this->$attr, 1);
        $isExist = !!GameMode::findOne(['key' => $value]);
        if (!$isExist) {
            $this->addError($attr, 'ルールの指定が正しくありません');
        }
    }

    public function validateWeaponType($attr, $params)
    {
        $value = substr($this->$attr, 1);
        $isExist = !!WeaponType::findOne(['key' => $value]);
        if (!$isExist) {
            $this->addError($attr, 'ブキの指定が正しくありません');
        }
    }
}
