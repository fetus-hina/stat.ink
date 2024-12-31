<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\grid;

use Yii;
use app\assets\KillRatioColumnAsset;
use yii\base\Model;
use yii\grid\DataColumn;
use yii\helpers\Json;

use function hash;
use function microtime;
use function sprintf;
use function substr;
use function uniqid;
use function vsprintf;

class KillRatioColumn extends DataColumn
{
    public static $idPrefix;
    public static $idCounter = 0;

    public bool $killRate = false;

    public function init()
    {
        parent::init();

        if (static::$idPrefix === null) {
            static::$idPrefix = sprintf('col-kr-%s-', substr(
                hash('sha256', uniqid(microtime(false), true)),
                0,
                8,
            ));
        }

        $cellClass = $this->killRate ? 'cell-kill-rate' : 'cell-kill-ratio';

        $this->label = $this->killRate ? Yii::t('app', 'Rate') : Yii::t('app', 'Ratio');
        $this->headerOptions = [
            'class' => [$cellClass, 'auto-tooltip'],
            'title' => $this->killRate
                ? Yii::t('app', 'Kill Rate')
                : Yii::t('app', 'Kill Ratio'),
        ];
        $this->contentOptions = function (Model $model) use ($cellClass): array {
            $killRatio = $this->getKillRatio($model);
            if ($killRatio === null) {
                return [
                    'class' => [
                        $cellClass,
                        'text-right',
                    ],
                ];
            }

            $view = Yii::$app->getView();
            KillRatioColumnAsset::register($view);
            $id = sprintf('%s%d', static::$idPrefix, ++static::$idCounter);
            $view->registerJs(vsprintf('jQuery(%s).killRatioColumn();', [
                Json::encode('#' . $id),
            ]));

            return [
                'id' => $id,
                'class' => [
                    $cellClass,
                    'text-right',
                ],
                'data' => [
                    'kill-ratio' => $killRatio,
                ],
            ];
        };
        if ($this->killRate) {
            $this->format = ['percent', 2];
            $this->value = fn (Model $model): ?float => $this->getKillRate($model);
        } else {
            $this->format = ['decimal', 2];
            $this->value = fn (Model $model): ?float => $this->getKillRatio($model);
        }
    }

    protected function getKillRatio(Model $model): ?float
    {
        return $model->kill_ratio !== null
            ? (float)$model->kill_ratio
            : null;
    }

    protected function getKillRate(Model $model): ?float
    {
        return $model->kill_rate !== null
            ? (float)$model->kill_rate / 100.0
            : null;
    }
}
