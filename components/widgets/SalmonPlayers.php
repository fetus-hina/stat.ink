<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\components\i18n\Formatter;
use app\models\SalmonPlayer2;
use app\models\SalmonPlayerSpecialUse2;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function array_filter;
use function array_map;
use function call_user_func;
use function implode;
use function is_callable;
use function sprintf;

class SalmonPlayers extends Widget
{
    public $formatter;
    public $work;
    public $players;

    public function init()
    {
        parent::init();

        if (!$this->formatter) {
            $this->formatter = Yii::createObject([
                'class' => Formatter::class,
                'nullDisplay' => '-',
            ]);
        }
    }

    public function run(): string
    {
        BootstrapAsset::register($this->view);

        $id = "#{$this->id}";
        $this->view->registerCss(sprintf(
            '%s@media(max-width:30em){%s}',
            Html::renderCss([
                "{$id}" => [
                    'table-layout' => 'fixed',
                    'width' => 'calc(100% - 1px)',
                ],
                "{$id} th" => [
                    'width' => 'calc((100% - 15em) / 4)',
                ],
                "{$id} th:first-child" => [
                    'width' => '15em',
                ],
            ]),
            // min-display
            Html::renderCss([
                "{$id}" => [
                    'table-layout' => 'auto',
                ],
                "{$id} th" => [
                    'width' => 'auto',
                ],
                "{$id} th:first-child" => [
                    'width' => 'auto',
                ],
            ]),
        ));

        return Html::tag(
            'div',
            Html::tag(
                'table',
                $this->renderHeader() . $this->renderBody(),
                [
                    'id' => $this->id,
                    'class' => 'table table-striped table-bordered',
                ],
            ),
            [
                'class' => 'table-responsive',
            ],
        );
    }

    protected function renderHeader(): string
    {
        return Html::tag('thead', Html::tag(
            'tr',
            Html::tag('th', '') . implode('', array_map(
                fn (SalmonPlayer2 $player): string => Html::tag('th', PlayerName2Widget::widget([
                    'player' => $player,
                    'user' => $this->work->user,
                    'nameOnly' => false,
                ])),
                $this->players,
            )),
        ));
    }

    protected function renderBody(): string
    {
        $data = array_filter([
            [
                'label' => Yii::t('app-salmon2', 'Wave {waveNumber}', ['waveNumber' => 1]),
                'format' => 'text',
                'value' => function (SalmonPlayer2 $player, self $widget): string {
                    $weapons = $player->weapons;
                    return Yii::t('app-weapon2', $weapons[0]->weapon->name ?? '?');
                },
            ],
            [
                'label' => Yii::t('app-salmon2', 'Wave {waveNumber}', ['waveNumber' => 2]),
                'format' => 'text',
                'value' => function (SalmonPlayer2 $player, self $widget): string {
                    $weapons = $player->weapons;
                    return Yii::t('app-weapon2', $weapons[1]->weapon->name ?? '?');
                },
            ],
            [
                'label' => Yii::t('app-salmon2', 'Wave {waveNumber}', ['waveNumber' => 3]),
                'format' => 'text',
                'value' => function (SalmonPlayer2 $player, self $widget): string {
                    $weapons = $player->weapons;
                    return Yii::t('app-weapon2', $weapons[2]->weapon->name ?? '?');
                },
            ],
            [
                'label' => Yii::t('app', 'Special'),
                'format' => 'text',
                'value' => fn (SalmonPlayer2 $player, self $widget): string => Yii::t('app-special2', $player->special->name ?? '?'),
            ],
            [
                'label' => Yii::t('app-salmon2', 'Special Use'),
                'format' => 'text',
                'value' => fn (SalmonPlayer2 $player, self $widget): string => implode(' - ', array_map(
                    fn (SalmonPlayerSpecialUse2 $use): string => $this->formatter->asInteger($use->count),
                    $player->specialUses,
                )),
            ],
            [
                'label' => Yii::t('app-salmon2', 'Rescues'),
                'attribute' => 'rescue',
                'format' => 'integer',
            ],
            [
                'label' => Yii::t('app-salmon2', 'Deaths'),
                'attribute' => 'death',
                'format' => 'integer',
            ],
            [
                'label' => Yii::t('app-salmon2', 'Golden Eggs'),
                'attribute' => 'golden_egg_delivered',
                'format' => 'integer',
            ],
            [
                'label' => Yii::t('app-salmon2', 'Power Eggs'),
                'attribute' => 'power_egg_collected',
                'format' => 'integer',
            ],
        ]);
        return Html::tag('tbody', implode('', array_map(
            fn (array $row): string => $this->renderRow($row),
            $data,
        )));
    }

    protected function renderRow(array $rowInfo): string
    {
        return Html::tag('tr', implode('', [
            $this->renderRowHeader($rowInfo),
            implode('', array_map(
                fn (SalmonPlayer2 $player): ?string => $this->renderCellData($rowInfo, $player),
                $this->players,
            )),
        ]));
    }

    protected function renderRowHeader(array $rowInfo): string
    {
        return Html::tag(
            'th',
            $this->formatter->asText($rowInfo['label']),
            ['scope' => 'row'],
        );
    }

    protected function renderCellData(array $rowInfo, SalmonPlayer2 $player): string
    {
        return Html::tag(
            'td',
            $this->formatter->format(
                $this->renderCellValue($rowInfo, $player),
                ArrayHelper::getValue($rowInfo, 'format', 'text'),
            ),
        );
    }

    protected function renderCellValue(array $rowInfo, SalmonPlayer2 $player)
    {
        $value = ArrayHelper::getValue($rowInfo, 'value');
        if ($value === null && isset($rowInfo['attribute'])) {
            $value = ArrayHelper::getValue($player, $rowInfo['attribute']);
        }

        if (is_callable($value)) {
            $value = call_user_func($value, $player, $this);
        }

        return $value;
    }
}
