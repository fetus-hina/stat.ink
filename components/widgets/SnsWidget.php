<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use jp3cki\yii2\twitter\widget\TweetButton;
use rmrevin\yii\fontawesome\FontAwesome;
use app\assets\ClipboardJsAsset;

class SnsWidget extends Widget
{
    public static $autoIdPrefix = 'sns-';
    public $template;

    public $tweetButton;

    private $initialized = false;

    public function init()
    {
        if ($this->initialized) {
            return;
        }
        $this->initialized = true;

        parent::init();

        // <div id="{id}" class="sns">{tweet} {permalink}</div>
        $this->template = Html::tag('div', '{tweet} {permalink}', [
            'id' => '{id}',
            'class' => [
                'sns',
            ],
        ]);
        $this->tweetButton = Yii::createObject([
            'class' => TweetButton::class
        ]);
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
            'tweet' => function () {
                return $this->tweetButton->run();
            },
            'permalink' => function () {
                return $this->procPermaLink();
            },
        ];
        return preg_replace_callback(
            '/\{(\w+)\}/',
            function ($match) use ($replace) {
                if (isset($replace[$match[1]])) {
                    $value = $replace[$match[1]];
                    return is_callable($value) ? $value() : $value;
                }
                return $match[0];
            },
            $this->template
        );
    }

    protected function procPermaLink()
    {
        $id = $this->id . '-permalink';
        $this->view->registerCss(sprintf(
            '.label-permalink{%s}',
            Html::cssStyleFromArray([
                'cursor'            => 'pointer',
                'display'           => 'inline-block',
                'font-size'         => '11px',
                'font-weight'       => '500',
                'height'            => '20px',
                'padding'           => '5px 8px 1px 6px',
                'vertical-align'    => 'top',
            ])
        ));
        if ($this->looksClipboardWorks) {
            ClipboardJsAsset::register($this->view);
            $this->view->registerCss(sprintf(
                '.label-permalink:hover{%s}',
                Html::cssStyleFromArray([
                    'background-color'  => '#1b3a63',
                ])
            ));
            $this->view->registerJs(sprintf('jQuery("#%s").permaLink();', $id));
            return Html::tag(
                'span',
                sprintf(
                    '%s %s',
                    FontAwesome::icon('anchor')->tag('span')->render(),
                    Html::encode(Yii::t('app', 'Permalink'))
                ),
                [
                    'id' => $id,
                    'class' => [
                        'label',
                        'label-success',
                        'label-permalink',
                        'auto-tooltip',
                    ],
                    'title' => Yii::t('app', 'Click to copy'),
                ]
            );
        } else {
            $this->view->registerCss(sprintf(
                '.label-permalink{%s}',
                Html::cssStyleFromArray([
                    'cursor' => 'not-allowed',
                ])
            ));
            return Html::tag(
                'span',
                sprintf(
                    '%s %s',
                    FontAwesome::icon('anchor')->tag('span')->render(),
                    Html::encode(Yii::t('app', 'PermaLink'))
                ),
                [
                    'id' => $id,
                    'class' => [
                        'label',
                        'label-default',
                        'label-permalink',
                        'auto-tooltip',
                    ],
                    'title' => Yii::t('app', 'Your browser does not support this action.'),
                ]
            );
        }
    }

    public function getLooksClipboardWorks()
    {
        $ua = (string)Yii::$app->request->userAgent;

        if (preg_match('/iPhone|iP[ao]d/', $ua)) {
            return false;
        }

        if (strpos($ua, 'Safari/') !== false && strpos($ua, ' Chrome/') === false) {
            return false;
        }

        return true;
    }
}
