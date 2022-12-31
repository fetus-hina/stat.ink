<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\alerts;

use Yii;
use app\components\widgets\Alert;
use yii\helpers\Html;

final class PleaseUseLatest extends Alert
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
            'Please update your client software and use always latest version.',
            'Incorrect data will be registered if you do not use the latest version.',
        ];
        // phpcs:enable

        return implode('', array_map(
            function (string $message): string {
                return Html::tag(
                    'p',
                    Html::encode(Yii::t('app-alert', $message)),
                );
            },
            $messages,
        ));
    }
}
