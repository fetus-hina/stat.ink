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
use app\components\helpers\CriticalSection;
use app\components\validators\KeyValidator;
use app\models\Battle3;
use app\models\BattlePlayer3;
use app\models\SplashtagTitle3;
use app\models\Weapon3;
use app\models\Weapon3Alias;
use yii\base\Model;
use yii\helpers\Json;

final class PlayerForm extends Model
{
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
    public $disconnected;

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
            [['kill', 'assist', 'kill_or_assist', 'death', 'special'], 'integer',
                'min' => 0,
                'max' => 99,
            ],
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

    public function save(Battle3 $battle, bool $isOurTeam, bool $rewriteKillAssist): ?BattlePlayer3
    {
        if (!$this->validate()) {
            return null;
        }

        $model = Yii::createObject([
            'class' => BattlePlayer3::class,
            'battle_id' => $battle->id,
            'is_our_team' => $isOurTeam,
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
            'splashtag_title_id' => $this->splashtagTitle(self::strVal($this->splashtag_title)),
        ]);

        if (
            $rewriteKillAssist &&
            \is_int($model->kill) &&
            \is_int($model->assist)
        ) {
            $model->kill_or_assist = $model->kill;
            $model->kill = $model->kill_or_assist - $model->assist;
            if ($model->kill < 0) {
                $model->kill_or_assist = null;
                $model->kill = null;
                $model->assist = null;
            }
        }

        if (!$model->save()) {
            $this->addError('_system', \vsprintf('Failed to store new player info, info=%s', [
                \base64_encode(Json::encode($model->getFirstErrors())),
            ]));
            return null;
        }

        return $model;
    }

    private function splashtagTitle(?string $title): ?int
    {
        $title = \trim((string)$title);
        if ($title === '') {
            return null;
        }

        // Find with Double-checked locking pattern
        $model = SplashtagTitle3::findOne(['name' => $title]);
        if (!$model) {
            $lock = CriticalSection::lock(__METHOD__);
            try {
                $model = SplashtagTitle3::findOne(['name' => $title]);
                if (!$model) {
                    // Not registered. Create it!
                    $model = Yii::createObject([
                        'class' => SplashtagTitle3::class,
                        'name' => $title,
                    ]);
                    if (!$model->save()) {
                        return null;
                    }
                }
            } finally {
                unset($lock);
            }
        }

        return (int)$model->id;
    }

    private static function hashNumberVal($value): ?string
    {
        // もし3桁以下の数字だったら0埋めする
        $intVal = self::intVal($value);
        if ($intVal && $intVal < 1000) {
            return \sprintf('%04d', $intVal);
        }

        return self::strVal($value);
    }
}
