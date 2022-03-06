<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\alerts;

use Yii;
use app\components\widgets\Alert;
use yii\helpers\Html;

class PleaseUseLatest extends Alert
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
        // phpcs:disable
        $messages = [
            'For SquidTracks or splatnet2statink users:',
            'Please update your client software and use always latest version (they will be updated to the latest version when restarted).',
            'Incorrect data will be registered if you do not use the latest version.',
        ];
        // phpcs:enable

        return implode('', array_map(
            fn (string $message): string => Html::tag(
                'p',
                Html::encode(Yii::t('app-alert', $message))
            ),
            $messages
        ));
    }
}
