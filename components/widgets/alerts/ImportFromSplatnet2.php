<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\alerts;

use Yii;
use app\components\helpers\Html;
use app\components\widgets\Alert;

class ImportFromSplatnet2 extends Alert
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
                        'You can import automatically from SplatNet 2, use these apps: (USE AT YOUR OWN RISK)'
                        // phpcs:enable
                    )
                )
            ),
            Html::tag('ul', implode('', [
                Html::tag('li', Html::a(
                    Html::encode('SquidTracks'),
                    'https://github.com/hymm/squid-tracks/',
                    ['class' => 'alert-link']
                )),
                Html::tag('li', Html::a(
                    Html::encode('splatnet2statink'),
                    'https://github.com/frozenpandaman/splatnet2statink',
                    ['class' => 'alert-link']
                )),
            ])),
            Html::tag(
                'p',
                Html::encode(
                    Yii::t(
                        'app-alert',
                        // phpcs:disable
                        'We won\'t implement automatic importing to {appName} for security reasons.',
                        // phpcs:enable
                        ['appName' => Yii::$app->name]
                    )
                )
            ),
        ]);
    }
}
