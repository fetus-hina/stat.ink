<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\alerts;

use Override;
use Yii;
use app\components\widgets\Alert;
use yii\helpers\Html;

use function array_map;
use function implode;

final class NSOIssue extends Alert
{
    #[Override]
    public function init()
    {
        parent::init();

        $this->body = $this->renderBody();
    }

    #[Override]
    protected function initOptions()
    {
        parent::initOptions();

        Html::addCssClass($this->options, ['alert-danger']);
    }

    private function renderBody(): string
    {
        // phpcs:disable
        $messages = [
            'Due to updates to Nintendo Switch Online (NSO), third-party applications are currently being affected.',
            'We have received reports that updating authentication credentials for s3s and s3si.ts is not possible, and these apps are currently unavailable.',
            'Under no circumstances should you contact Nintendo regarding this issue.',
        ];
        // phpcs:enable

        return implode(
            '',
            array_map(
                fn (string $message, int $index): string => Html::tag(
                    'p',
                    Html::encode(Yii::t('app-alert', $message)),
                    [
                        'class' => $index === count($messages) - 1 ? 'mb-0' : null,
                    ],
                ),
                array_values($messages),
                array_keys($messages),
            ),
        );
    }
}
