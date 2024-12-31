<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\api\v3\postBattle;

use Yii;
use app\components\behaviors\TrimAttributesBehavior;
use app\components\helpers\CriticalSection;
use app\components\helpers\GearConfiguration3Fingerprint;
use app\components\validators\KeyValidator;
use app\models\Ability3;
use app\models\GearConfiguration3;
use app\models\GearConfigurationSecondary3;
use yii\base\Model;
use yii\db\Connection;

use function array_keys;
use function array_map;
use function array_values;

final class GearForm extends Model
{
    public $primary_ability;
    public $secondary_abilities;

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
            [['primary_ability'], 'string'],
            [['primary_ability'], KeyValidator::class,
                'modelClass' => Ability3::class,
            ],

            [['secondary_abilities'], 'each',
                'message' => '{attribute} must be an array',
                'rule' => Yii::createObject([
                    'class' => KeyValidator::class,
                    'modelClass' => Ability3::class,
                ]),
            ],
        ];
    }

    public function save(): ?GearConfiguration3
    {
        if (!$this->validate()) {
            return null;
        }

        $fingerprint = $this->calcFingerprint();
        if (!$fingerprint) {
            return null;
        }

        return $this->findOrCreateConfiguration($fingerprint);
    }

    private function calcFingerprint(): ?string
    {
        return GearConfiguration3Fingerprint::calc(
            null,
            $this->primary_ability ? $this->findAbility($this->primary_ability) : null,
            $this->secondary_abilities
                ? array_map(
                    fn (?string $key): ?Ability3 => $this->findAbility($key),
                    array_values($this->secondary_abilities),
                )
                : [],
        );
    }

    private function findAbility(?string $key): ?Ability3
    {
        return $key === null
            ? null
            : Ability3::find()
                ->andWhere(['key' => $key])
                ->limit(1)
                ->one();
    }

    private function findOrCreateConfiguration(string $fingerprint): ?GearConfiguration3
    {
        $model = $this->findByFingerprint($fingerprint);
        if ($model) {
            return $model;
        }

        $lock = CriticalSection::lock(__METHOD__, 60);
        if (!$lock) {
            $this->addError('_system', 'Failed to get lock. System busy. Try again.');
            return null;
        }

        $model = $this->findByFingerprint($fingerprint);
        if ($model) {
            return $model;
        }

        return Yii::$app->db->transaction(
            function (Connection $db) use ($fingerprint): ?GearConfiguration3 {
                $model = Yii::createObject([
                    'class' => GearConfiguration3::class,
                    'fingerprint' => $fingerprint,
                    'ability_id' => ($ability = $this->findAbility($this->primary_ability))
                        ? $ability->id
                        : null,
                ]);
                if (!$model->save()) {
                    $this->addError('_system', 'Failed to store the gear configuration. Try again.');
                    $db->transaction->rollBack();
                    return null;
                }

                foreach ($this->secondary_abilities as $key) {
                    $model2 = Yii::createObject([
                        'class' => GearConfigurationSecondary3::class,
                        'config_id' => $model->id,
                        'ability_id' => ($ability = $this->findAbility($key))
                            ? $ability->id
                            : null,
                    ]);

                    if (!$model2->save()) {
                        $this->addError('_system', 'Failed to store the gear configuration. Try again.');
                        $db->transaction->rollBack();
                        return null;
                    }
                }

                return $model;
            },
        );
    }

    private function findByFingerprint(string $fingerprint): ?GearConfiguration3
    {
        return GearConfiguration3::find()
            ->andWhere(['fingerprint' => $fingerprint])
            ->limit(1)
            ->one();
    }
}
