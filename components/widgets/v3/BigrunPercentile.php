<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\components\helpers\TypeHelper;
use app\components\widgets\Icon;
use app\models\BigrunOfficialResult3;
use app\models\EggstraWorkOfficialResult3;
use app\models\SalmonSchedule3;
use app\models\StatBigrunDistribUserAbstract3;
use app\models\StatEggstraWorkDistribAbstract3;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\db\Connection;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

use function filter_var;
use function implode;
use function is_int;
use function is_object;
use function trim;
use function vsprintf;

use const FILTER_VALIDATE_INT;

final class BigrunPercentile extends Widget
{
    public ?SalmonSchedule3 $schedule = null;
    private bool $isEggstraWork = false;

    public function run(): string
    {
        $this->isEggstraWork = $this->schedule?->is_eggstra_work ?? false;
        $stats = $this->getStats();
        $users = ArrayHelper::getValue($stats, 'users');
        if (!is_int($users) || $users < 10) {
            return '';
        }

        $view = TypeHelper::instanceOf($this->view, View::class);
        BootstrapAsset::register($view);
        BootstrapPluginAsset::register($view);

        $id = (string)$this->id;
        return implode('', [
            $this->renderButton($id),
            $this->renderModal($id, $stats),
        ]);
    }

    private function renderButton(string $id): string
    {
        return Html::button(
            Icon::info(),
            [
                'class' => 'btn btn-default btn-xs p-1',
                'data' => [
                    'target' => "#{$id}",
                    'toggle' => 'modal',
                ],
            ],
        );
    }

