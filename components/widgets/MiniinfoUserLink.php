<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use app\assets\AppLinkAsset;
use statink\yii2\twitter\webintents\TwitterWebIntentsAsset;
use yii\base\Widget;
use yii\helpers\Html;

use function http_build_query;
use function implode;
use function sprintf;
use function trim;

class MiniinfoUserLink extends Widget
{
    public $user;
    private $iconAsset;

    public function init()
    {
        parent::init();

        $this->iconAsset = AppLinkAsset::register($this->view);
    }

    public function run(): string
    {
        $id = $this->id;
        return Html::tag(
            'div',
            implode('', [
                $this->renderTwitter(),
            ]),
            [
                'id' => $id,
                'class' => 'miniinfo-databox',
            ],
        );
    }

    private function renderTwitter(): ?string
    {
        if ($this->user->twitter == '') {
            return null;
        }

        TwitterWebIntentsAsset::register($this->view);
        return $this->renderData(
            Icon::twitter(),
            "@{$this->user->twitter}",
            sprintf(
                'https://twitter.com/intent/user?%s',
                http_build_query([
                    'screen_name' => $this->user->twitter,
                ], '', '&'),
            ),
        );
    }

    private function renderData(
        string $icon,
        $value,
        $link = null
    ): ?string {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        return Html::tag('div', implode('', [
            $icon,
            $link === null
                ? Html::encode($value)
                : Html::a(
                    Html::encode($value),
                    $link,
                    ['rel' => 'nofollow', 'target' => '_blank'],
                ),
        ]));
    }
}
