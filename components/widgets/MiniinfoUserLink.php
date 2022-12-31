<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\AppLinkAsset;
use app\assets\FontAwesomeAsset;
use statink\yii2\twitter\webintents\TwitterWebIntentsAsset;
use yii\base\Widget;
use yii\helpers\Html;

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
        FontAwesomeAsset::register($this->view);

        $id = $this->id;
        return Html::tag(
            'div',
            implode('', [
                $this->renderTwitter(),
                $this->renderNnid(),
                $this->renderSwitch(),
                $this->renderIkanakama2(),
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

    private function renderNnid(): ?string
    {
        return $this->renderData(
            $this->iconAsset->nnid,
            $this->user->nnid,
        );
    }

    private function renderSwitch(): ?string
    {
        if ($this->user->sw_friend_code == '') {
            return null;
        }

        return $this->renderData(
            $this->iconAsset->switch,
            sprintf(
                'SW-%s-%s-%s',
                substr($this->user->sw_friend_code, 0, 4),
                substr($this->user->sw_friend_code, 4, 4),
                substr($this->user->sw_friend_code, 8, 4),
            ),
        );
    }

    private function renderIkanakama2(): ?string
    {
        if ($this->user->ikanakama2 == '') {
            return null;
        }

        return $this->renderData(
            $this->iconAsset->ikanakama,
            Yii::t('app', 'Ika-Nakama'),
            sprintf('https://ikanakama.ink/users/%d', $this->user->ikanakama2),
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
