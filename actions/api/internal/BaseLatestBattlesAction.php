<?php

/**
 * @copyright Copyright (C) 2020-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\api\internal;

use Override;
use Yii;
use app\actions\api\internal\latestBattles\Battle1Formatter;
use app\actions\api\internal\latestBattles\Battle2Formatter;
use app\actions\api\internal\latestBattles\Battle3Formatter;
use app\actions\api\internal\latestBattles\Salmon2Formatter;
use app\actions\api\internal\latestBattles\Salmon3Formatter;
use app\assets\NoDependedAppAsset;
use app\models\Battle;
use app\models\Battle2;
use app\models\Battle3;
use app\models\Salmon2;
use app\models\Salmon3;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\ViewAction;

use function array_filter;
use function array_merge;
use function array_values;
use function preg_replace;

abstract class BaseLatestBattlesAction extends ViewAction
{
    use Battle1Formatter;
    use Battle2Formatter;
    use Battle3Formatter;
    use Salmon2Formatter;
    use Salmon3Formatter;

    /**
     * @return array<Battle|Battle2|Battle3|Salmon2|Salmon3>
     */
    abstract protected function fetchBattles(): array;

    abstract protected function getHeading(): string;

    protected function isPrecheckOK(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function run(): array
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

    /**
     * @return array<string, mixed>
     */
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
                    fn (string $format): array => [
                        'one' => preg_replace(
                            '/\b1\b/',
                            '{delta}',
                            Yii::t('app-reltime', $format, ['delta' => 1]),
                        ),
                        'many' => preg_replace(
                            '/\b42\b/',
                            '{delta}',
                            Yii::t('app-reltime', $format, ['delta' => 42]),
                        ),
                    ],
                ),
            ),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function getBattles(): array
    {
        return ArrayHelper::getColumn(
            $this->fetchBattles(),
            function ($battle): ?array {
                switch ($battle::class) {
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
            },
        )
            |> array_filter(...)
            |> array_values(...);
    }

    /**
     * @return array<string, string>
     */
    private function getImages(): array
    {
        $am = Yii::$app->assetManager;
        $bundle = $am->getBundle(NoDependedAppAsset::class, true);

        return [
            'noImage' => Url::to($am->getAssetUrl($bundle, 'no-image.png'), true),
        ];
    }
}
