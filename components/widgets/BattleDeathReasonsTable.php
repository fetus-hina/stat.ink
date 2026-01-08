<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\models\BattleDeathReason2;
use yii\base\Widget;
use yii\helpers\Html;

use function array_map;
use function implode;
use function strcmp;
use function usort;

final class BattleDeathReasonsTable extends Widget
{
    /**
     * @var BattleDeathReason2[]
     */
    public array $reasons = [];

    public function run(): string
    {
        if (!$reasons = $this->reasons) {
            return '';
        }

        usort(
            $reasons,
            fn (BattleDeathReason2 $a, BattleDeathReason2 $b): int => $b->count <=> $a->count
                ?: strcmp($a->reason->name, $b->reason->name),
        );

        return $this->renderTable($reasons);
    }

    /**
     * @param BattleDeathReason2[] $reasons
     */
    private function renderTable(array $reasons): string
    {
        return Html::tag(
            'table',
            $this->renderTbody($reasons),
            [],
        );
    }

    /**
     * @param BattleDeathReason2[] $reasons
     */
    private function renderTbody(array $reasons): string
    {
        return Html::tag(
            'tbody',
            implode(
                '',
                array_map(
                    fn (BattleDeathReason2 $model): string => $this->renderRow($model),
                    $reasons,
                ),
            ),
            [],
        );
    }

    private function renderRow(BattleDeathReason2 $model): string
    {
        return Html::tag(
            'tr',
            implode('', [
                Html::tag('td', Html::encode($model->reason->getTranslatedName())),
                Html::tag('td', ':', ['style' => ['padding' => '0 10px']]), // FIXME
                Html::tag(
                    'td',
                    Html::encode(
                        Yii::t('app', '{nFormatted} {n, plural, =1{time} other{times}}', [
                            'n' => (int)$model->count,
                            'nFormatted' => Yii::$app->formatter->asInteger((int)$model->count),
                        ]),
                    ),
                ),
            ]),
            [
                'data-key' => $model->reason->key,
            ],
        );
    }
}