    private function renderModal(
        string $id,
        StatBigrunDistribUserAbstract3|StatEggstraWorkDistribAbstract3|null $stats,
    ): string {
        return Html::tag(
            'div',
            Html::tag(
                'div',
                Html::tag(
                    'div',
                    implode('', [
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
            implode('', [
                $this->renderModalHeaderClose(),
                $this->renderModalHeaderTitle(),
            ]),
            ['class' => 'modal-header'],
        );
    }

    private function renderModalHeaderClose(): string
    {
        return Html::button(Icon::close(), [
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
            implode('', [
                $this->renderModalFooterDetails(),
                $this->renderModalFooterClose(),
            ]),
            ['class' => 'modal-footer'],
        );
    }

    private function renderModalFooterClose(): string
    {
        return Html::button(
            implode('', [
                Icon::close(),
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

    private function renderModalFooterDetails(): string
    {
        return $this->isEggstraWork
            ? Html::a(
                Html::encode(Yii::t('app', 'Details')),
                ['entire/salmon3-eggstra-work', 'shift' => $this->schedule?->id],
                ['class' => 'btn btn-default'],
            )
            : Html::a(
                Html::encode(Yii::t('app', 'Details')),
                ['entire/salmon3-bigrun', 'shift' => $this->schedule?->id],
                ['class' => 'btn btn-default'],
            );
    }

    private function renderModalBody(
        StatBigrunDistribUserAbstract3|StatEggstraWorkDistribAbstract3|null $stats,
    ): string {
        return Html::tag(
            'div',
            implode('', [
                $this->renderTable($stats),
                $this->renderNotice(),
            ]),
            ['class' => 'modal-body mb-3'],
        );
    }

    private function renderTable(
        StatBigrunDistribUserAbstract3|StatEggstraWorkDistribAbstract3|null $stats,
    ): string {
        return Html::tag(
            'div',
            Html::tag(
                'table',
                implode('', [
                    $this->renderTableHeader($stats),
                    $this->renderTableBody($stats),
                ]),
                ['class' => 'table table-striped'],
            ),
            ['class' => 'table-responsive'],
        );
    }

    private function renderTableHeader(
        StatBigrunDistribUserAbstract3|StatEggstraWorkDistribAbstract3|null $stats,
    ): string {
        return Html::tag(
            'thead',
            Html::tag(
                'tr',
                implode('', [
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

    private function renderTableBody(
        StatBigrunDistribUserAbstract3|StatEggstraWorkDistribAbstract3|null $stats,
    ): string {
        $official = $this->isEggstraWork
            ? EggstraWorkOfficialResult3::find()
                ->andWhere(['schedule_id' => $this->schedule?->id ?? -1])
                ->limit(1)
                ->cache(120)
                ->one()
            : BigrunOfficialResult3::find()
                ->andWhere(['schedule_id' => $this->schedule?->id ?? -1])
                ->limit(1)
                ->cache(120)
                ->one();

        return Html::tag(
            'tbody',
            implode('', [
                $this->renderTableRowGold($stats, $official),
                $this->renderTableRowSilver($stats, $official),
                $this->renderTableRowBronze($stats, $official),
                $this->renderTableRowAverage($stats, $official),
                $this->renderTableRowUsers($stats, $official),
            ]),
        );
    }

    private function renderTableRowGold(
        StatBigrunDistribUserAbstract3|StatEggstraWorkDistribAbstract3|null $stats,
        BigrunOfficialResult3|EggstraWorkOfficialResult3|null $official,
    ): string {
        return $this->renderTableRowEggs(
            $this->isEggstraWork ? 'eggstra-1.png' : 'top-1.png',
            5,
            self::intVal(
                match (is_object($stats) ? $stats::class : null) {
                    StatBigrunDistribUserAbstract3::class => ArrayHelper::getValue($stats, 'p95'),
                    StatEggstraWorkDistribAbstract3::class => ArrayHelper::getValue($stats, 'top_5_pct'),
                    default => null,
                },
            ),
            self::intVal(ArrayHelper::getValue($official, 'gold')),
        );
    }

    private function renderTableRowSilver(
        StatBigrunDistribUserAbstract3|StatEggstraWorkDistribAbstract3|null $stats,
        BigrunOfficialResult3|EggstraWorkOfficialResult3|null $official,
    ): string {
        return $this->renderTableRowEggs(
            $this->isEggstraWork ? 'eggstra-2.png' : 'top-2.png',
            20,
            self::intVal(
                match (is_object($stats) ? $stats::class : null) {
                    StatBigrunDistribUserAbstract3::class => ArrayHelper::getValue($stats, 'p80'),
                    StatEggstraWorkDistribAbstract3::class => ArrayHelper::getValue($stats, 'top_20_pct'),
                    default => null,
                },
            ),
            self::intVal(ArrayHelper::getValue($official, 'silver')),
        );
    }

    private function renderTableRowBronze(
        StatBigrunDistribUserAbstract3|StatEggstraWorkDistribAbstract3|null $stats,
        BigrunOfficialResult3|EggstraWorkOfficialResult3|null $official,
    ): string {
        return $this->renderTableRowEggs(
            $this->isEggstraWork ? 'eggstra-3.png' : 'top-3.png',
            50,
            self::intVal(
                match (is_object($stats) ? $stats::class : null) {
                    StatBigrunDistribUserAbstract3::class => ArrayHelper::getValue($stats, 'p50'),
                    StatEggstraWorkDistribAbstract3::class => ArrayHelper::getValue($stats, 'median'),
                    default => null,
                },
            ),
            self::intVal(ArrayHelper::getValue($official, 'bronze')),
        );
    }

    private function renderTableRowEggs(
        string $badgeIconPath,
        int $percentile,
        ?int $userEggs,
        ?int $officialEggs,
    ): string {
        return Html::tag(
            'tr',
            implode('', [
                Html::tag(
                    'th',
                    implode(' ', [
                        Icon::goldenEgg(),
                        Html::encode(
                            Yii::t('app', 'Top {percentile}%', ['percentile' => $percentile]),
                        ),
                    ]),
                    ['scope' => 'row'],
                ),
                Html::tag(
                    'td',
                    trim(
                        $userEggs === null
                            ? '-'
                            : implode(' ', [
                                Icon::goldenEgg(),
                                Html::encode(Yii::$app->formatter->asInteger($userEggs)),
                            ]),
                    ),
                ),
                Html::tag(
                    'td',
                    trim(
                        $officialEggs === null
                            ? '-'
                            : implode(' ', [
                                Icon::goldenEgg(),
                                Html::encode(Yii::$app->formatter->asInteger($officialEggs)),
                            ]),
                    ),
                ),
            ]),
        );
    }

    private function renderTableRowAverage(
        StatBigrunDistribUserAbstract3|StatEggstraWorkDistribAbstract3|null $stats,
        BigrunOfficialResult3|EggstraWorkOfficialResult3|null $official,
    ): string {
        return Html::tag(
            'tr',
            implode('', [
                Html::tag(
                    'th',
                    Html::encode(Yii::t('app', 'Average')),
                    ['scope' => 'row'],
                ),
                Html::tag(
                    'td',
                    vsprintf('%s %s (σ = %s)', [
                        Icon::goldenEgg(),
                        Html::encode(
                            Yii::$app->formatter->asDecimal(
                                ArrayHelper::getValue($stats, 'average'),
                                2,
                            ),
                        ),
                        Html::encode(
                            Yii::$app->formatter->asDecimal(
                                ArrayHelper::getValue($stats, 'stddev'),
                                2,
                            ),
                        ),
                    ]),
                ),
                Html::tag('td', '-'),
            ]),
        );
    }

    private function renderTableRowUsers(
        StatBigrunDistribUserAbstract3|StatEggstraWorkDistribAbstract3|null $stats,
        BigrunOfficialResult3|EggstraWorkOfficialResult3|null $official,
    ): string {
        return Html::tag(
            'tr',
            implode('', [
                Html::tag(
                    'th',
                    Html::encode(Yii::t('app', 'Users')),
                    ['scope' => 'row'],
                ),
                Html::tag(
                    'td',
                    vsprintf('%s %s', [
                        Icon::user(),
                        Html::encode(
                            Yii::$app->formatter->asInteger(
                                ArrayHelper::getValue($stats, 'users'),
                            ),
                        ),
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

    private function getStats(): StatBigrunDistribUserAbstract3|StatEggstraWorkDistribAbstract3|null
    {
        $db = TypeHelper::instanceOf(Yii::$app->db, Connection::class);
        return $this->isEggstraWork
            ? StatEggstraWorkDistribAbstract3::find()
                ->andWhere(['schedule_id' => $this->schedule?->id ?? -1])
                ->limit(1)
                ->one($db)
            : StatBigrunDistribUserAbstract3::find()
                ->andWhere(['schedule_id' => $this->schedule?->id ?? -1])
                ->limit(1)
                ->one($db);
    }

    private static function intVal(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        $value = filter_var($value, FILTER_VALIDATE_INT);
        return is_int($value) ? $value : null;
    }
}
