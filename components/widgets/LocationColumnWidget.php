<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use GeoIp2\Model\City;
use Yii;
use app\components\helpers\Html;
use statink\yii2\jdenticon\Jdenticon;
use yii\base\Widget;

class LocationColumnWidget extends Widget
{
    public $geoip;
    private $cityInfo = false;

    public $remoteAddr;
    public $remoteAddrMasked;
    public $remoteHost;

    public function init()
    {
        parent::init();
        if (!$this->geoip) {
            $this->geoip = Yii::$app->geoip;
        }
    }

    public function run()
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderJdenticon(),
                $this->renderTexts(),
            ]),
            [
                'id' => $this->id,
                'class' => [
                    'd-flex',
                ],
            ]
        );
    }

    protected function renderJdenticon(): string
    {
        return Jdenticon::widget([
            'hash' => $this->getJdenticonHash(),
            'params' => [
                'style' => [
                    'width' => '2em',
                    'height' => '2em',
                    'flex' => '0 0 2em',
                ],
            ],
        ]);
    }

    protected function getJdenticonHash(): string
    {
        return hash(
            'sha256',
            $this->remoteAddrMasked
                ? $this->remoteAddrMasked
                : $this->remoteAddr
        );
    }

    protected function renderTexts(): string
    {
        return Html::tag(
            'div',
            implode('', array_map(
                fn (string $html): string => Html::tag('div', $html),
                array_filter([
                    $this->renderLocation(),
                    $this->renderIpAddress(),
                ])
            )),
            [
                'style' => [
                    'flex' => '1 1 auto',
                ],
            ]
        );
    }

    protected function renderLocation(): ?string
    {
        if (!$this->remoteAddr) {
            return null;
        }

        try {
            $city = $this->getCityInfo();
            if (!$city) {
                return null;
            }

            return implode(' ', array_filter([
                $this->renderLocationText($city),
                $this->renderLocationIcon($city),
            ]));
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function renderLocationText(City $city): ?string
    {
        $get = function ($obj): ?string {
            if (!$obj) {
                return null;
            }

            $lang = $this->geoip->lang;
            return $obj->names[$lang] ?? $obj->name;
        };

        return Html::encode(implode(', ', array_filter([
            $get($city->city),
            $get($city->mostSpecificSubdivision),
            $get($city->country),
        ])));
    }

    protected function renderLocationIcon(City $city): ?string
    {
        $country = $city->country;
        if ($country->isoCode === null) {
            return null;
        }

        return (string)FlagIcon::fg(strtolower($country->isoCode));
    }

    protected function renderIpAddress(): ?string
    {
        if (!$this->remoteAddr) {
            return null;
        }

        if ($this->remoteHost) {
            return Html::tag(
                'span',
                Html::encode(strtolower($this->remoteHost)),
                ['title' => $this->remoteAddr, 'class' => 'auto-tooltip']
            );
        }

        if (strpos($this->remoteAddr, ':') !== false && $this->remoteAddrMasked) {
            return Html::tag(
                'span',
                Html::encode(strtolower($this->remoteAddrMasked)),
                ['title' => $this->remoteAddr, 'class' => 'auto-tooltip']
            );
        }

        return Html::tag('span', Html::encode($this->remoteAddr));
    }

    private function getCityInfo(): ?City
    {
        if ($this->cityInfo === false) {
            try {
                $this->cityInfo = $this->geoip->city($this->remoteAddr);
            } catch (\Throwable $e) {
                $this->cityInfo = null;
            }
        }
        return $this->cityInfo;
    }
}
