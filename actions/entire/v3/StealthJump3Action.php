<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use Yii;
use app\components\helpers\Season3Helper;
use app\components\helpers\TypeHelper;
use app\models\Rule3;
use app\models\Season3;
use app\models\StatStealthJumpEquipment3;
use app\models\StatXPowerDistribAbstract3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function is_string;

use const SORT_ASC;
use const SORT_DESC;

final class StealthJump3Action extends Action
{
    private const PARAM_SEASON_ID = Season3Helper::DEFAULT_SEASON_PARAM_NAME;
    private const PARAM_RULE_ID = 'rule';

    private const FALLBACK_XP_AVG = 2000.0;
    private const FALLBACK_XP_STDDEV = 290.0;

    public function run(): Response|string
    {
        $controller = TypeHelper::instanceOf($this->controller, Controller::class);

        $params = Yii::$app->db->transaction(
            fn (Connection $db): Response|array => $this->doRun(
                $controller,
                $db,
                Season3Helper::getUrlTargetSeason(self::PARAM_SEASON_ID)
                    ?? Season3Helper::getCurrentSeason('P1D')
                    ?? $this->getLatestSeason($db)
                    ?? throw new NotFoundHttpException(Yii::t('yii', 'Page not found.')),
                $this->getRule(
                    $db,
                    Yii::$app->request->get(self::PARAM_RULE_ID),
                ),
            ),
            Transaction::REPEATABLE_READ,
        );

        return $params instanceof Response
            ? $params
            : $controller->render('v3/stealth-jump3', $params);
    }

    private function doRun(
        Controller $controller,
        Connection $db,
        Season3 $season,
        ?Rule3 $rule,
    ): Response|array {
        if (!$rule) {
            return $controller->redirect([
                'entire/stealth-jump3',
                self::PARAM_SEASON_ID => $season->id,
                self::PARAM_RULE_ID => 'area',
            ]);
        }

        $xpDistrib = StatXPowerDistribAbstract3::find()
            ->andWhere([
                'rule_id' => $rule->id,
                'season_id' => $season->id,
            ])
            ->limit(1)
            ->one($db);

        return [
            'data' => $this->getData($db, $season, $rule),
            'rule' => $rule,
            'rules' => $this->getRules($db),
            'season' => $season,
            'seasons' => Season3Helper::getSeasons(),
            'xpAvg' => $xpDistrib && $xpDistrib->users >= 50 && $xpDistrib->stddev
                ? (float)$xpDistrib->average
                : self::FALLBACK_XP_AVG ,
            'xpStdDev' => $xpDistrib && $xpDistrib->users >= 50 && $xpDistrib->stddev
                ? (float)$xpDistrib->stddev
                : self::FALLBACK_XP_STDDEV,
            'ruleUrl' => fn (Rule3 $rule): string => Url::to(
                ['entire/stealth-jump3',
                    self::PARAM_SEASON_ID => $season->id,
                    self::PARAM_RULE_ID => $rule->key,
                ],
            ),
            'seasonUrl' => fn (Season3 $season): string => Url::to(
                ['entire/stealth-jump3',
                    self::PARAM_SEASON_ID => $season->id,
                    'rule' => $rule->key,
                ],
            ),
        ];
    }

    private function getLatestSeason(Connection $db): ?Season3
    {
        $model = StatStealthJumpEquipment3::find()
            ->innerJoinWith(['season'], true)
            ->orderBy(['{{%season3}}.[[start_at]]' => SORT_DESC])
            ->limit(1)
            ->one($db);
        return $model?->season ?? null;
    }

    private function getRule(Connection $db, mixed $ruleKey): ?Rule3
    {
        if (!is_string($ruleKey)) {
            return null;
        }

        return Rule3::find()
            ->andWhere(['key' => $ruleKey])
            ->limit(1)
            ->one($db);
    }

    /**
     * @return StatStealthJumpEquipment3[]
     */
    private function getData(
        Connection $db,
        Season3 $season,
        Rule3 $rule,
    ): array {
        return StatStealthJumpEquipment3::find()
            ->andWhere([
                'season_id' => $season->id,
                'rule_id' => $rule->id,
            ])
            ->orderBy(['x_power' => SORT_DESC])
            ->limit(600)
            ->all($db);
    }

    /**
     * @return array<int, Rule3>
     */
    private function getRules(Connection $db): array
    {
        return ArrayHelper::map(
            Rule3::find()
                ->innerJoinWith('group', false)
                ->andWhere(['{{%rule_group3}}.[[key]]' => 'gachi'])
                ->orderBy(['{{%rule3}}.[[rank]]' => SORT_ASC])
                ->all($db),
            'id',
            fn (Rule3 $v): Rule3 => $v,
        );
    }
}
