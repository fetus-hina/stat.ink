<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\widgets\alerts;

use Override;
use Yii;
use app\components\widgets\Alert;
use yii\helpers\Html;

use function array_keys;
use function array_map;
use function array_values;
use function count;
use function implode;

final class AccessRestriction extends Alert
{
    /**
     * @inheritdoc
     *
     * Builds the alert body so that this widget can be used without any configuration.
     */
    #[Override]
    public function init()
    {
        parent::init();

        $this->body = $this->renderBody();
    }

    /**
     * @inheritdoc
     *
     * Renders this alert as a warning, because the restriction is not a fatal issue for most users.
     */
    #[Override]
    protected function initOptions()
    {
        parent::initOptions();

        Html::addCssClass($this->options, ['alert-warning']);
    }

    /**
     * Renders the notice about the access restriction as a series of paragraphs.
     *
     * Each message is translated through the `app-alert` category, and only the last
     * paragraph drops its bottom margin to fit within the alert box.
     */
    private function renderBody(): string
    {
        // phpcs:disable
        $messages = [
            'Because the load on our server has reached a level that we can no longer overlook, we have begun restricting accesses that appear to be made by bots.',
            'We are blocking IP addresses that mainly belong to data centers.',
            'If you are intentionally making such accesses, please stop doing so. Moving to an address that is not blocked yet will not help, because it will be blocked as well.',
            'If you need to access this site programmatically, please do so slowly enough. Concurrent accesses are not allowed at all.',
            'If your address is blocked, you can still browse the site by passing the check provided by Cloudflare. We are sorry for the inconvenience.',
            'We do not intend to restrict posting battles through the API. If your posts are blocked, please contact the administrator.',
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
