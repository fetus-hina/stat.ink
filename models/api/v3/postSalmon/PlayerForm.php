<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v3\postSalmon;

use Yii;
use app\components\behaviors\TrimAttributesBehavior;
use app\components\validators\ArrayValidator;
use app\components\validators\KeyValidator;
use app\models\Salmon3;
use app\models\SalmonPlayer3;
use app\models\SalmonPlayerWeapon3;
use app\models\SalmonUniform3;
use app\models\SalmonUniform3Alias;
use app\models\SalmonWeapon3;
use app\models\SalmonWeapon3Alias;
use app\models\Special3;
use app\models\Special3Alias;
use app\models\Species3;
use app\models\api\v3\postBattle\SplashtagTrait;
use app\models\api\v3\postBattle\TypeHelperTrait;
use yii\base\Model;

use function array_keys;
use function array_values;
use function is_array;

final class PlayerForm extends Model
{
    use SplashtagTrait;
    use TypeHelperTrait;

    public $me;
    public $name;
    public $number;
    public $splashtag_title;
    public $uniform;
    public $special;
    public $weapons;
    public $golden_eggs;
    public $golden_assist;
    public $power_eggs;
    public $rescue;
    public $rescued;
    public $defeat_boss;
    public $disconnected;
    public $species;

    public function behaviors()
    {
        return [
            [
                'class' => TrimAttributesBehavior::class,
                'targets' => array_keys($this->attributes),
            ],
        ];
    }

    public function rules()
    {
        return [
            [['me'], 'required'],
            [['me', 'disconnected'], 'in', 'range' => ['yes', 'no', true, false]],
            [['name'], 'string', 'min' => 1, 'max' => 10],

            // "number" is not an integer.
            // see #1099 and #1113
            [['number'], 'string', 'max' => 32],
            [['number'], 'match', 'pattern' => '/^[0-9A-Za-z]+$/'],
            [['splashtag_title'], 'string', 'max' => 255],

            [['uniform', 'special'], 'string'],
            [['golden_eggs', 'golden_assist', 'power_eggs'], 'integer', 'min' => 0],
            [['rescue', 'rescued', 'defeat_boss'], 'integer', 'min' => 0],

            [['uniform'], KeyValidator::class,
                'modelClass' => SalmonUniform3::class,
                'aliasClass' => SalmonUniform3Alias::class,
            ],
            [['special'], KeyValidator::class,
                'modelClass' => Special3::class,
                'aliasClass' => Special3Alias::class,
            ],
            [['weapons'], ArrayValidator::class,
                'rule' => [KeyValidator::class,
                    'modelClass' => SalmonWeapon3::class,
                    'aliasClass' => SalmonWeapon3Alias::class,
                ],
                'min' => 1,
                'max' => 5,
            ],
            [['species'], 'string'],
            [['species'], KeyValidator::class,
                'modelClass' => Species3::class,
            ],
        ];
    }

    public function save(Salmon3 $salmon): ?SalmonPlayer3
    {
        $model = Yii::createObject([
            'class' => SalmonPlayer3::class,
            'salmon_id' => $salmon->id,
            'is_me' => self::boolVal($this->me),
            'name' => self::strVal($this->name),
            'number' => self::hashNumberVal($this->number),
            'splashtag_title_id' => self::splashtagTitle($this->splashtag_title),
            'uniform_id' => self::key2id(
                $this->uniform,
                SalmonUniform3::class,
                SalmonUniform3Alias::class,
                'uniform_id',
            ),
            'special_id' => self::key2id(
                $this->special,
                Special3::class,
                Special3Alias::class,
                'special_id',
            ),
            'golden_eggs' => self::intVal($this->golden_eggs),
            'golden_assist' => self::intVal($this->golden_assist),
            'power_eggs' => self::intVal($this->power_eggs),
            'rescue' => self::intVal($this->rescue),
            'rescued' => self::intVal($this->rescued),
            'defeat_boss' => self::intVal($this->defeat_boss),
            'is_disconnected' => self::boolVal($this->disconnected),
            'species_id' => self::key2id($this->species, Species3::class),
        ]);

        if (!$model->save()) {
            return null;
        }

        if ($this->weapons && is_array($this->weapons)) {
            foreach (array_values($this->weapons) as $i => $weapon) {
                $model2 = Yii::createObject([
                    'class' => SalmonPlayerWeapon3::class,
                    'player_id' => $model->id,
                    'wave' => $i + 1,
                    'weapon_id' => self::key2id(
                        $weapon,
                        SalmonWeapon3::class,
                        SalmonWeapon3Alias::class,
                        'weapon_id',
                    ),
                ]);
                if (!$model2->save()) {
                    return null;
                }
            }
        }

        return $model;
    }
}
