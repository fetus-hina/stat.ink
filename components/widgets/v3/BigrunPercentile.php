<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\assets\SalmonBadgeAsset;
use app\assets\SalmonEggAsset;
use app\components\widgets\FA;
use app\models\BigrunOfficialResult3;
use app\models\SalmonSchedule3;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\db\Connection;
use yii\db\Expression as DbExpression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

final class BigrunPercentile extends Widget
{
    public ?SalmonSchedule3 $schedule = null;

    public function run(): string
    {
        $stats = $this->getStats();
        if ($stats['users'] < 10) {
            return '';
        }

        $view = $this->view;
        if ($view instanceof View) {
            BootstrapAsset::register($view);
            BootstrapPluginAsset::register($view);
        }

        $id = (string)$this->id;
        return \implode('', [
            $this->renderButton($id),
            $this->renderModal($id, $stats),
        ]);
    }

    private function renderButton(string $id): string
    {
        $view = $this->view;

        return Html::button(
            $view instanceof View
                ? Html::img(
                    Yii::$app->assetManager->getAssetUrl(
                        SalmonBadgeAsset::register($view),
                        'top-1.png',
                    ),
                    ['class' => 'basic-icon'],
                )
                : '',
            [
                'class' => 'btn btn-default btn-xs p-1',
                'data' => [
                    'target' => "#{$id}",
                    'toggle' => 'modal',
                ],
            ],
        );
    }

