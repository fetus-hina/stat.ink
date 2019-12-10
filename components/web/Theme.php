<?php

/**
 * @copyright Copyright (C) 2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\web;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\assets\BootswatchAsset;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\Json;
use yii\web\Cookie;
use yii\web\View;

class Theme extends Component
{
    private $theme;
    public $cookieName = 'theme';

    public function init()
    {
        parent::init();

        if (!$this->theme) {
            $this->loadFromCookie();
        }
    }

    public function setTheme(string $themeId): self
    {
        if (!$this->isValidTheme($themeId)) {
            throw new InvalidConfigException("Unknown theme: {$themeId}");
        }

        $this->theme = $themeId;
        $this->sendCookie();
        return $this;
    }

    public function getTheme(): string
    {
        return $this->theme ?? 'default';
    }

    public function getIsDarkTheme(): bool
    {
        switch ($this->getTheme()) {
            case 'bootswatch-cyborg':
            case 'bootswatch-darkly':
            case 'bootswatch-slate':
                return true;

            default:
                return false;
        }
    }

    public function getIsLightTheme(): bool
    {
        return !$this->getIsDarkTheme();
    }

    public function isValidTheme(string $themeId): bool
    {
        if ($themeId === 'default' || $themeId === 'color-blind') {
            return true;
        }

        if (preg_match('/^bootswatch-([a-z]+)$/', $themeId, $match)) {
            return BootswatchAsset::isValidTheme($match[1]);
        }

        return false;
    }

    public function registerAssets(View $view): void
    {
        $themeId = $this->getTheme();

        $view->registerJs(
            vsprintf('window.colorLock = %s;', [
                Json::encode($themeId === 'color-blind'),
            ]),
            View::POS_HEAD
        );

        if (preg_match('/^bootswatch-([a-z]+)$/', $themeId, $match)) {
            BootswatchAsset::register($view)->setTheme($match[1]);
            return;
        }

        BootstrapAsset::register($view);
    }

    protected function loadFromCookie(): self
    {
        if (!$this->cookieName) {
            return $this;
        }

        $theme = Yii::$app->request->cookies->getValue($this->cookieName);
        if (!$theme || !$this->isValidTheme($theme)) {
            $theme = 'default';
        }

        return $this->setTheme($theme);
    }

    protected function sendCookie(): self
    {
        if (!$this->cookieName) {
            return $this;
        }

        $expires = (new DateTimeImmutable())
            ->setTimezone(new DateTimeZone('Etc/UTC'))
            ->setTimestamp(time())
            ->add(new DateInterval('P1Y1D'))
            ->setTime(0, 0, 0);

        Yii::$app->response->cookies->add(Yii::createObject([
            'class' => Cookie::class,
            'name' => $this->cookieName,
            'value' => $this->getTheme(),
            'expire' => $expires->getTimestamp(),
            'httpOnly' => true,
        ]));

        return $this;
    }
}
