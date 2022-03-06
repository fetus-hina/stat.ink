<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\grid;

use Yii;
use app\assets\KillRatioColumnAsset;
use yii\grid\DataColumn;
use yii\helpers\Json;

class KillRatioColumn extends DataColumn
{
    public static $idPrefix;
    public static $idCounter = 0;
    public $killRate = false;

    public function init()
    {
        parent::init();

        if (static::$idPrefix === null) {
            static::$idPrefix = sprintf('col-kr-%s-', substr(
                hash('sha256', uniqid(microtime(false), true)),
                0,
                8
            ));
        }

        $attribute = $this->killRate ? 'kill_rate' : 'kill_ratio';
        $cellClass = $this->killRate ? 'cell-kill-rate' : 'cell-kill-ratio';

        $this->label = $this->killRate ? Yii::t('app', 'Rate') : Yii::t('app', 'Ratio');
        $this->attribute = $attribute;
        $this->headerOptions = [
            'class' => [$cellClass, 'auto-tooltip'],
            'title' => $this->killRate
                ? Yii::t('app', 'Kill Rate')
                : Yii::t('app', 'Kill Ratio'),
        ];
        $this->contentOptions = function ($model) use ($cellClass): array {
            if ($model->kill_ratio === null) {
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
                    'kill-ratio' => $model->kill_ratio,
                ],
            ];
        };
        if ($this->killRate) {
            $this->format = ['percent', 2];
            $this->value = fn ($model): ?float => $model->kill_rate !== null
                    ? $model->kill_rate / 100.0
                    : null;
        } else {
            $this->format = ['decimal', 2];
        }
    }
}
