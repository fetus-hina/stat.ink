<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\battle\item;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\assets\AppAsset;
use app\assets\BattleThumbListAsset;
use app\components\widgets\ActiveRelativeTimeWidget;
use app\models\User;
use statink\yii2\jdenticon\Jdenticon;
use yii\base\Widget;
use yii\bootstrap\Html;

abstract class BaseWidget extends Widget
{
    public $model;
    public static $img16x9;

    public function init()
    {
        parent::init();

        if (!static::$img16x9) {
            static::$img16x9 = sprintf(
                'data:%s;base64,%s',
                'image/png',
                base64_encode(file_get_contents(
                    implode(DIRECTORY_SEPARATOR, [
                        Yii::getAlias('@app'),
                        'resources',
                        'stat.ink',
                        '16x9.png',
                    ])
                ))
            );
            BattleThumbListAsset::register($this->view);
        }
    }

    abstract public function getBattleEndAt(): ?DateTimeImmutable;
    abstract public function getDescription(): string;
    abstract public function getHasBattleEndAt(): bool;
    abstract public function getImageUrl(): string;
    abstract public function getLinkRoute(): array;
    abstract public function getModeIcons(): array;
    abstract public function getRuleKey(): string;
    abstract public function getRuleName(): string;
    abstract public function getUser(): User;
    abstract public function getUserLinkRoute(): array;

    public function getImagePlaceholderUrl(): string
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

    public function getUserIconHtml(): string
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
            return Jdenticon::widget([
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
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);

            return Html::tag(
                'div',
                implode('', [
                    Html::tag(
                        'meta',
                        '',
                        [
                            'itemprop' => 'description',
                            'content' => $this->getDescription(),
                        ]
                    ),
                    Html::a(
                        implode('', [
                            Html::img(
                                static::$img16x9,
                                [
                                    'class' => ['battle-item-image', 'auto-tooltip'],
                                    'data' => [
                                        'src' => $this->getImageUrl(),
                                        'fallback' => $this->getImagePlaceholderUrl(),
                                    ],
                                    'title' => $this->getDescription(),
                                ]
                            ),
                            Html::tag(
                                'meta',
                                '',
                                [
                                    'itemprop' => 'url',
                                    'content' => $this->getImageUrl(),
                                ]
                            ),
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
                            Html::tag(
                                'span',
                                implode(' ', $this->getModeIcons()),
                                ['class' => [
                                    'mode-icons',
                                ]]
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
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }
}
