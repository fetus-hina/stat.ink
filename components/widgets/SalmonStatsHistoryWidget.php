<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\SalmonStatsHistoryAsset;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

class SalmonStatsHistoryWidget extends Widget
{
    public $user;
    public $html;

    public function init()
    {
        parent::init();
        if (!$this->html) {
            $this->html = $this->loadDefaultTemplate();
        }
    }

    public function run()
    {
        SalmonStatsHistoryAsset::register($this->view);
        $this->view->registerJs(vsprintf('$(%s).salmonStatsHistoryDialog();', [
            Json::encode('#' . $this->id),
        ]));

        return preg_replace_callback(
            '/\{(\w+)\}/',
            function (array $match): string {
                switch ($match[1]) {
                    case 'id':
                        return Html::encode($this->id);

                    case 'apiUrl':
                        return Html::encode(
                            Url::to(
                                ['api-internal/salmon-stats2',
                                    'screen_name' => $this->user->screen_name,
                                ],
                                true,
                            ),
                        );

                    case 'close':
                        return Yii::t('app', 'Close');

                    case 'closeIcon':
                        return (string)FA::fas('times');

                    case 'title':
                        return Html::encode(Yii::t('app-salmon2', 'Grizzco Point Card'));

                    case 'tabs':
                        return $this->renderTabs();

                    case 'body':
                        return $this->renderBody();
                }

                return $match[0];
            },
            $this->html,
        );
    }

    protected function renderTabs(): string
    {
        $tabs = $this->getTabs();
        return Html::tag(
            'ul',
            implode('', array_map(
                function (string $key, string $short) {
                    return Html::tag(
                        'li',
                        Html::a(
                            Html::encode($short),
                            sprintf('#%s-%s', $this->id, $key),
                            ['data-toggle' => 'tab'],
                        ),
                        [
                            'role' => 'presentation',
                            'class' => array_filter([
                                $key === $this->getDefaultTab() ? 'active' : '',
                            ]),
                        ],
                    );
                },
                array_keys($tabs),
                ArrayHelper::getColumn($tabs, 'short'),
            )),
            [
                'class' => 'nav nav-tabs mb-2',
                'role' => 'tablist',
            ],
        );
    }

    protected function renderBody(): string
    {
        $tabs = $this->getTabs();
        return Html::tag(
            'div',
            implode('', array_map(
                function (string $key, array $options): string {
                    return Html::tag(
                        'div',
                        implode('', [
                            $options['total']
                                ? $this->renderBodyTotal($key, $options['api'])
                                : '',
                            $options['average']
                                ? $this->renderBodyAverage($key, $options['api'])
                                : '',
                        ]),
                        [
                            'id' => sprintf('%s-%s', $this->id, $key),
                            'role' => 'tabpanel',
                            'class' => array_filter([
                                'tab-pane',
                                $key === $this->getDefaultTab() ? 'active' : '',
                            ]),
                        ],
                    );
                },
                array_keys($tabs),
                array_values($tabs),
            )),
            ['class' => 'tab-content'],
        );
    }

    protected function renderBodyTotal(string $key, string $apiKey): string
    {
        return $this->renderBodyGraph(
            Yii::t('app-salmon-history2', 'Total'),
            $key,
            $apiKey,
            'total',
        );
    }

    protected function renderBodyAverage(string $key, string $apiKey): string
    {
        return $this->renderBodyGraph(
            Yii::t('app-salmon-history2', 'Average'),
            $key,
            $apiKey,
            'average',
        );
    }

    protected function renderBodyGraph(
        string $title,
        string $idKey,
        string $apiKey,
        string $typeKey
    ): string {
        return Html::tag(
            'div',
            implode('', [
                Html::tag('h4', Html::encode($title)),
                Html::tag('div', '', [
                    'class' => 'salmon-stats-history-graph',
                    'id' => sprintf('%s-%s-%s', $this->id, $idKey, $typeKey),
                    'data' => [
                        'type' => $typeKey,
                        'api' => $apiKey,
                    ],
                ]),
            ]),
            ['class' => 'mb-2'],
        );
    }

    protected function getDefaultTab(): string
    {
        return 'points';
    }

    protected function getTabs(): array
    {
        return [
            'shifts' => [
                'short' => Yii::t('app-salmon-history2', 'Shifts'),
                'api' => 'work_count',
                'total' => true,
                'average' => false,
            ],
            'points' => [
                'short' => Yii::t('app-salmon-history2', 'Points'),
                'api' => 'total_point',
                'total' => true,
                'average' => true,
            ],
            'golden' => [
                'short' => Yii::t('app-salmon-history2', 'Golden E.'),
                'api' => 'total_golden_eggs',
                'total' => true,
                'average' => true,
            ],
            'power' => [
                'short' => Yii::t('app-salmon-history2', 'Power E.'),
                'api' => 'total_eggs',
                'total' => true,
                'average' => true,
            ],
            'rescued' => [
                'short' => Yii::t('app-salmon-history2', 'Rescued'),
                'api' => 'total_rescued',
                'total' => true,
                'average' => true,
            ],
        ];
    }

    protected function loadDefaultTemplate(): string
    {
        if (!$fh = @fopen(__FILE__, 'rt')) {
            throw new ServerErrorHttpException();
        }
        try {
            fseek($fh, __COMPILER_HALT_OFFSET__, SEEK_SET);
            return trim(stream_get_contents($fh));
        } finally {
            fclose($fh);
        }
    }
}
// phpcs:disable
__halt_compiler();
<div id="{id}" class="modal fade" tabindex="-1" role="dialog" data-url="{apiUrl}">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="{close}">
          {closeIcon}
        </button>
        <h4 class="modal-title">
          {title}
        </h4>
      </div>
      <div class="modal-body">
        <nav>
          {tabs}
        </nav>
        {body}
      </div>
    </div>
  </div>
</div>