    private function renderModal(string $id, array $stats): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'div',
                Html::tag(
                    'div',
                    \implode('', [
                        $this->renderModalHeader(),
                        $this->renderModalBody($stats),
                        $this->renderModalFooter(),
                    ]),
                    ['class' => 'modal-content'],
                ),
                ['class' => 'modal-dialog'],
            ),
            [
                'class' => 'modal fade',
                'id' => $id,
                'tabindex' => '-1',
            ],
        );
    }

    private function renderModalHeader(): string
    {
        return Html::tag(
            'div',
            \implode('', [
                $this->renderModalHeaderClose(),
                $this->renderModalHeaderTitle(),
            ]),
            ['class' => 'modal-header'],
        );
    }

    private function renderModalHeaderClose(): string
    {
        return Html::button((string)FA::fas('times')->fw(), [
            'class' => 'close',
            'data' => [
                'dismiss' => 'modal',
            ],
            'aria' => [
                'label' => Yii::t('app', 'Close'),
            ],
        ]);
    }

    private function renderModalHeaderTitle(): string
    {
        return Html::tag(
            'h4',
            Html::encode(''), // TODO
            ['class' => 'modal-title'],
        );
    }

    private function renderModalFooter(): string
    {
        return Html::tag(
            'div',
            \implode('', [
                $this->renderModalFooterClose(),
            ]),
            ['class' => 'modal-footer'],
        );
    }

    private function renderModalFooterClose(): string
    {
        return Html::button(
            \implode('', [
                (string)FA::fas('times')->fw(),
                Html::encode(Yii::t('app', 'Close')),
            ]),
            [
                'class' => 'btn btn-default',
                'data' => [
                    'dismiss' => 'modal',
                ],
            ],
        );
    }

    private function renderModalBody(array $stats): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderTable($stats),
                $this->renderNotice(),
            ]),
            ['class' => 'modal-body mb-3'],
        );
    }

    private function renderTable(array $stats): string
    {
        return Html::tag(
            'div',
            Html::tag(
                'table',
                \implode('', [
                    $this->renderTableHeader($stats),
                    $this->renderTableBody($stats),
                ]),
                ['class' => 'table table-striped'],
            ),
            ['class' => 'table-responsive'],
        );
    }

    private function renderTableHeader(array $stats): string
    {
        return Html::tag(
            'thead',
            Html::tag(
                'tr',
                \implode('', [
                    Html::tag('th', '', ['style' => ['width' => '10em']]),
                    Html::tag(
                        'th',
                        Html::encode(Yii::$app->name),
                        ['class' => 'text-center'],
                    ),
                    Html::tag(
                        'th',
                        Html::encode(Yii::t('app', 'Official Results')),
                        ['class' => 'text-center'],
                    ),
                ]),
            ),
        );
    }

    private function renderTableBody(array $stats): string
    {
        $official = $this->schedule?->bigrunOfficialResult3;

        return Html::tag(
            'tbody',
            \implode('', [
                $this->renderTableRowGold($stats, $official),
                $this->renderTableRowSilver($stats, $official),
                $this->renderTableRowBronze($stats, $official),
                $this->renderTableRowAverage($stats, $official),
                $this->renderTableRowUsers($stats, $official),
            ]),
        );
    }

    private function renderTableRowGold(array $stats, ?BigrunOfficialResult3 $official): string
    {
        return $this->renderTableRowEggs(
            'top-1.png',
            5,
            self::intVal(ArrayHelper::getValue($stats, 'val05pct')),
            self::intVal(ArrayHelper::getValue($official, 'gold')),
        );
    }

    private function renderTableRowSilver(array $stats, ?BigrunOfficialResult3 $official): string
    {
        return $this->renderTableRowEggs(
            'top-2.png',
            20,
            self::intVal(ArrayHelper::getValue($stats, 'val20pct')),
            self::intVal(ArrayHelper::getValue($official, 'silver')),
        );
    }

    private function renderTableRowBronze(array $stats, ?BigrunOfficialResult3 $official): string
    {
        return $this->renderTableRowEggs(
            'top-3.png',
            50,
            self::intVal(ArrayHelper::getValue($stats, 'val50pct')),
            self::intVal(ArrayHelper::getValue($official, 'bronze')),
        );
    }

    private function renderTableRowEggs(
        string $badgeIconPath,
        int $percentile,
        ?int $userEggs,
        ?int $officialEggs,
    ): string {
        $view = $this->view;
        $egg = $view instanceof View
            ? Html::img(
                Yii::$app->assetManager->getAssetUrl(SalmonEggAsset::register($view), 'golden-egg.png'),
                ['class' => 'basic-icon'],
            )
            : '';

        return Html::tag(
            'tr',
            \implode('', [
                Html::tag(
                    'th',
                    \trim(
                        \vsprintf('%s %s', [
                            $view instanceof View
                                ? Html::img(
                                    Yii::$app->assetManager->getAssetUrl(
                                        SalmonBadgeAsset::register($view),
                                        $badgeIconPath,
                                    ),
                                    ['class' => 'basic-icon'],
                                )
                                : '',
                            Html::encode(
                                Yii::t('app', 'Top {percentile}%', ['percentile' => $percentile]),
                            ),
                        ]),
                    ),
                    ['scope' => 'row'],
                ),
                Html::tag(
                    'td',
                    \trim(
                        $userEggs === null
                            ? '-'
                            : \vsprintf('%s %s', [
                                $egg,
                                Html::encode(Yii::$app->formatter->asInteger($userEggs)),
                            ]),
                    ),
                ),
                Html::tag(
                    'td',
                    \trim(
                        $officialEggs === null
                            ? '-'
                            : \vsprintf('%s %s', [
                                $egg,
                                Html::encode(Yii::$app->formatter->asInteger($officialEggs)),
                            ]),
                    ),
                ),
            ]),
        );
    }

    private function renderTableRowAverage(array $stats, ?BigrunOfficialResult3 $official): string
    {
        $view = $this->view;
        $egg = $view instanceof View
            ? Html::img(
                Yii::$app->assetManager->getAssetUrl(SalmonEggAsset::register($view), 'golden-egg.png'),
                ['class' => 'basic-icon'],
            )
            : '';

        return Html::tag(
            'tr',
            \implode('', [
                Html::tag(
                    'th',
                    Html::encode(Yii::t('app', 'Average')),
                    ['scope' => 'row'],
                ),
                Html::tag(
                    'td',
                    \vsprintf('%s %s (σ = %s)', [
                        $egg,
                        Html::encode(Yii::$app->formatter->asDecimal($stats['avg'], 2)),
                        Html::encode(Yii::$app->formatter->asDecimal($stats['stddev'], 2)),
                    ]),
                ),
                Html::tag('td', '-'),
            ]),
        );
    }

    private function renderTableRowUsers(array $stats, ?BigrunOfficialResult3 $official): string
    {
        return Html::tag(
            'tr',
            \implode('', [
                Html::tag(
                    'th',
                    Html::encode(Yii::t('app', 'Users')),
                    ['scope' => 'row'],
                ),
                Html::tag(
                    'td',
                    \vsprintf('%s %s', [
                        (string)FA::fas('user')->fw(),
                        Html::encode(Yii::$app->formatter->asInteger($stats['users'])),
                    ]),
                ),
                Html::tag('td', '-'),
            ]),
        );
    }

    private function renderNotice(): string
    {
        return Html::tag(
            'p',
            Html::encode(
                Yii::t(
                    'app',
                    'This data is based on {siteName} users and differs significantly from overall game statistics.',
                    ['siteName' => Yii::$app->name],
                ),
            ),
        );
    }

    private function getStats(): array
    {
        $db = Yii::$app->db;
        assert($db instanceof Connection);

        $percentile = fn (int $pct): DbExpression => new DbExpression(
            vsprintf('PERCENTILE_DISC(%.2f) WITHIN GROUP (ORDER BY %s DESC)', [
                (float)$pct / 100.0,
                $db->quoteColumnName('golden_eggs'),
            ]),
        );

        $query = (new Query())
            ->select([
                'users' => 'COUNT(*)',
                'val05pct' => $percentile(5),
                'val20pct' => $percentile(20),
                'val50pct' => $percentile(50),
                'avg' => 'AVG([[golden_eggs]])',
                'stddev' => 'STDDEV_SAMP([[golden_eggs]])',
            ])
            ->from('{{%user_stat_bigrun3}}')
            ->andWhere(['schedule_id' => $this->schedule?->id ?? -1]);

        return $query->one($db);
    }

    private static function intVal($value): ?int
    {
        $value = \filter_var($value, FILTER_VALIDATE_INT);
        return \is_int($value) ? $value : null;
    }
}
