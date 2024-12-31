<?php

/**
 * @copyright Copyright (C) 2019-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\kdWin;

use Yii;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\Html;

use function array_map;
use function implode;
use function range;
use function sprintf;
use function vsprintf;

class KDWinTable extends Widget
{
    public $data = [];
    public $limit = 16;
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

        return implode('', [
            Html::beginTag('table', ['class' => 'table table-bordered table-condensed rule-table']),
            $this->renderHeader(),
            $this->renderBody(),
            Html::endTag('table'),
        ]);
    }

    public function renderHeader(): string
    {
        return sprintf(
            '<thead><tr>%s%s</tr></thead>',
            Html::tag('th', Html::encode(vsprintf('%s＼%s', [
                Yii::t('app', 'd'),
                Yii::t('app', 'k'),
            ]))),
            implode('', array_map(
                fn (int $k): string => Html::tag(
                    'th',
                    Html::encode(implode('', [
                        $this->formatter->asInteger($k),
                        $k === $this->limit ? '+' : '',
                    ])),
                    [
                        'class' => [
                            'text-center',
                            'kdcell',
                        ],
                    ],
                ),
                range(0, $this->limit),
            )),
        );
    }

    public function renderBody(): string
    {
        return sprintf(
            '<tbody>%s</tbody>',
            implode('', array_map(
                fn (int $d): string => sprintf(
                    '<tr>%s%s</tr>',
                    Html::tag(
                        'th',
                        Html::encode(implode('', [
                            $this->formatter->asInteger($d),
                            $d === $this->limit ? '+' : '',
                        ])),
                        [
                            'class' => [
                                'text-center',
                                'kdcell',
                            ],
                        ],
                    ),
                    implode('', array_map(
                        fn (int $k): string => KDWinCell::widget([
                            'battles' => (int)($this->data[$k][$d]['battle'] ?? 0),
                            'win' => (int)($this->data[$k][$d]['win'] ?? 0),
                            'formatter' => $this->formatter,
                        ]),
                        range(0, $this->limit),
                    )),
                ),
                range(0, $this->limit),
            )),
        );
    }
}
