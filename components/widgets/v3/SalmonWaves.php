<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\assets\SalmonEggAsset;
use app\assets\SalmonWavesAsset;
use app\assets\Spl3WeaponAsset;
use app\components\i18n\Formatter;
use app\components\widgets\Label;
use app\models\Salmon3;
use app\models\SalmonEvent3;
use app\models\SalmonKing3;
use app\models\SalmonSpecialUse3;
use app\models\SalmonWaterLevel2;
use app\models\SalmonWave3;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\Progress;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\i18n\Formatter as BaseFormatter;
use yii\web\View;

final class SalmonWaves extends Widget
{
    public Salmon3 $job;

    public ?SalmonWave3 $wave1 = null;
    public ?SalmonWave3 $wave2 = null;
    public ?SalmonWave3 $wave3 = null;
    public ?SalmonWave3 $extra = null;

    public ?BaseFormatter $formatter = null;

    /**
     * @inheritdoc
     * @return void
     */
    public function init()
    {
        parent::init();

        if (!$this->formatter) {
            $this->formatter = Yii::createObject([
                'class' => Formatter::class,
                'nullDisplay' => '-',
            ]);
        }

        $view = $this->view;
        if ($view instanceof View) {
            BootstrapAsset::register($view);
            SalmonWavesAsset::register($view);
        }
    }

