<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 * @author Yoshiyuki Kawashima <ykawashi7@gmail.com>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\PermalinkDialogAsset;
use app\components\widgets\Icon;
use jp3cki\yii2\twitter\widget\TweetButton;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

use const PATHINFO_EXTENSION;

final class SnsWidget extends Widget
{
    public static $autoIdPrefix = 'sns-';
    public $template;

    public $tweetButton;
    public $imageUrl;
    public $feedUrl;
    public $jsonUrl;

    private $initialized = false;

    public function init()
    {
        if ($this->initialized) {
            return;
        }
        $this->initialized = true;

        parent::init();

        $this->template = Html::tag(
            'div',
            \implode(' ', [
                '{tweet}',
                '{permalink}',
                '{image}',
                '{feed}',
                '{json}',
            ]),
            [
                'id' => '{id}',
                'class' => [
                    'sns',
                ],
            ],
        );
        $this->tweetButton = Yii::createObject([
            'class' => TweetButton::class,
        ]);

        $this->view->registerCss(
            \vsprintf('#%s .label{%s}', [
                $this->id,
                Html::cssStyleFromArray([
                    'cursor' => 'pointer',
                    'display' => 'inline-block',
                    'font-size' => '11px',
                    'font-weight' => '500',
                    'height' => '20px',
                    'padding' => '5px 8px 3px 6px',
                    'vertical-align' => 'top',
                ]),
            ]),
        );
    }

    public function __set($key, $value)
    {
        $this->init();
        if (\preg_match('/^tweet(.+)$/', $key, $match)) {
            $attr = \lcfirst($match[1]);
            $this->tweetButton->$attr = $value;
        } else {
            parent::__set($key, $value);
        }
    }

    public function run()
    {
        $replace = [
            'feed' => fn (): ?string => $this->procFeed(),
            'id' => $this->id,
            'image' => fn (): ?string => $this->procImage(),
            'json' => fn (): ?string => $this->procJson(),
            'permalink' => fn (): ?string => $this->procPermaLink(),
            'tweet' => fn (): ?string => $this->tweetButton->run(),
        ];
        return \preg_replace_callback(
            '/\{(\w+)\}/',
            function (array $match) use ($replace): string {
                if (isset($replace[$match[1]])) {
                    $value = $replace[$match[1]];
                    return (string)(\is_callable($value) ? $value() : $value);
                }

                return $match[0];
            },
            $this->template,
        );
    }

    protected function procPermaLink(): ?string
    {
        PermalinkDialogAsset::register($this->view);
        $id = $this->id . '-permalink';
        $this->view->registerCss(
            \vsprintf('body[data-theme="default"] .label-permalink:hover{%s}', [
                Html::cssStyleFromArray([
                    'background-color'  => '#1b3a63',
                ]),
            ]),
        );
        return Html::tag(
            'span',
            \implode(' ', [
                Icon::permalink(),
                Html::encode(Yii::t('app', 'Permalink')),
            ]),
            [
                'id' => $id,
                'class' => [
                    'auto-tooltip',
                    'label',
                    'label-permalink',
                    'label-success',
                ],
                'data' => [
                    'dialog-title' => Yii::t('app', 'Permalink'),
                    'dialog-hint' => Yii::t('app', 'Please copy this URL:'),
                ],
            ],
        );
    }

    protected function procFeed(): ?string
    {
        $id = $this->id . '-feed';
        if (!$this->feedUrl) {
            return null;
        }
        $this->view->registerCss(
            \vsprintf('.label-feed{%s}.label-feed[href]:hover{%s}', [
                Html::cssStyleFromArray([
                    'background-color'  => '#ff7010',
                ]),
                Html::cssStyleFromArray([
                    'background-color'  => '#dc5800',
                ]),
            ]),
        );
        return Html::tag(
            'a',
            Icon::feed(),
            [
                'id' => $id,
                'class' => [
                    'auto-tooltip',
                    'label',
                    'label-feed',
                    'label-warning',
                ],
                'href' => Url::to($this->feedUrl),
            ],
        );
    }

    protected function procJson(): ?string
    {
        $id = $this->id . '-json';
        if (!$this->jsonUrl) {
            return null;
        }
        return Html::tag(
            'a',
            Icon::apiJson(),
            [
                'class' => ['auto-tooltip', 'label', 'label-default'],
                'href' => Url::to($this->jsonUrl),
                'id' => $id,
                'rel' => 'nofollow',
                'target' => '_blank',
                'type' => 'application/json',
            ],
        );
    }

    protected function procImage(): ?string
    {
        $id = $this->id . '-image';
        if (!$this->imageUrl) {
            return null;
        }

        $imageUrl = Url::to($this->imageUrl);

        $contentType = match (is_string($imageUrl) ? \strtolower(\pathinfo($imageUrl, PATHINFO_EXTENSION)) : '') {
            'avif' => 'image/avif',
            'gif' => 'image/gif',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => null,
        };

        return Html::tag(
            'a',
            Icon::image(),
            [
                'class' => ['auto-tooltip', 'label', 'label-default'],
                'href' => Url::to($this->imageUrl),
                'id' => $id,
                'rel' => 'nofollow',
                'target' => '_blank',
                'type' => $contentType,
            ],
        );
    }
}
