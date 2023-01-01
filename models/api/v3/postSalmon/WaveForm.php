<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v3\postSalmon;

use Yii;
use app\components\behaviors\TrimAttributesBehavior;
use app\components\validators\KeyValidator;
use app\components\validators\SalmonSpecialUse3Validator;
use app\models\Salmon3;
use app\models\SalmonEvent3;
use app\models\SalmonEvent3Alias;
use app\models\SalmonSpecialUse3;
use app\models\SalmonWaterLevel2;
use app\models\SalmonWave3;
use app\models\Special3;
use app\models\Special3Alias;
use app\models\api\v3\postBattle\TypeHelperTrait;
use yii\base\Model;

use function array_keys;
use function filter_var;
use function is_array;
use function is_int;

use const FILTER_VALIDATE_INT;

final class WaveForm extends Model
{
    use TypeHelperTrait;

    public $tide;
    public $event;
    public $golden_quota;
    public $golden_delivered;
    public $golden_appearances;
    public $special_uses;

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
            [['tide', 'event'], 'string'],
            [['golden_quota'], 'integer', 'min' => 1],
            [['golden_delivered', 'golden_appearances'], 'integer', 'min' => 0],

            [['tide'], KeyValidator::class,
                'modelClass' => SalmonWaterLevel2::class,
            ],

            [['event'], KeyValidator::class,
                'modelClass' => SalmonEvent3::class,
                'aliasClass' => SalmonEvent3Alias::class,
            ],

            [['special_uses'], SalmonSpecialUse3Validator::class],
        ];
    }

    public function save(Salmon3 $salmon, int $waveNumber): ?SalmonWave3
    {
        $model = Yii::createObject([
            'class' => SalmonWave3::class,
            'salmon_id' => $salmon->id,
            'wave' => $waveNumber,
            'tide_id' => self::key2id($this->tide, SalmonWaterLevel2::class),
            'event_id' => self::key2id(
                $this->event,
                SalmonEvent3::class,
                SalmonEvent3Alias::class,
                'event_id',
            ),
            'golden_quota' => $this->golden_quota,
            'golden_delivered' => $this->golden_delivered,
            'golden_appearances' => $this->golden_appearances,
        ]);

        if (!$model->save()) {
            return null;
        }

        if ($this->special_uses && is_array($this->special_uses)) {
            foreach ($this->special_uses as $spKey => $spCount) {
                $special = self::key2id($spKey, Special3::class, Special3Alias::class, 'special_id');
                if ($special) {
                    $spCountN = filter_var($spCount, FILTER_VALIDATE_INT);
                    if (is_int($spCountN) && $spCountN > 0) {
                        $model2 = Yii::createObject([
                            'class' => SalmonSpecialUse3::class,
                            'wave_id' => $model->id,
                            'special_id' => $special,
                            'count' => $spCountN,
                        ]);
                        if (!$model2->save()) {
                            return null;
                        }
                    }
                }
            }
        }

        return $model;
    }
}
