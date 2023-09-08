<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\assets\SalmonPlayersAsset;
use app\components\helpers\TypeHelper;
use app\components\i18n\Formatter;
use app\components\widgets\Icon;
use app\models\Salmon3;
use app\models\SalmonPlayer3;
use app\models\SalmonPlayerWeapon3;
use app\models\SalmonSpecialUse3;
use app\models\SalmonWave3;
use app\models\SalmonWeapon3;
use app\models\Special3;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\i18n\Formatter as BaseFormatter;
use yii\web\View;

use function array_filter;
use function array_map;
use function array_merge;
use function array_slice;
use function array_sum;
use function implode;
use function sprintf;
use function trim;
use function vsprintf;

final class SalmonPlayers extends Widget
{
    public Salmon3 $job;

    /**
     * @var SalmonPlayer3[]
     */
    public array $players = [];

    /**
     * @var SalmonWave3[]
     */
    public array $waves = [];

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
            SalmonPlayersAsset::register($view);
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
                        'salmon-v3-players',
                        'table',
                        'table-bordered',
                        'table-striped',
                    ],
                ],
            ),
            [
                'class' => 'table-responsive',
            ],
        );
    }

    private function renderHeader(): string
    {
        $am = null;
        $view = $this->view;
        if ($view instanceof View) {
            $am = Yii::$app->assetManager;
        }

        return Html::tag(
            'thead',
            Html::tag(
                'tr',
                implode('', [
                    Html::tag('th', ''),
                    implode('', ArrayHelper::getColumn(
                        array_slice(
                            array_merge($this->players, [null, null, null, null]),
                            0,
                            4,
                        ),
                        fn (?SalmonPlayer3 $player): string => Html::tag(
                            'th',
                            $player
                                ? implode(' ', [
                                    $player->is_disconnected
                                        ? Html::tag(
                                            'span',
                                            Icon::hasDisconnected(),
                                            ['class' => 'text-danger'],
                                        )
                                        : '',
                                    $player->species
                                        ? Icon::s3Species($player->species)
                                        : '',
                                    Html::tag(
                                        'span',
                                        Html::encode($player->name),
                                        [
                                            'class' => 'auto-tooltip',
                                            'title' => trim(
                                                vsprintf('%s %s', [
                                                    $player->name,
                                                    $player->number !== null
                                                        ? sprintf('#%s', $player->number)
                                                        : '',
                                                ]),
                                            ),
                                        ],
                                    ),
                                ])
                                : '-',
                            ['class' => 'omit'],
                        ),
                    )),
                ]),
            ),
        );
    }

    private function renderBody(): string
    {
        $players = array_slice(
            array_merge($this->players, [null, null, null, null]),
            0,
            4,
        );

        return Html::tag(
            'tbody',
            implode(
                '',
                array_filter(
                    [
                        $this->renderWeapons($players),
                        $this->renderSpecial($players),
                        $this->renderGoldenEggs($players),
                        $this->renderPowerEggs($players),
                        $this->renderRescues($players),
                        $this->renderRescued($players),
                        $this->renderBoss($players),
                    ],
                    fn (?string $html): bool => $html !== null,
                ),
            ),
        );
    }

    /**
     * @param (SalmonPlayer3|null)[] $players
     */
    private function renderWeapons(array $players): string
    {
        $isEggstraWork = $this->job?->is_eggstra_work ?? false;

        return Html::tag(
            'tr',
            implode('', [
                Html::tag('th', Html::encode(Yii::t('app', 'Weapons'))),
                implode('', ArrayHelper::getColumn(
                    $players,
                    fn (?SalmonPlayer3 $player): string => Html::tag(
                        'td',
                        ArrayHelper::getValue(
                            $player,
                            function (?SalmonPlayer3 $player) use ($isEggstraWork): string {
                                if (!$player) {
                                    return Html::encode('-');
                                }

                                $weapons = ArrayHelper::getColumn(
                                    ArrayHelper::sort(
                                        $player->salmonPlayerWeapon3s,
                                        fn (SalmonPlayerWeapon3 $a, SalmonPlayerWeapon3 $b): int => $a->wave <=> $b->wave,
                                    ),
                                    'weapon',
                                );

                                $am = null;
                                if ($this->view instanceof View) {
                                    $am = Yii::$app->assetManager;
                                }

                                if ($isEggstraWork) {
                                    $weapons = array_slice($weapons, 0, 1);
                                }

                                return implode('', array_map(
                                    function (?SalmonWeapon3 $weapon) use ($am): string {
                                        if (!$am) {
                                            return '';
                                        }

                                        return Html::tag(
                                            'div',
                                            Html::encode(Yii::t('app-weapon3', $weapon?->name ?? '?')),
                                            ['class' => 'omit'],
                                        );
                                    },
                                    $weapons,
                                ));
                            },
                        ),
                        ['class' => 'text-left'],
                    ),
                )),
            ]),
        );
    }

    /**
     * @param (SalmonPlayer3|null)[] $players
     */
    private function renderSpecial(array $players): string
    {
        return Html::tag(
            'tr',
            implode('', [
                Html::tag('th', Html::encode(Yii::t('app', 'Special'))),
                implode('', ArrayHelper::getColumn(
                    $players,
                    fn (?SalmonPlayer3 $player): string => Html::tag(
                        'td',
                        ArrayHelper::getValue(
                            $player,
                            function (?SalmonPlayer3 $player): string {
                                if (!$player || !$player->special) {
                                    return Html::encode('-');
                                }

                                return vsprintf('%s %s (%s)', [
                                    Icon::s3Special($player->special),
                                    Html::encode(
                                        Yii::t('app-special3', $player->special->name),
                                    ),
                                    Html::encode(
                                        TypeHelper::instanceOf($this->formatter, BaseFormatter::class)
                                            ->asInteger(
                                                $this->getSpecialUses($player->special),
                                            ),
                                    ),
                                ]);
                            },
                        ),
                        ['class' => 'text-center'],
                    ),
                )),
            ]),
        );
    }

    /**
     * @param (SalmonPlayer3|null)[] $players
     */
    private function renderGoldenEggs(array $players): string
    {
        return Html::tag(
            'tr',
            implode('', [
                Html::tag(
                    'th',
                    implode(' ', [
                        Icon::goldenEgg(),
                        Html::encode(Yii::t('app-salmon2', 'Delivers')),
                    ]),
                ),
                implode('', ArrayHelper::getColumn(
                    $players,
                    fn (?SalmonPlayer3 $player): string => Html::tag(
                        'td',
                        ArrayHelper::getValue(
                            $player,
                            function (?SalmonPlayer3 $player): string {
                                if (!$player || $player->golden_eggs === null) {
                                    return Html::encode('-');
                                }

                                if ($player->golden_assist > 0) {
                                    return vsprintf('%s %s', [
                                        Html::encode($this->formatter->asInteger($player->golden_eggs)),
                                        Html::tag(
                                            'small',
                                            Html::encode(
                                                vsprintf('<%s>', [
                                                    $this->formatter->asInteger($player->golden_assist),
                                                ]),
                                            ),
                                            ['class' => 'text-muted'],
                                        ),
                                    ]);
                                }

                                return Html::encode(
                                    $this->formatter->asInteger($player->golden_eggs),
                                );
                            },
                        ),
                        ['class' => 'text-center'],
                    ),
                )),
            ]),
        );
    }

    /**
     * @param (SalmonPlayer3|null)[] $players
     */
    private function renderPowerEggs(array $players): string
    {
        return Html::tag(
            'tr',
            implode('', [
                Html::tag(
                    'th',
                    implode(' ', [
                        Icon::powerEgg(),
                        Html::encode(Yii::t('app-salmon2', 'Power Eggs')),
                    ]),
                ),
                implode('', ArrayHelper::getColumn(
                    $players,
                    fn (?SalmonPlayer3 $player): string => Html::tag(
                        'td',
                        ArrayHelper::getValue(
                            $player,
                            function (?SalmonPlayer3 $player): string {
                                if (!$player || $player->power_eggs === null) {
                                    return Html::encode('-');
                                }

                                return Html::encode(
                                    $this->formatter->asInteger($player->power_eggs),
                                );
                            },
                        ),
                        ['class' => 'text-center'],
                    ),
                )),
            ]),
        );
    }

    /**
     * @param (SalmonPlayer3|null)[] $players
     */
    private function renderRescues(array $players): string
    {
        return Html::tag(
            'tr',
            implode('', [
                Html::tag(
                    'th',
                    implode(' ', [
                        // Icon::s3Rescues(),
                        Html::encode(Yii::t('app-salmon3', 'Rescues')),
                    ]),
                ),
                implode('', ArrayHelper::getColumn(
                    $players,
                    fn (?SalmonPlayer3 $player): string => Html::tag(
                        'td',
                        ArrayHelper::getValue(
                            $player,
                            function (?SalmonPlayer3 $player): string {
                                if (!$player || $player->rescue === null) {
                                    return Html::encode('-');
                                }

                                return Html::encode(
                                    $this->formatter->asInteger($player->rescue),
                                );
                            },
                        ),
                        ['class' => 'text-center'],
                    ),
                )),
            ]),
        );
    }

    /**
     * @param (SalmonPlayer3|null)[] $players
     */
    private function renderRescued(array $players): string
    {
        return Html::tag(
            'tr',
            implode('', [
                Html::tag(
                    'th',
                    implode(' ', [
                        // Icon::s3Rescued(),
                        Html::encode(Yii::t('app-salmon3', 'Rescued')),
                    ]),
                ),
                implode('', ArrayHelper::getColumn(
                    $players,
                    fn (?SalmonPlayer3 $player): string => Html::tag(
                        'td',
                        ArrayHelper::getValue(
                            $player,
                            function (?SalmonPlayer3 $player): string {
                                if (!$player || $player->rescued === null) {
                                    return Html::encode('-');
                                }

                                return Html::encode(
                                    $this->formatter->asInteger($player->rescued),
                                );
                            },
                        ),
                        ['class' => 'text-center'],
                    ),
                )),
            ]),
        );
    }

    /**
     * @param (SalmonPlayer3|null)[] $players
     */
    private function renderBoss(array $players): string
    {
        return Html::tag(
            'tr',
            implode('', [
                Html::tag('th', Html::encode(Yii::t('app-salmon3', 'Boss Salmonid'))),
                implode('', ArrayHelper::getColumn(
                    $players,
                    fn (?SalmonPlayer3 $player): string => Html::tag(
                        'td',
                        ArrayHelper::getValue(
                            $player,
                            function (?SalmonPlayer3 $player): string {
                                if (!$player || $player->defeat_boss === null) {
                                    return Html::encode('-');
                                }

                                return Html::encode(
                                    $this->formatter->asInteger($player->defeat_boss),
                                );
                            },
                        ),
                        ['class' => 'text-center'],
                    ),
                )),
            ]),
        );
    }

    private function getSpecialUses(Special3 $special): ?int
    {
        if (!$this->waves) {
            return null;
        }

        return array_sum(
            array_map(
                fn (SalmonWave3 $wave): int => $this->getSpecialUsesInWave($special, $wave),
                array_slice(
                    $this->waves,
                    0,
                    $this->job?->is_eggstra_work ? 5 : 3, // Exclude Xtrawave
                ),
            ),
        );
    }

    private function getSpecialUsesInWave(Special3 $special, SalmonWave3 $wave): int
    {
        return array_sum(
            array_map(
                fn (SalmonSpecialUse3 $model): int => $model->special_id === $special->id
                    ? $model->count
                    : 0,
                $wave->salmonSpecialUse3s,
            ),
        );
    }
}
