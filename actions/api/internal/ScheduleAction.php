<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\actions\api\internal\schedule\Splatoon2;
use app\actions\api\internal\schedule\Splatoon3;
use app\assets\GameVersionIconAsset;
use app\components\helpers\Battle as BattleHelper;
use yii\base\Action;
use yii\helpers\Url;

use function time;

final class ScheduleAction extends Action
{
    use Splatoon2;
    use Splatoon3;

    private DateTimeImmutable $now;
    private int $currentPeriod;

    public function init()
    {
        parent::init();

        $t = (int)($_SERVER['REQUEST_TIME'] ?? time());
        $this->now = (new DateTimeImmutable())
            ->setTimestamp($t)
            ->setTimezone(new DateTimeZone('Etc/UTC'));
        $this->currentPeriod = BattleHelper::calcPeriod2($t);
    }

    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = YII_ENV_PROD ? 'compact-json' : 'json';

        return [
            'time' => $this->now->getTimestamp(),
            'locale' => [
                'locale' => Yii::$app->language,
                'timezone' => Yii::$app->timeZone,
                'calendar' => Yii::$app->localeCalendar,
            ],
            'sources' => $this->getSources(),
            'games' => $this->getGames(),
            'splatoon2' => $this->getSplatoon2(),
            'splatoon3' => $this->getSplatoon3(),
            'translations' => $this->getTranslations(),
        ];
    }

    private function getTranslations(): array
    {
        return [
            'current_time' => Yii::t('app', 'Current Time:'),
            'heading' => Yii::t('app', 'Schedule'),
            'salmon_open' => Yii::t('app-salmon2', 'Open!'),
            'source' => Yii::t('app', 'Source: {source}'),
        ];
    }

    private function getSources(): array
    {
        return [
            's2ink' => [
                'url' => 'https://splatoon2.ink/',
                'name' => 'Splatoon2.ink',
            ],
            's3ink' => [
                'url' => 'https://splatoon3.ink/',
                'name' => 'Splatoon3.ink',
            ],
        ];
    }

    private function getGames(): array
    {
        $am = Yii::$app->assetManager;
        $asset = $am ? $am->getBundle(GameVersionIconAsset::class) : null;

        return [
            'splatoon1' => [
                'name' => Yii::t('app', 'Splatoon'),
                'icon' => $asset ? Url::to($am->getAssetUrl($asset, 's1.png'), true) : null,
            ],
            'splatoon2' => [
                'name' => Yii::t('app', 'Splatoon 2'),
                'icon' => $asset ? Url::to($am->getAssetUrl($asset, 's2.png'), true) : null,
            ],
            'splatoon3' => [
                'name' => Yii::t('app', 'Splatoon 3'),
                'icon' => $asset ? Url::to($am->getAssetUrl($asset, 's3.png'), true) : null,
            ],
        ];
    }
}
