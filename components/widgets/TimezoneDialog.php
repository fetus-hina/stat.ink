<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\components\widgets;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\Country;
use app\models\Timezone;
use app\models\TimezoneGroup;
use hiqdev\assets\flagiconcss\FlagIconCssAsset;
use yii\helpers\Html;

class TimezoneDialog extends Dialog
{
    public function init()
    {
        parent::init();
        $this->title = implode(' ', [
            FA::far('clock')->fw(),
            Html::encode(Yii::t('app', 'Time Zone')),
        ]);
        $this->titleFormat = 'raw';
        $this->hasClose = true;
        $this->footer = Dialog::FOOTER_CLOSE;
        $this->body = $this->createBody();
        $this->wrapBody = false;
    }

    private function createBody(): string
    {
        $jsCode = implode("\n", [
            '$(\'#{ID} [data-toggle="collapse"]\').each(function(){',
            '  var $parent = $(this);',
            '  var $icon = $(".fa-chevron-down", $parent);',
            '  $($parent.data("target"))',
            '    .on("hidden.bs.collapse", function(){',
            '      $icon.removeClass("fa-chevron-up");',
            '      $icon.addClass("fa-chevron-down");',
            '    })',
            '    .on("shown.bs.collapse", function(){',
            '      $icon.removeClass("fa-chevron-down");',
            '      $icon.addClass("fa-chevron-up");',
            '    });',
            '});',
        ]);
        $this->view->registerJs(
            str_replace('{ID}', $this->id, $jsCode)
        );
        return Html::tag(
            'div',
            $this->currentTimezone() . $this->renderZoneGroups(),
            ['class' => 'list-group-flush']
        );
    }

    private function currentTimezone(): string
    {
        if (!$tz = Timezone::findOne(['identifier' => Yii::$app->timeZone])) {
            return '';
        }

        return $this->renderTimezone($tz, true, true);
    }

    private function renderTimezone(Timezone $tz, bool $isCurrent, bool $renderGroup): string
    {
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
                ]
            );
        } else {
            return Html::a(
                $this->renderTimezoneDetail($tz, $renderGroup),
                'javascript:;',
                [
                    'class' => 'list-group-item timezone-change text-dark',
                    'data' => [
                        'tz' => $tz->identifier,
                    ],
                ]
            );
        }
    }

    private function renderTimezoneDetail(Timezone $tz, bool $renderGroup): string
    {
        $flags = implode(' ', array_map(
            function (?Country $country): string {
                $flag = $country
                    ? Html::tag('span', '', ['class' => [
                        'flag-icon',
                        'flag-icon-' . $country->key,
                    ]])
                    : '';
                return FA::hack($flag)->fw()->__toString();
            },
            array_slice(array_merge($tz->countries, [null, null]), 0, 2) // always 2 elements
        ));

        $ret = '';
        if ($renderGroup) {
            $ret .= Html::tag(
                'div',
                sprintf(
                    '%s %s',
                    Html::encode(Yii::t('app-tz', $tz->group->name)),
                    FA::fas('angle-double-right')->fw()
                ),
                ['class' => 'small']
            );
        }
        $ret .= Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'span',
                    $flags . ' ' . Html::encode(Yii::t('app-tz', $tz->name))
                ),
                Html::tag(
                    'span',
                    Html::encode($this->renderOffset($tz)),
                    ['class' => 'd-inline small']
                ),
            ]),
            ['class' => 'd-flex justify-content-between']
        );
        return $ret;
    }

    private function renderZoneGroups(): string
    {
        $ret = '';
        $currentTz = Yii::$app->timeZone;
        $groups = TimezoneGroup::find()->with(['timezones', 'timezones.countries'])->all();
        foreach ($groups as $group) {
            if ($group->timezones) {
                $ret .= $this->renderZoneGroupHeader($group);
                $ret .= Html::tag(
                    'div',
                    implode('', array_map(
                        function (Timezone $tz) use ($currentTz): string {
                            return $this->renderTimezone(
                                $tz,
                                $currentTz === $tz->identifier,
                                false
                            );
                        },
                        $group->timezones
                    )),
                    [
                        'class' => 'collapse',
                        'id' => sprintf(
                            'tzgroup-%s',
                            trim(preg_replace('/[^a-z]+/', '-', strtolower($group->name)), '-')
                        ),
                    ]
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
            'javascript:;',
            [
                'class' => 'list-group-item d-flex justify-content-between',
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
                        trim(preg_replace('/[^a-z]+/', '-', strtolower($group->name)), '-')
                    ),
                ],
            ]
        );
    }

    private function renderOffset(Timezone $tz): string
    {
        $time = (new DateTimeImmutable())
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time())
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

        return trim(sprintf(
            '%s%02d:%02d %s',
            $isEast ? '+' : '-',
            floor($offset / 3600),
            floor(($offset % 3600) / 60),
            $textOffset
        ));
    }
}
