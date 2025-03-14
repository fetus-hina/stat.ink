<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

use function array_filter;
use function implode;

class UserIcon extends Widget
{
    public $user;
    public $options = [];

    public function run()
    {
        return implode('', [
            FallbackableImage::widget([
                'srcs' => array_filter(
                    [
                        $this->user->userIcon->url ?? null,
                        $this->user->jdenticonUrl,
                    ],
                    fn (?string $src): bool => $src !== null,
                ),
                'options' => $this->options,
            ]),
            Html::tag('meta', '', [
                'itemprop' => 'image',
                'content' => Url::to($this->user->iconUrl, true),
            ]),
        ]);
    }
}
