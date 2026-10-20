<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\fest;

use Override;
use Yii;
use app\models\FestViewForm;
use app\models\Splatfest;
use app\models\SplatfestBattleSummary;
use app\models\SplatfestTeam;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

use function array_map;
use function strtotime;

use const SORT_ASC;

/**
 * @property-read array<int, array{at: int|false, alpha: int, bravo: int}> $results
 */
class ViewAction extends BaseAction
{
    public ?Splatfest $fest = null;
    public ?SplatfestTeam $alpha = null;
    public ?SplatfestTeam $bravo = null;

    #[Override]
    public function init(): void
    {
        parent::init();
        $this->doInit();
    }

    private function doInit(): void
    {
        if (!$form = $this->createAndValidateRequestForm()) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $this->fest = Splatfest::find()
            ->innerJoinWith('region', false)
            ->andWhere([
                '{{region}}.[[key]]' => $form->region,
                '{{splatfest}}.[[order]]' => $form->order,
            ])
            ->limit(1)
            ->one();
        if (!$this->fest) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $this->alpha = SplatfestTeam::findOne(['fest_id' => $this->fest->id, 'team_id' => 1]);
        $this->bravo = SplatfestTeam::findOne(['fest_id' => $this->fest->id, 'team_id' => 2]);
        if (!$this->alpha || !$this->bravo) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    private function createAndValidateRequestForm(): FestViewForm|false
    {
        $req = Yii::$app->request;
        $model = FestViewForm::validateData(
            [
                'region' => $req->get('region'),
                'order' => $req->get('order'),
            ],
        );
        if ($model->validate()) {
            return $model;
        }
        return false;
    }

    #[Override]
    public function run(): string
    {
        return $this->controller->render('view', [
            'fest' => $this->fest,
            'alpha' => $this->alpha,
            'bravo' => $this->bravo,
            'results' => $this->results,
        ]);
    }

    /**
     * @return array<int, array{at: int|false, alpha: int, bravo: int}>
     */
    public function getResults(): array
    {
        $query = SplatfestBattleSummary::find()
            ->andWhere(['{{splatfest_battle_summary}}.[[fest_id]]' => $this->fest?->id])
            ->orderBy(['{{splatfest_battle_summary}}.[[timestamp]]' => SORT_ASC]);

        return array_map(
            fn ($a) => [
                'at' => strtotime($a->timestamp),
                'alpha' => $a->alpha_win + $a->bravo_lose,
                'bravo' => $a->bravo_win + $a->alpha_lose,
            ],
            $query->all(),
        );
    }
}
