<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\kdWin;

use Yii;
use app\assets\EntireKDWinAsset;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\Html;

class KDWinCell extends Widget
{
    public $battles;
    public $win;
    public $formatter;

    public function init()
    {
        parent::init();
        if (!$this->formatter) {
            $this->formatter = Yii::$app->formatter;
        }
    }

    public function run()
    {
        BootstrapAsset::register($this->view);
        EntireKDWinAsset::register($this->view);

        return Html::tag(
            'td',
            implode('<br>', [
                Html::encode(sprintf(
                    '%s / %s',
                    $this->formatter->asInteger((int)$this->win),
                    $this->formatter->asInteger((int)$this->battles),
                )),
                Html::encode(
                    $this->battles > 0
                        ? $this->formatter->asPercent($this->win / $this->battles, 1)
                        : '-'
                ),
            ]),
            [
                'class' => [
                    'text-center',
                    'kdcell',
                    'percent-cell',
                ],
                'data' => [
                    'battle' => (string)(int)$this->battles,
                    'percent' => $this->battles > 0
                        ? ($this->win * 100 / $this->battles)
                        : '',
                ],
            ]
        );
    }
}
