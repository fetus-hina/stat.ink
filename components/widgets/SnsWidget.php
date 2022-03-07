<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 * @author Yoshiyuki Kawashima <ykawashi7@gmail.com>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\PermalinkDialogAsset;
use app\components\helpers\Html;
use jp3cki\yii2\twitter\widget\TweetButton;
use yii\base\Widget;
use yii\helpers\Url;

class SnsWidget extends Widget
{
    public static $autoIdPrefix = 'sns-';
    public $template;

    public $tweetButton;
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

        // <div id="{id}" class="sns">{tweet} {permalink}</div>
        $this->template = Html::tag('div', '{tweet} {permalink} {feed} {json}', [
            'id' => '{id}',
            'class' => [
                'sns',
            ],
        ]);
        $this->tweetButton = Yii::createObject([
            'class' => TweetButton::class,
        ]);

        $this->view->registerCss(sprintf(
            '#%s .label{%s}',
            $this->id,
            Html::cssStyleFromArray([
                'cursor'            => 'pointer',
                'display'           => 'inline-block',
                'font-size'         => '11px',
                'font-weight'       => '500',
                'height'            => '20px',
                'padding'           => '5px 8px 3px 6px',
                'vertical-align'    => 'top',
            ])
        ));
    }

    public function __set($key, $value)
    {
        $this->init();
        if (preg_match('/^tweet(.+)$/', $key, $match)) {
            $attr = lcfirst($match[1]);
            $this->tweetButton->$attr = $value;
        } else {
            parent::__set($key, $value);
        }
    }

    public function run()
    {
        $replace = [
            'id' => $this->id,
            'tweet' => fn (): ?string => $this->tweetButton->run(),
            'permalink' => fn (): ?string => $this->procPermaLink(),
            'feed' => fn (): ?string => $this->procFeed(),
            'json' => fn (): ?string => $this->procJson(),
        ];
        return preg_replace_callback(
            '/\{(\w+)\}/',
            function (array $match) use ($replace): string {
                if (isset($replace[$match[1]])) {
                    $value = $replace[$match[1]];
                    return (string)(is_callable($value) ? $value() : $value);
                }
                return $match[0];
            },
            $this->template
        );
    }

    protected function procPermaLink(): ?string
    {
        PermalinkDialogAsset::register($this->view);
        $id = $this->id . '-permalink';
        $this->view->registerCss(sprintf(
            'body[data-theme="default"] .label-permalink:hover{%s}',
            Html::cssStyleFromArray([
                'background-color'  => '#1b3a63',
            ])
        ));
        return Html::tag(
            'span',
            implode(' ', [
                (string)FA::fas('anchor')->fw(),
                Html::encode(Yii::t('app', 'Permalink')),
            ]),
            [
                'id' => $id,
                'class' => [
                    'label',
                    'label-success',
                    'label-permalink',
                    'auto-tooltip',
                ],
                'data' => [
                    'dialog-title' => Yii::t('app', 'Permalink'),
                    'dialog-hint' => Yii::t('app', 'Please copy this URL:'),
                ],
            ]
        );
    }

    protected function procFeed(): ?string
    {
        $id = $this->id . '-feed';
        if (!$this->feedUrl) {
            return null;
        }
        $this->view->registerCss(sprintf(
            '.label-feed{%s}.label-feed[href]:hover{%s}',
            Html::cssStyleFromArray([
                'background-color'  => '#ff7010',
            ]),
            Html::cssStyleFromArray([
                'background-color'  => '#dc5800',
            ])
        ));
        return Html::tag(
            'a',
            (string)FA::fas('rss')->fw(),
            [
                'id' => $id,
                'class' => [
                    'label',
                    'label-warning',
                    'label-feed',
                    'auto-tooltip',
                ],
                'href' => Url::to($this->feedUrl),
            ]
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
            (string)FA::fas('code')->fw(),
            [
                'id' => $id,
                'class' => [
                    'label',
                    'label-default',
                    'auto-tooltip',
                ],
                'href' => Url::to($this->jsonUrl),
                'rel' => 'nofollow',
                'type' => 'application/json',
            ]
        );
    }
}
