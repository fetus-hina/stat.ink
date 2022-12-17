<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 * @author eli <frozenpandaman@users.noreply.github.com>
 */

namespace app\models\api\v3\postBattle;

use Yii;
use app\components\behaviors\TrimAttributesBehavior;
use app\components\validators\KeyValidator;
use app\models\Battle3;
use app\models\BattlePlayer3;
use app\models\BattleTricolorPlayer3;
use app\models\Rule3;
use app\models\SplashtagTitle3;
use app\models\Weapon3;
use app\models\Weapon3Alias;
use yii\base\Model;
use yii\helpers\Json;

final class PlayerForm extends Model
{
    use SplashtagTrait;
    use TypeHelperTrait;

    public $me;
    public $rank_in_team;
    public $name;
    public $number;
    public $splashtag_title;
    public $weapon;
    public $inked;
    public $kill;
    public $assist;
    public $kill_or_assist;
    public $death;
    public $special;
    public $signal;
    public $gears;
    public $disconnected;
    public $crown;

    /**
     * @var GearsForm|null
     */
    private $gearsForm = null;

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
            [['me', 'disconnected', 'crown'], 'in', 'range' => ['yes', 'no', true, false]],
            [['rank_in_team'], 'integer', 'min' => 1, 'max' => 4],
            [['name'], 'string', 'min' => 1, 'max' => 10],

            // "number" is not an integer.
            // see #1099 and #1113
            [['number'], 'string', 'max' => 32],
            [['number'], 'match',
                'pattern' => '/^[0-9A-Za-z]+$/',
            ],

            [['splashtag_title'], 'string', 'max' => 255],
            [['weapon'], 'string'],
            [['weapon'], KeyValidator::class,
                'modelClass' => Weapon3::class,
                'aliasClass' => Weapon3Alias::class,
            ],
            [['inked'], 'integer', 'min' => 0],
            [['kill', 'assist', 'kill_or_assist', 'death', 'special', 'signal'], 'integer',
                'min' => 0,
                'max' => 99,
            ],

            [['gears'], 'validateGears'],
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

    public function save(
        Battle3 $battle,
        bool $isOurTeam,
        int $tricolorTeamNumber,
    ): BattlePlayer3|BattleTricolorPlayer3|null {
        if (!$this->validate()) {
            return null;
        }

        $isTricolor = self::isTricolor($battle);
        $model = Yii::createObject(
            \array_merge(
                [
                    'class' => $isTricolor ? BattleTricolorPlayer3::class : BattlePlayer3::class,
                    'battle_id' => $battle->id,
                    'is_me' => (bool)($isOurTeam && self::boolVal($this->me)),
                    'rank_in_team' => self::intVal($this->rank_in_team),
                    'name' => self::strVal($this->name),
                    'number' => self::hashNumberVal($this->number),
                    'weapon_id' => self::key2id($this->weapon, Weapon3::class, Weapon3Alias::class, 'weapon_id'),
                    'inked' => self::intVal($this->inked),
                    'kill' => self::intVal($this->kill),
                    'assist' => self::intVal($this->assist),
                    'kill_or_assist' => self::intVal($this->kill_or_assist),
                    'death' => self::intVal($this->death),
                    'special' => self::intVal($this->special),
                    'is_disconnected' => self::boolVal($this->disconnected),
                    'splashtag_title_id' => self::splashtagTitle($this->splashtag_title),
                    'headgear_id' => $this->gearConfiguration($this->gearsForm ? $this->gearsForm->headgearForm : null),
                    'clothing_id' => $this->gearConfiguration($this->gearsForm ? $this->gearsForm->clothingForm : null),
                    'shoes_id' => $this->gearConfiguration($this->gearsForm ? $this->gearsForm->shoesForm : null),
                    'is_crowned' => self::boolVal($this->crown),
                ],
                $isTricolor
                    ? [
                        'team' => $tricolorTeamNumber,
                        'signal' => self::intVal($this->signal),
                    ]
                    : [
                        'is_our_team' => $isOurTeam,
                    ],
            ),
        );

        if (!$model->save()) {
            $this->addError('_system', \vsprintf('Failed to store new player info, info=%s', [
                \base64_encode(Json::encode($model->getFirstErrors())),
            ]));
            return null;
        }

        return $model;
    }

    public function validateGears(string $attribute): void
    {
        if ($this->hasErrors($attribute)) {
            return;
        }

        $data = $this->$attribute;
        if ($data === null || $data === '') {
            return;
        }

        if (!is_array($data)) {
            $this->addError($attribute, 'Gears structure needed');
            return;
        }

        $form = Yii::createObject(GearsForm::class);
        $form->attributes = $this->$attribute;
        if ($form->validate()) {
            $this->gearsForm = $form;
            return;
        }

        foreach ($form->getErrors() as $key => $values) {
            foreach ($values as $value) {
                $this->addError($attribute, "{$key}::{$value}");
            }
        }
    }

    private function gearConfiguration(?GearForm $form): ?int
    {
        $model = $form ? $form->save() : null;
        return $model ? (int)$model->id : null;
    }

    private static function isTricolor(Battle3 $battle): bool
    {
        $tricolor = Rule3::find()
            ->andWhere(['key' => 'tricolor'])
            ->limit(1)
            ->one();

        return $tricolor &&
            $battle->rule_id !== null &&
            (int)$tricolor->id === (int)$battle->rule_id;
    }
}
