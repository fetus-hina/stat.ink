<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\models\api\v2\salmon;

use Yii;
use app\components\behaviors\AutoTrimAttributesBehavior;
use app\models\SalmonMainWeapon2;
use app\models\Special2;
use yii\base\Model;

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
            [['species'], 'range', 'in' => ['inkling', 'octoling']],
            [['gender'], 'range', 'in' => ['boy', 'girl']],
            [['special'], 'exist', 'skipOnError' => true,
                'targetClass' => Special2::class,
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
}
