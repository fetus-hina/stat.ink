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
use app\actions\api\internal\latestBattles\Battle1Formatter;
use app\actions\api\internal\latestBattles\Battle2Formatter;
use app\actions\api\internal\latestBattles\Battle3Formatter;
use app\actions\api\internal\latestBattles\Salmon2Formatter;
use app\actions\api\internal\latestBattles\Salmon3Formatter;
use app\assets\GameModeIconsAsset;
use app\assets\NoDependedAppAsset;
use app\assets\Spl3StageAsset;
use app\components\helpers\CombinedBattles;
use app\models\Battle2;
use app\models\Battle3;
use app\models\Battle;
use app\models\Salmon2;
use app\models\Salmon3;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\ViewAction;

use function array_filter;
use function strtotime;
use function time;

abstract class BaseLatestBattlesAction extends ViewAction
{
    use Battle1Formatter;
    use Battle2Formatter;
    use Battle3Formatter;
    use Salmon2Formatter;
    use Salmon3Formatter;

    private DateTimeImmutable $now;

    abstract protected function fetchBattles(): array;
    abstract protected function getHeading(): string;

    protected function isPrecheckOK(): bool
    {
        return true;
    }

    public function init()
    {
        parent::init();

        $t = (int)($_SERVER['REQUEST_TIME'] ?? time());
        $this->now = (new DateTimeImmutable())
            ->setTimestamp($t)
            ->setTimezone(new DateTimeZone('Etc/UTC'));
    }

    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'compact-json';

        if (!$this->isPrecheckOK()) {
            return [
                'battles' => [],
                'images' => (object)[],
                'translations' => (object)[],
                'user' => null,
            ];
        }

        return [
            'battles' => $this->getBattles(),
            'images' => $this->getImages(),
            'translations' => $this->getTranslations(),
            'user' => null,
        ];
    }

    private function getTranslations(): array
    {
        $reltimes = [
            'year' => '{delta} yr',
            'month' => '{delta} mo',
            'day' => '{delta} d',
            'hour' => '{delta} h',
            'minute' => '{delta} m',
            'second' => '{delta} s',
        ];

        return [
            'heading' => $this->getHeading(),
            'reltime' => array_merge(
                ['now' => Yii::t('app-reltime', 'now')],
                ArrayHelper::getColumn(
                    $reltimes,
                    function (string $format): array {
                        return [
                            'one' => preg_replace(
                                '/\b1\b/',
                                '{delta}',
                                Yii::t('app-reltime', $format, ['delta' => 1])
                            ),
                            'many' => preg_replace(
                                '/\b42\b/',
                                '{delta}',
                                Yii::t('app-reltime', $format, ['delta' => 42])
                            ),
                        ];
                    }
                )
            ),
        ];
    }

    private function getBattles(): array
    {
        return \array_values(
            \array_filter(
                ArrayHelper::getColumn(
                    $this->fetchBattles(),
                    function ($battle): ?array {
                        switch (\get_class($battle)) {
                            case Battle::class:
                                return $this->formatBattle1($battle);

                            case Battle2::class:
                                return $this->formatBattle2($battle);

                            case Battle3::class:
                                return $this->formatBattle3($battle);

                            case Salmon2::class:
                                return $this->formatSalmon2($battle);

                            case Salmon3::class:
                                return $this->formatSalmon3($battle);

                            default:
                                return null;
                        }
                    }
                ),
            ),
        );
    }

    private function getImages(): array
    {
        $am = Yii::$app->assetManager;
        $bundle = $am->getBundle(NoDependedAppAsset::class, true);

        return [
            'noImage' => Url::to($am->getAssetUrl($bundle, 'no-image.png'), true),
        ];
    }
}
