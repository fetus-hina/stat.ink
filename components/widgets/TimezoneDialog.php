<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use DateTimeImmutable;
use DateTimeZone;
use Throwable;
use Yii;
use app\assets\TimezoneDialogAsset;
use app\models\Country;
use app\models\Timezone;
use app\models\TimezoneGroup;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

use function abs;
use function array_map;
use function array_merge;
use function array_slice;
use function floor;
use function implode;
use function preg_match;
use function preg_replace;
use function sprintf;
use function strtolower;
use function trim;
use function vsprintf;

final class TimezoneDialog extends Dialog
{
    /**
     * @inheritdoc
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->title = implode(' ', [
            Icon::timezone(),
            Html::encode(Yii::t('app', 'Time Zone')),
        ]);
        $this->titleFormat = 'raw';
        $this->hasClose = true;
        $this->body = $this->createBody();
        $this->wrapBody = false;
    }

    private function createBody(): string
    {
        $view = $this->view;
        if ($view instanceof View) {
            TimezoneDialogAsset::register($view);
            $view->registerJs(
                vsprintf('$(%s).timezoneDialog();', [
                    Json::encode(
                        vsprintf('#%s', [
                            $this->id,
                        ]),
                    ),
                ]),
            );
        }

        return Yii::$app->cache->getOrSet(
            [
                __METHOD__,
                Yii::$app->locale,
                Yii::$app->timeZone,
            ],
            fn (): string => Html::tag(
                'div',
                $this->currentTimezone() . $this->renderZoneGroups(),
                ['class' => 'list-group-flush'],
            ),
            86400,
        );
    }

    private function currentTimezone(): string
    {
        $tz = Timezone::find()
            ->andWhere(['identifier' => Yii::$app->timeZone])
            ->cache(86400)
            ->limit(1)
            ->one();
        if (!$tz) {
            return '';
        }

        return $this->renderTimezone($tz, true, true);
    }

    private function renderTimezone(Timezone $tz, bool $isCurrent, bool $renderGroup): string
    {
        // https://github.com/php/php-src/issues/10218
        try {
            new DateTimeZone($tz->identifier);
        } catch (Throwable $e) {
            return '';
        }

        if ($isCurrent) {
            return Html::tag(
                'div',
                $this->renderTimezoneDetail($tz, $renderGroup),
                [
                    'class' => 'list-group-item',
                    'style' => [
                        'color' => '#fff',
                        'background-color' => '#337ab7',
                    ],
                ],
            );
        } else {
            return Html::a(
                $this->renderTimezoneDetail($tz, $renderGroup),
                null,
                [
                    'class' => 'list-group-item timezone-change text-dark cursor-pointer',
                    'data' => [
                        'tz' => $tz->identifier,
                    ],
                ],
            );
        }
    }

    private function renderTimezoneDetail(Timezone $tz, bool $renderGroup): string
    {
        $flags = implode(' ', array_map(
            function (?Country $country): string {
                if (!$country) {
                    return (string)FlagIcon::fg('none');
                }

                return (string)FlagIcon::fg($country->key);
            },
            array_slice(array_merge($tz->countries, [null, null]), 0, 2), // always 2 elements
        ));

        $ret = '';
        if ($renderGroup) {
            $ret .= Html::tag(
                'div',
                implode(' ', [
                    Html::encode(Yii::t('app-tz', $tz->group->name)),
                    Icon::subCategory(),
                ]),
                ['class' => 'small'],
            );
        }
        $ret .= Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'span',
                    $flags . ' ' . Html::encode(Yii::t('app-tz', $tz->name)),
                ),
                Html::tag(
                    'span',
                    Html::encode($this->renderOffset($tz)),
                    ['class' => 'd-inline small'],
                ),
            ]),
            ['class' => 'd-flex justify-content-between'],
        );
        return $ret;
    }

    private function renderZoneGroups(): string
    {
        $ret = '';
        $currentTz = Yii::$app->timeZone;
        $groups = TimezoneGroup::find()
            ->with(['timezones', 'timezones.countries'])
            ->cache(86400)
            ->all();
        foreach ($groups as $group) {
            if ($group->timezones) {
                $ret .= $this->renderZoneGroupHeader($group);
                $ret .= Html::tag(
                    'div',
                    implode('', array_map(
                        fn (Timezone $tz): string => $this->renderTimezone(
                            $tz,
                            $currentTz === $tz->identifier,
                            false,
                        ),
                        $group->timezones,
                    )),
                    [
                        'class' => 'collapse',
                        'id' => sprintf(
                            'tzgroup-%s',
                            trim(preg_replace('/[^a-z]+/', '-', strtolower($group->name)), '-'),
                        ),
                    ],
                );
            }
        }
        return $ret;
    }

    private function renderZoneGroupHeader(TimezoneGroup $group): string
    {
        return Html::a(
            implode(' ', [
                Html::encode(Yii::t('app-tz', $group->name)),
                FA::fas('chevron-down')->fw()->__toString(),
            ]),
            null,
            [
                'class' => 'list-group-item d-flex justify-content-between cursor-pointer',
                'style' => [
                    'color' => '#fff',
                    'background-color' => '#868e96',
                    'font-size' => '75%',
                    'padding' => '8px 15px',
                ],
                'role' => 'button',
                'data' => [
                    'toggle' => 'collapse',
                    'target' => sprintf(
                        '#tzgroup-%s',
                        trim(preg_replace('/[^a-z]+/', '-', strtolower($group->name)), '-'),
                    ),
                ],
            ],
        );
    }

    private function renderOffset(Timezone $tz): string
    {
        $time = (new DateTimeImmutable())
            ->setTimestamp($_SERVER['REQUEST_TIME'])
            ->setTimezone(new DateTimeZone($tz->identifier));
        $offset = $time->getOffset();

        $isEast = $offset >= 0;
        $offset = abs($offset);

        // "JST" "PST" のような文字列
        // "+09" のような形で出てくる時があるので、その時はなかったことにする
        $textOffset = $time->format('T');
        if (preg_match('/^[+-][0-9]/', $textOffset)) {
            $textOffset = null;
        } else {
            $textOffset = "({$textOffset})";
        }

        return trim(
            vsprintf('%s%02d:%02d %s', [
                $isEast ? '+' : '-',
                (int)floor($offset / 3600),
                (int)floor(($offset % 3600) / 60),
                $textOffset,
            ]),
        );
    }

    protected function renderFooter(): string
    {
        $close = Html::tag(
            'button',
            implode(' ', [
                Icon::close(),
                Html::encode(Yii::t('app', 'Close')),
            ]),
            [
                'type' => 'button',
                'class' => 'btn btn-default',
                'data-dismiss' => 'modal',
            ],
        );
        return Html::tag(
            'div',
            Html::tag(
                'div',
                implode('', [
                    Html::tag(
                        'div',
                        implode('', [
                            Html::tag('div', implode('', [
                                Html::encode(Yii::t('app', 'Guessed by your IP:')),
                                ' ',
                                Html::tag(
                                    'span',
                                    Html::encode(Yii::t('app', 'Unknown')),
                                    [
                                        'class' => 'guessed-timezone',
                                        'data' => [
                                            'error' => Yii::t('app', 'Error'),
                                            'loading' => Yii::t('app', 'Loading...'),
                                            'tooltip' => Yii::t('app', 'GeoIP guessed {timezone}'),
                                            'unknown' => Yii::t('app', 'Unknown'),
                                        ],
                                    ],
                                ),
                                ' ',
                                Html::a(
                                    Icon::help(),
                                    'https://github.com/fetus-hina/stat.ink/wiki/Time-Zone-Detection',
                                    [
                                        'rel' => 'external',
                                        'target' => '_blank',
                                    ],
                                ),
                            ])),
                            MaxmindMessage::widget(),
                        ]),
                    ),
                    $close,
                ]),
                [
                    'class' => [
                        'd-flex',
                        'justify-content-between',
                        'align-items-center', // vertical middle
                    ],
                ],
            ),
            [
                'class' => [
                    'modal-footer',
                    'text-left-important',
                ],
            ],
        );
    }
}
