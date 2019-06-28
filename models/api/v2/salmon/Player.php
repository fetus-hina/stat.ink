<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\api\v2\salmon;

use Yii;
use app\components\behaviors\AutoTrimAttributesBehavior;
use app\components\helpers\ApiInputFormatter;
use app\models\Salmon2;
use app\models\SalmonBoss2;
use app\models\SalmonMainWeapon2;
use app\models\SalmonPlayer2;
use app\models\SalmonPlayerBossKill2;
use app\models\SalmonPlayerSpecialUse2;
use app\models\SalmonPlayerWeapon2;
use app\models\SalmonSpecial2;
use app\models\Species2;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\validators\NumberValidator;

class Player extends Model
{
    public $is_me;
    public $splatnet_id;
    public $name;
    public $special;
    public $rescue;
    public $death;
    public $golden_egg_delivered;
    public $power_egg_collected;
    public $species;
    public $gender;
    public $special_uses;
    public $weapons;
    public $boss_kills;

    public function behaviors()
    {
        return [
            AutoTrimAttributesBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['is_me'], 'required'],
            [['splatnet_id'], 'string', 'max' => 16],
            [['name'], 'string', 'max' => 10],
            [['special', 'species', 'gender'], 'string'],
            [['is_me'], 'boolean', 'trueValue' => 'yes', 'falseValue' => 'no'],
            [['rescue', 'death', 'golden_egg_delivered', 'power_egg_collected'], 'integer', 'min' => 0],
            [['species'], 'in', 'range' => ['inkling', 'octoling']],
            [['gender'], 'in', 'range' => ['boy', 'girl']],
            [['special'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonSpecial2::class,
                'targetAttribute' => ['special' => 'key'],
            ],
            [['special_uses'], 'validateSpecialUses'],
            [['weapons'], 'validateWeapons'],
            [['boss_kills'], 'validateBossKills'],
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function validateSpecialUses(): void
    {
        // {{{
        if ($this->hasErrors('special_uses')) {
            return;
        }

        if ($this->special_uses === null || $this->special_uses === '') {
            $this->special_uses = null;
            return;
        }

        if (!is_array($this->special_uses)) {
            $this->addError('special_uses', 'special_uses should be an array');
            return;
        }

        if (!ArrayHelper::isIndexed($this->special_uses)) {
            $this->addError('special_uses', 'special_uses should be an array (not associative array)');
            return;
        }

        if (count($this->special_uses) > 3) {
            $this->addError('special_uses', 'too many special_uses');
            return;
        }

        $countValidator = Yii::createObject([
            'class' => NumberValidator::class,
            'integerOnly' => true,
            'min' => 0,
            'max' => 3,
        ]);
        for ($i = 0; $i < 3; ++$i) {
            $value = $this->special_uses[$i] ?? null;
            if ($value === null) {
                break;
            }

            $error = null;
            if (!$countValidator->validate($value, $error)) {
                $this->addError('special_uses', sprintf('%s: %s', $key, $error));
                continue;
            }
        }
        // }}}
    }

    public function validateWeapons(): void
    {
        // {{{
        if ($this->hasErrors('weapons')) {
            return;
        }

        if ($this->weapons === null || $this->weapons === '') {
            $this->weapons = null;
            return;
        }

        if (!is_array($this->weapons)) {
            $this->addError('weapons', 'weapons should be an array');
            return;
        }

        if (empty($this->weapons)) {
            $this->weapons = null;
            return;
        }

        if (count($this->weapons) > 3) {
            $this->addError('weapons', 'too many weapons');
            return;
        }

        for ($i = 0; $i < 3; ++$i) {
            $value = $this->weapons[$i] ?? null;
            if ($value === '' || $value === null) {
                break;
            }

            $model = SalmonMainWeapon2::findOne(['key' => $value]);
            if (!$model) {
                $this->addError('weapons', sprintf('unknown key "%s"', (string)$value));
                continue;
            }
        }
        // }}}
    }

    public function validateBossKills(): void
    {
        // {{{
        if ($this->hasErrors('boss_kills')) {
            return;
        }

        if ($this->boss_kills === null || $this->boss_kills === '') {
            $this->boss_kills = null;
            return;
        }

        if (!is_array($this->boss_kills)) {
            $this->addError('boss_kills', 'boss_kills should be an associative array');
            return;
        }

        if (empty($this->boss_kills)) {
            $this->boss_kills = null;
            return;
        }

        $countValidator = Yii::createObject([
            'class' => NumberValidator::class,
            'integerOnly' => true,
            'min' => 0,
        ]);
        foreach ($this->boss_kills as $key => $value) {
            $boss = SalmonBoss2::findOne(['key' => (string)$key]);
            if (!$boss) {
                $this->addError('boss_kills', sprintf('unknown key "%s"', (string)$key));
                continue;
            }

            $error = null;
            if (!$countValidator->validate($value, $error)) {
                $this->addError('boss_kills', sprintf('%s: %s', $key, $error));
                continue;
            }
        }
        // }}}
    }

    public function save(Salmon2 $work): bool
    {
        return Yii::$app->db->transactionEx(function () use ($work): bool {
            if (!$this->validate()) {
                return false;
            }

            if (!$player = $this->savePlayer($work)) {
                return false;
            }

            if (!$this->saveSpecialUses($player)) {
                return false;
            }

            if (!$this->saveWeapons($player)) {
                return false;
            }

            if (!$this->saveBossKills($player)) {
                return false;
            }

            return true;
        });
    }

    protected function savePlayer(Salmon2 $work): ?SalmonPlayer2
    {
        return Yii::$app->db->transactionEx(function () use ($work): ?SalmonPlayer2 {
            $fmt = Yii::createObject(['class' => ApiInputFormatter::class]);
            $model = Yii::createObject([
                'class' => SalmonPlayer2::class,
                'work_id' => $work->id,
                'is_me' => $this->is_me === 'yes',
                'splatnet_id' => $fmt->asString($this->splatnet_id),
                'name' => $fmt->asString($this->name),
                'special_id' => $fmt->asKeyId($this->special, SalmonSpecial2::class, 'key', 'splatnet'),
                'rescue' => $fmt->asInteger($this->rescue),
                'death' => $fmt->asInteger($this->death),
                'golden_egg_delivered' => $fmt->asInteger($this->golden_egg_delivered),
                'power_egg_collected' => $fmt->asInteger($this->power_egg_collected),
                'species_id' => $fmt->asKeyId($this->species, Species2::class),
                'gender_id' => (function () use ($fmt): ?int {
                    switch ($fmt->asString($this->gender)) {
                        case 'boy':
                            return 1;

                        case 'girl':
                            return 2;

                        default:
                            return null;
                    }
                })(),
            ]);
            return $model->save() ? $model : null;
        });
    }

    protected function saveSpecialUses(SalmonPlayer2 $player): bool
    {
        if (!$this->special_uses) {
            return true;
        }

        return Yii::$app->db->transactionEx(function () use ($player): bool {
            $fmt = Yii::createObject(['class' => ApiInputFormatter::class]);
            for ($i = 0; $i < 3; ++$i) {
                $data = $this->special_uses[$i] ?? null;
                if ($data === null) {
                    break;
                }

                $model = Yii::createObject([
                    'class' => SalmonPlayerSpecialUse2::class,
                    'player_id' => $player->id,
                    'wave' => $i + 1,
                    'count' => $fmt->asInteger($data),
                ]);
                if (!$model->save()) {
                    return false;
                }
            }

            return true;
        });
    }

    protected function saveWeapons(SalmonPlayer2 $player): bool
    {
        if (!$this->weapons) {
            return true;
        }

        return Yii::$app->db->transactionEx(function () use ($player): bool {
            $fmt = Yii::createObject(['class' => ApiInputFormatter::class]);
            for ($i = 0; $i < 3; ++$i) {
                $data = $this->weapons[$i] ?? null;
                if ($data === null) {
                    break;
                }

                $model = Yii::createObject([
                    'class' => SalmonPlayerWeapon2::class,
                    'player_id' => $player->id,
                    'wave' => $i + 1,
                    'weapon_id' => $fmt->asKeyId($data, SalmonMainWeapon2::class, 'key', 'splatnet'),
                ]);
                if (!$model->save()) {
                    return false;
                }
            }

            return true;
        });
    }

    protected function saveBossKills(SalmonPlayer2 $player): bool
    {
        if (!$this->boss_kills) {
            return true;
        }

        return Yii::$app->db->transactionEx(function () use ($player): bool {
            $fmt = Yii::createObject(['class' => ApiInputFormatter::class]);
            foreach ($this->boss_kills as $bossKey => $count) {
                if ($count < 1) {
                    continue;
                }

                $model = Yii::createObject([
                    'class' => SalmonPlayerBossKill2::class,
                    'player_id' => $player->id,
                    'boss_id' => $fmt->asKeyId($bossKey, SalmonBoss2::class),
                    'count' => (int)$count,
                ]);
                if (!$model->save()) {
                    return false;
                }
            }

            return true;
        });
    }
}