    public function run(): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'table',
                implode('', [
                    $this->renderHeader(),
                    $this->renderBody(),
                ]),
                [
                    'class' => [
                        'salmon-v3-waves',
                        'table',
                        'table-bordered',
                        'table-striped',
                    ],
                ]
            ),
            [
                'class' => 'table-responsive',
            ],
        );
    }

    private function renderHeader(): string
    {
        return Html::tag(
            'thead',
            Html::tag(
                'tr',
                implode('', [
                    Html::tag('th', ''),
                    Html::tag(
                        'th',
                        Html::encode(Yii::t('app-salmon2', 'Wave {waveNumber}', ['waveNumber' => 1])),
                        ['class' => 'text-center'],
                    ),
                    Html::tag(
                        'th',
                        Html::encode(Yii::t('app-salmon2', 'Wave {waveNumber}', ['waveNumber' => 2])),
                        ['class' => 'text-center'],
                    ),
                    Html::tag(
                        'th',
                        Html::encode(Yii::t('app-salmon2', 'Wave {waveNumber}', ['waveNumber' => 3])),
                        ['class' => 'text-center'],
                    ),
                    Html::tag(
                        'th',
                        Html::encode(Yii::t('app-salmon3', 'Xtrawave')),
                        ['class' => 'text-center'],
                    ),
                    Html::tag(
                        'th',
                        Html::encode(Yii::t('app', 'Total')),
                        ['class' => 'text-center'],
                    ),
                ]),
            ),
        );
    }

    private function renderBody(): string
    {
        $waves = [
            $this->makeData($this->wave1),
            $this->makeData($this->wave2),
            $this->makeData($this->wave3),
            $this->makeData($this->extra),
        ];

        return Html::tag(
            'tbody',
            \implode(
                '',
                \array_filter(
                    [
                        $this->renderResults($waves),
                        $this->renderEvents($waves),
                        $this->renderTides($waves),
                        $this->renderGoldenEggs($waves),
                        $this->renderGoldenAppearances($waves),
                        $this->renderSpecialUses($waves),
                    ],
                    fn (?string $html): bool => $html !== null,
                ),
            ),
        );
    }

    /**
     * @param array{result: ?bool}[] $waves
     */
    private function renderResults(array $waves): string
    {
        return Html::tag(
            'tr',
            \implode('', [
                Html::tag('th', Html::encode(Yii::t('app', 'Result'))),
                \implode('', ArrayHelper::getColumn(
                    $waves,
                    fn (array $wave): string => Html::tag(
                        'td',
                        ArrayHelper::getValue(
                            $wave,
                            function (array $wave): string {
                                if ($wave['result'] === null) {
                                    return Html::encode('-');
                                }

                                return $wave['result']
                                    ? Label::widget([
                                        'content' => Yii::t('app-salmon2', '✓'),
                                        'color' => 'success',
                                        'formatter' => $this->formatter,
                                    ])
                                    : Label::widget([
                                        'content' => Yii::t('app-salmon2', '✗'),
                                        'color' => 'danger',
                                        'formatter' => $this->formatter,
                                    ]);
                            },
                        ),
                        ['class' => 'text-center'],
                    ),
                )),
                Html::tag('td', ''),
            ]),
        );
    }

    /**
     * @param array{event: ?SalmonEvent3, king: ?SalmonKing3}[] $waves
     */
    private function renderEvents(array $waves): string
    {
        return Html::tag(
            'tr',
            \implode('', [
                Html::tag('th', Html::encode(Yii::t('app-salmon-event2', 'Event'))),
                \implode('', ArrayHelper::getColumn(
                    $waves,
                    function (array $wave): string {
                        if ($wave['king']) {
                            return Html::tag(
                                'td',
                                Html::encode(Yii::t('app-salmon-boss3', $wave['king']->name)),
                                ['class' => 'text-center'],
                            );
                        }

                        if ($wave['event']) {
                            return Html::tag(
                                'td',
                                Html::encode(Yii::t('app-salmon-event3', $wave['event']->name)),
                                ['class' => 'text-center'],
                            );
                        }

                        return Html::tag('td', Html::encode('-'), ['class' => 'text-center']);
                    },
                )),
                Html::tag('td', ''),
            ]),
        );
    }

    /**
     * @param array{tide: ?SalmonWaterLevel2}[] $waves
     */
    private function renderTides(array $waves): string
    {
        return Html::tag(
            'tr',
            \implode('', [
                Html::tag('th', Html::encode(Yii::t('app-salmon-tide2', 'Water Level'))),
                \implode('', ArrayHelper::getColumn(
                    $waves,
                    fn (array $wave): string => Html::tag(
                        'td',
                        ArrayHelper::getValue(
                            $wave,
                            function (array $wave): string {
                                $tide = $wave['tide'];
                                if (!$tide) {
                                    return Html::encode('-');
                                }

                                switch ($tide->key) {
                                    case 'low':
                                        return Progress::widget([
                                            'barOptions' => ['class' => 'progress-bar-info'],
                                            'label' => Yii::t('app-salmon-tide2', $tide->name),
                                            'options' => ['class' => 'm-0'],
                                            'percent' => 100.0 / 3.0,
                                        ]);

                                    case 'normal':
                                        return Progress::widget([
                                            'barOptions' => ['class' => 'progress-bar-success'],
                                            'label' => Yii::t('app-salmon-tide2', $tide->name),
                                            'options' => ['class' => 'm-0'],
                                            'percent' => 200.0 / 3.0,
                                        ]);

                                    case 'high':
                                        return Progress::widget([
                                            'barOptions' => ['class' => 'progress-bar-danger'],
                                            'label' => Yii::t('app-salmon-tide2', $tide->name),
                                            'options' => ['class' => 'm-0'],
                                            'percent' => 100.0,
                                        ]);
                                }

                                return Html::encode('-');
                            },
                        ),
                        ['class' => 'text-center'],
                    ),
                )),
                Html::tag('td', ''),
            ]),
        );
    }

    /**
     * @param array{quota: ?int, deliv: ?int}[] $waves
     */
    private function renderGoldenEggs(array $waves): string
    {
        $asset = SalmonEggAsset::register($this->view);

        return Html::tag(
            'tr',
            \implode('', [
                Html::tag(
                    'th',
                    \vsprintf('%s %s/%s', [
                        Html::img(
                            Yii::$app->assetManager->getAssetUrl($asset, 'golden-egg.png'),
                            ['class' => 'basic-icon'],
                        ),
                        Html::encode(Yii::t('app-salmon2', 'Delivers')),
                        Html::encode(Yii::t('app-salmon2', 'Quota')),
                    ]),
                ),
                \implode('', ArrayHelper::getColumn(
                    $waves,
                    function (array $wave): string {
                        $quota = $wave['quota'];
                        $deliv = $wave['deliv'];
                        if ($quota !== null && $deliv !== null && $quota > 0 && $deliv >= 0) {
                            return Html::tag(
                                'td',
                                Progress::widget([
                                    'barOptions' => [
                                        'class' => $deliv >= $quota ? 'progress-bar-success' : 'progress-bar-danger',
                                    ],
                                    'label' => \vsprintf('%s / %s', [
                                        $this->formatter->asInteger($deliv),
                                        $this->formatter->asInteger($quota),
                                    ]),
                                    'options' => ['class' => 'm-0'],
                                    'percent' => min(100, 100 * $deliv / $quota),
                                ]),
                            );
                        }

                        return Html::tag('td', Html::encode('-'), ['class' => 'text-center']);
                    },
                )),
                Html::tag(
                    'td',
                    implode(' ', [
                        Html::img(
                            Yii::$app->assetManager->getAssetUrl($asset, 'golden-egg.png'),
                            ['class' => 'basic-icon'],
                        ),
                        Html::encode(
                            $this->formatter->asInteger(
                                \array_reduce(
                                    ArrayHelper::getColumn($waves, 'deliv'),
                                    fn (int $carry, ?int $item): int => $carry + (int)$item,
                                    0,
                                ),
                            ),
                        ),
                    ]),
                    ['class' => 'text-center'],
                ),
            ]),
        );
    }

    /**
     * @param array{apper: ?int}[] $waves
     */
    private function renderGoldenAppearances(array $waves): string
    {
        $asset = SalmonEggAsset::register($this->view);

        return Html::tag(
            'tr',
            \implode('', [
                Html::tag(
                    'th',
                    \vsprintf('%s %s', [
                        Html::img(
                            Yii::$app->assetManager->getAssetUrl($asset, 'golden-egg.png'),
                            ['class' => 'basic-icon'],
                        ),
                        Html::encode(Yii::t('app-salmon2', 'Appearances')),
                    ]),
                ),
                \implode('', \array_map(
                    fn (array $wave, int $waveNumber): string => Html::tag(
                        'td',
                        $waveNumber < 4
                            ? $this->formatter->asInteger($wave['apper'])
                            : sprintf('(%s)', $this->formatter->asInteger($wave['apper'])),
                        ['class' => 'text-center'],
                    ),
                    $waves,
                    \range(1, count($waves)),
                )),
                Html::tag(
                    'td',
                    implode(' ', [
                        Html::img(
                            Yii::$app->assetManager->getAssetUrl($asset, 'golden-egg.png'),
                            ['class' => 'basic-icon'],
                        ),
                        Html::encode(
                            $this->formatter->asInteger(
                                \array_reduce(
                                    \array_slice(
                                        ArrayHelper::getColumn($waves, 'apper'),
                                        0,
                                        3, // ignores xtrawave
                                    ),
                                    fn (int $carry, ?int $item): int => $carry + (int)$item,
                                    0,
                                ),
                            ),
                        ),
                    ]),
                    ['class' => 'text-center'],
                ),
            ]),
        );
    }

    /**
     * @param array{result: ?bool, specials: SalmonSpecialUse3[]}[] $waves
     */
    private function renderSpecialUses(array $waves): string
    {
        return Html::tag(
            'tr',
            \implode('', [
                Html::tag('th', Html::encode(Yii::t('app', 'Specials'))),
                \implode('', ArrayHelper::getColumn(
                    $waves,
                    fn (array $wave): string => Html::tag(
                        'td',
                        ArrayHelper::getValue(
                            $wave,
                            function (array $wave): string {
                                return \implode('', ArrayHelper::getColumn(
                                    $wave['specials'],
                                    function (SalmonSpecialUse3 $info): string {
                                        $asset = Spl3WeaponAsset::register($this->view);
                                        return \str_repeat(
                                            Html::img($asset->getIconUrl('special', $info->special->key), [
                                                'class' => 'basic-icon mr-1',
                                            ]),
                                            $info->count,
                                        );
                                    },
                                ));
                            },
                        ),
                        ['style' => 'line-height:1.75em'],
                    ),
                )),
                Html::tag('td', ''),
            ]),
        );
    }

    private function makeData(?SalmonWave3 $wave): array
    {
        $clearWaves = $this->job->clear_waves;
        $result = null;
        if ($clearWaves !== null && $wave) {
            if ($clearWaves >= 3 && $wave->wave === 4) {
                // extra wave
                $result = $this->job->clear_extra;
            } elseif ($clearWaves >= $wave->wave) {
                $result = true;
            } elseif ($clearWaves + 1 === $wave->wave) {
                $result = false;
            }
        }

        return [
            'result' => $result,
            'event' => $wave ? $wave->event : null,
            'king' => $wave && $wave->wave === 4 ? $this->job->kingSalmonid : null,
            'tide' => $wave ? $wave->tide : null,
            'quota' => $wave && $wave->wave < 4 ? $wave->golden_quota : null,
            'deliv' => $wave && $wave->wave < 4 ? $wave->golden_delivered : null,
            'apper' => $wave ? $wave->golden_appearances : null,
            'specials' => $wave
                ? ArrayHelper::sort(
                    $wave->salmonSpecialUse3s,
                    fn (SalmonSpecialUse3 $a, SalmonSpecialUse3 $b): int => $a->special_id <=> $b->special_id,
                )
                : [],
        ];
    }
}
