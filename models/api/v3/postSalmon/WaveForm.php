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
use app\models\Salmon3;
use app\models\SalmonEvent3;
use app\models\SalmonEvent3Alias;
use app\models\SalmonWaterLevel2;
use app\models\SalmonWave3;
use app\models\api\v3\postBattle\TypeHelperTrait;
use yii\base\Model;

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
                'targets' => \array_keys($this->attributes),
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

            // special_uses
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

        // TODO: special_uses

        return $model;
    }
}
