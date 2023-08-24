<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\alerts;

use Yii;
use app\components\widgets\Alert;
use app\components\widgets\Icon;
use yii\helpers\Html;

use function implode;

final class ImportFromSplatnet extends Alert
{
    public function init()
    {
        parent::init();
        $this->body = $this->renderBody();
    }

    protected function initOptions()
    {
        parent::initOptions();
        Html::addCssClass($this->options, ['alert-warning']);
    }

    private function renderBody(): string
    {
        return implode('', [
            Html::tag(
                'p',
                Html::encode(
                    Yii::t(
                        'app-alert',
                        // phpcs:disable
                        'You can import automatically from SplatNet, use these apps: (USE AT YOUR OWN RISK)'
                        // phpcs:enable
                    ),
                ),
            ),
            Html::tag('ul', implode('', [
                Html::tag('li', implode('', [
                    implode(' ', [
                        Icon::splatoon3(),
                        Html::encode(Yii::t('app', 'Splatoon 3')),
                    ]),
                    Html::tag('ul', implode('', [
                        Html::tag('li', implode('', [
                            Html::a(
                                Html::encode('s3s'),
                                'https://github.com/frozenpandaman/s3s',
                                [
                                    'class' => 'alert-link',
                                    'rel' => 'noopener',
                                    'target' => '_blank',
                                ],
                            ),
                            ' ',
                            Html::tag(
                                'small',
                                Html::encode('(Python3)'),
                                ['class' => 'text-muted'],
                            ),
                        ])),
                        Html::tag('li', implode('', [
                            Html::a(
                                Html::encode('s3si.ts'),
                                'https://github.com/spacemeowx2/s3si.ts',
                                [
                                    'class' => 'alert-link',
                                    'rel' => 'noopener',
                                    'target' => '_blank',
                                ],
                            ),
                            ' ',
                            Html::tag(
                                'small',
                                Html::encode('(Deno: TypeScript)'),
                                ['class' => 'text-muted'],
                            ),
                        ])),
                    ])),
                ])),
                Html::tag('li', implode('', [
                    implode(' ', [
                        Icon::splatoon2(),
                        Html::encode(Yii::t('app', 'Splatoon 2')),
                    ]),
                    Html::tag('ul', implode('', [
                        Html::tag('li', Html::a(
                            Html::encode('splatnet2statink'),
                            'https://github.com/frozenpandaman/splatnet2statink',
                            [
                                'class' => 'alert-link',
                                'rel' => 'noopener',
                                'target' => '_blank',
                            ],
                        )),
                        Html::tag(
                            'li',
                            Html::tag(
                                'del',
                                Html::a(
                                    Html::encode('SquidTracks'),
                                    'https://github.com/hymm/squid-tracks/',
                                    [
                                        'class' => 'alert-link',
                                        'rel' => 'noopener',
                                        'target' => '_blank',
                                    ],
                                ),
                            ),
                        ),
                    ])),
                ])),
            ])),
            Html::tag(
                'p',
                Html::encode(
                    Yii::t(
                        'app-alert',
                        // phpcs:disable
                        'We won\'t implement automatic importing to {appName} for security reasons.',
                        // phpcs:enable
                        ['appName' => Yii::$app->name],
                    ),
                ),
            ),
        ]);
    }
}
