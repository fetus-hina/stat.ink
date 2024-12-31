<?php

/**
 * @copyright Copyright (C) 2019-2024 AIZAWA Hina
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

use function preg_match;
use function sprintf;
use function time;
use function vsprintf;

class Theme extends Component
{
    private ?string $theme = null;
    public string $cookieName = 'theme';

    /** @return void */
    public function init()
    {
        parent::init();

        if (!$this->theme) {
            $this->loadFromCookie();
        }
    }

    public function setTheme(string $themeId): self
    {
        Yii::trace(sprintf('setTheme(%s)', Json::encode($themeId)), __METHOD__);

        if (!$this->isValidTheme($themeId)) {
            throw new InvalidConfigException("Unknown theme: {$themeId}");
        }

        $this->theme = $themeId;
        $this->sendCookie();
        return $this;
    }

    public function getTheme(): string
    {
        $value = $this->theme ?? 'default';
        Yii::trace(sprintf('getTheme() returns %s', Json::encode($value)), __METHOD__);
        return $value;
    }

    public function getIsDarkTheme(): bool
    {
        switch ($this->getTheme()) {
            case 'bootswatch-cyborg':
            case 'bootswatch-darkly':
            case 'bootswatch-slate':
                Yii::trace('getIsDarkTheme() returns true', __METHOD__);
                return true;

            default:
                Yii::trace('getIsDarkTheme() returns false', __METHOD__);
                return false;
        }
    }

    public function getIsLightTheme(): bool
    {
        $value = !$this->getIsDarkTheme();
        Yii::trace(sprintf('getIsLightTheme() returns %s', Json::encode($value)), __METHOD__);
        return $value;
    }

    public function isValidTheme(string $themeId): bool
    {
        if ($themeId === 'default' || $themeId === 'color-blind') {
            Yii::trace('isValidTheme() returns true', __METHOD__);
            return true;
        }

        if (preg_match('/^bootswatch-([a-z]+)$/', $themeId, $match)) {
            $value = BootswatchAsset::isValidTheme($match[1]);
            Yii::trace(
                vsprintf('isValidTheme() returns %s via BootswatchAsset::isValidTheme', [
                    Json::encode($value),
                ]),
                __METHOD__,
            );
            return $value;
        }

        Yii::trace('isValidTheme() returns false', __METHOD__);
        return false;
    }

    public function registerAssets(View $view): void
    {
        $themeId = $this->getTheme();

        $view->registerJs(
            vsprintf('window.colorLock = %s;', [
                Json::encode($themeId === 'color-blind'),
            ]),
            View::POS_HEAD,
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
            Yii::trace('No cookie name set', __METHOD__);
            return $this;
        }

        $theme = Yii::$app->request->cookies->getValue($this->cookieName);
        Yii::trace(sprintf('Theme %s loaded from cookie', Json::encode($theme)), __METHOD__);
        if (!$theme || !$this->isValidTheme($theme)) {
            $theme = 'default';
            Yii::trace('Fallback to default theme', __METHOD__);
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
            'expire' => $expires->getTimestamp(),
            'httpOnly' => true,
            'name' => $this->cookieName,
            'sameSite' => Cookie::SAME_SITE_LAX,
            'value' => $this->getTheme(),
        ]));

        return $this;
    }
}
