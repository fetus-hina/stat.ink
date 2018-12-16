<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\components\widgets;

use GeoIp2\Database\Reader as GeoDbReader;
use Yii;
use statink\yii2\jdenticon\Jdenticon;
use yii\base\Widget;
use yii\helpers\Html;

class LocationColumnWidget extends Widget
{
    public $dbCity = '@geoip/GeoLite2-City.mmdb';
    // public $dbCountry = '@geoip/GeoLite2-Country.mmdb';

    public $remoteAddr;
    public $remoteAddrMasked;
    public $remoteHost;

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
                function (string $html): string {
                    return Html::tag('div', $html);
                },
                array_filter([
                    $this->renderLocation(),
                    $this->renderIpAddress(),
                ]),
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

        $dbPath = Yii::getAlias($this->dbCity);
        if (!@file_exists($dbPath)) {
            return null;
        }

        try {
            $reader = new GeoDbReader($dbPath);
            $data = $reader->city($this->remoteAddr);

            $get = function ($obj): ?string {
                if (!$obj) {
                    return null;
                }

                $lang = static::getGeoIpLang();
                return isset($obj->names[$lang])
                    ? $obj->names[$lang]
                    : $obj->name;
            };

            return Html::encode(implode(', ', array_filter([
                $get($data->city),
                $get($data->mostSpecificSubdivision),
                $get($data->country),
            ])));
        } catch (\Exception $e) {
        }
        return null;
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

    private static function getGeoIpLang(): string
    {
        $lang = Yii::$app->language;
        switch (substr($lang, 0, 2)) {
            case 'de':
            case 'en':
            case 'es':
            case 'fr':
            case 'ja':
            case 'ru':
                return substr($lang, 0, 2);

            case 'zh':
                return ($lang === 'zh-CN')
                    ? 'zh-CN'
                    : 'en';

            case 'pt':
                return 'pt-BR';

            default:
                return 'en';
        }
    }
}
