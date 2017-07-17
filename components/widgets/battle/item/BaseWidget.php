<?php
/**
 * @copyright Copyright (C) 2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\widgets\battle\item;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\assets\AppAsset;
use app\assets\JqueryLazyloadAsset;
use app\components\widgets\JdenticonWidget;
use app\components\widgets\ActiveRelativeTimeWidget;
use app\models\User;
use yii\base\Widget;
use yii\bootstrap\Html;

abstract class BaseWidget extends Widget
{
    public $model;

    public function init()
    {
        parent::init();
    }

    abstract public function getBattleEndAt() : ?DateTimeImmutable;
    abstract public function getDescription() : string;
    abstract public function getHasBattleEndAt() : bool;
    abstract public function getImageUrl() : string;
    abstract public function getLinkRoute() : array;
    abstract public function getRuleKey() : string;
    abstract public function getRuleName() : string;
    abstract public function getUser() : User;
    abstract public function getUserLinkRoute() : array;

    public function getImagePlaceholderUrl() : string
    {
        // {{{
        static $ret;
        if (!$ret) {
            $assetMgr = Yii::$app->getAssetManager();
            $ret = $assetMgr->getAssetUrl(
                $assetMgr->getBundle(AppAsset::class),
                'no-image.png'
            );
        }
        return $ret;
        // }}}
    }

    public function getUserIconHtml() : string
    {
        // {{{
        $user = $this->getUser();
        $icon = $user->userIcon;
        if ($icon) {
            return Html::img(
                $icon->url,
                [
                    'width' => 46,
                    'height' => 46,
                    'itemprop' => 'image',
                ]
            );
        } else {
            return JdenticonWidget::widget([
                'hash' => $user->identiconHash,
                'class' => 'identicon',
                'size' => 48,
                'schema' => 'image',
            ]);
        }
        // }}}
    }

    public function run()
    {
        JqueryLazyloadAsset::register($this->view);
        $this->view->registerJs("!function(\$){\$('img.lazyload').lazyload()}(jQuery);");

        return Html::tag(
            'div',
            implode('', [
                Html::tag('meta', '', ['itemprop' => 'description', 'content' => $this->getDescription()]),
                Html::a(
                    implode('', [
                        Html::img(
                            $this->getImagePlaceholderUrl(),
                            [
                                'class' => ['lazyload', 'auto-tooltip'],
                                'data' => [
                                    'original' => $this->getImageUrl(),
                                ],
                                'title' => $this->getDescription(),
                            ]
                        ),
                        Html::tag('meta', '', ['itemprop' => 'url', 'content' => $this->getImageUrl()]),
                    ]),
                    $this->getLinkRoute(),
                    ['itemprop' => 'url']
                ),
                Html::tag(
                    'div',
                    implode('', [
                        Html::tag(
                            'div',
                            Html::a(
                                implode('', [
                                    Html::tag(
                                        'span',
                                        $this->getUserIconHtml(),
                                        ['class' => 'thumblist-user-icon']
                                    ),
                                    Html::tag(
                                        'span',
                                        Html::encode($this->getUser()->name),
                                        ['itemprop' => 'name', 'class' => 'thumblist-user-name']
                                    ),
                                ]),
                                $this->getUserLinkRoute(),
                                ['itemprop' => 'url']
                            ),
                            [
                                'class' => 'caption-line',
                                'itemprop' => 'author',
                                'itemscope' => true,
                                'itemtype' => 'http://schema.org/Person',
                            ]
                        ),
                        $this->getHasBattleEndAt()
                            ? Html::a(
                                Html::tag(
                                    'time',
                                    ActiveRelativeTimeWidget::widget([
                                        'datetime' => $this->getBattleEndAt(),
                                        'mode' => 'short',
                                    ]),
                                    ['datetime' => $this->getBattleEndAt()->format(\DateTime::ATOM)]
                                ),
                                $this->getLinkRoute(),
                                [
                                    'title' => Yii::$app->getFormatter()->asDatetime(
                                        $this->getBattleEndAt(),
                                        'medium'
                                    ),
                                    'class' => ['auto-tooltip', 'thumblist-time'],
                                ]
                            )
                            : '',
                    ]),
                    ['class' => 'caption']
                ),
            ]),
            [
                'itemscope' => true,
                'itemtype' => 'http://schema.org/VideoGameClip',
                'class' => [
                    'thumbnail',
                    'thumbnail-' . $this->getRuleKey(),
                ],
            ]
        );
    }
}
