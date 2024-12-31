<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire\v3;

use Yii;
use app\components\helpers\Season3Helper;
use app\components\helpers\TypeHelper;
use app\models\Ability3;
use app\models\Rule3;
use app\models\Season3;
use app\models\StatAbility3XUsage;
use app\models\StatWeapon3XUsageRange;
use app\models\Weapon3;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use function array_pop;
use function vsprintf;

use const SORT_ASC;

final class Ability3Action extends Action
{
    private const PARAM_SEASON_ID = Season3Helper::DEFAULT_SEASON_PARAM_NAME;

    public function run(
        ?string $rule = null,
        ?string $xp = null,
    ): Response|string {
        $controller = TypeHelper::instanceOf($this->controller, Controller::class);

        $params = Yii::$app->db->transaction(
            fn (Connection $db): Response|array => $this->doRun(
                $controller,
                $db,
                $rule,
                $xp,
            ),
            Transaction::REPEATABLE_READ,
        );

        return $params instanceof Response
            ? $params
            : $controller->render('v3/ability3', $params);
    }

    private function doRun(
        Controller $controller,
        Connection $db,
        ?string $ruleKey,
        ?string $xpFilter,
    ): Response|array {
        $season = Season3Helper::getUrlTargetSeason(self::PARAM_SEASON_ID);
        $rule = $this->getRule($db, $ruleKey);
        if (!$season || !$rule) {
            $season = $season
                ?? Season3Helper::getCurrentSeason()
                ?? throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));

            return $controller->redirect(['entire/ability3',
                'rule' => $rule?->key ?? 'area',
                'xp' => $xpFilter,
                self::PARAM_SEASON_ID => $season?->id,
            ]);
        }

        if (!$xRange = $this->getXPowerRange($db, $season, $xpFilter)) {
            $ranges = $this->getXPowerRanges($db, $season);
            return $controller->redirect(['entire/ability3',
                'rule' => $rule->key,
                'xp' => array_pop($ranges)->id,
                self::PARAM_SEASON_ID => $season?->id,
            ]);
        }

        return [
            'abilities' => $this->getAbilities($db),
            'data' => $this->getData($db, $season, $rule, $xRange),
            'rule' => $rule,
            'rules' => $this->getRules($db),
            'season' => $season,
            'seasons' => Season3Helper::getSeasons(),
            'weapons' => $this->getWeapons($db, $season),
            'xRange' => $xRange,
            'xRanges' => $this->getXPowerRanges($db, $season),

            'seasonUrl' => fn (Season3 $season): string => Url::to(
                ['entire/ability3',
                    'rule' => $rule->key,
                    'xp' => $xRange->id,
                    self::PARAM_SEASON_ID => $season->id,
                ],
            ),

            'ruleUrl' => fn (Rule3 $rule): string => Url::to(
                ['entire/ability3',
                    'rule' => $rule->key,
                    'xp' => $xRange->id,
                    self::PARAM_SEASON_ID => $season?->id,
                ],
            ),

            'xRangeUrl' => fn (StatWeapon3XUsageRange $range): string => Url::to(
                ['entire/ability3',
                    'rule' => $rule->key,
                    'xp' => $range->id,
                    self::PARAM_SEASON_ID => $season?->id,
                ],
            ),
        ];
    }

    private function getRule(Connection $db, ?string $key): ?Rule3
    {
        if (!$key) {
            return null;
        }

        return Rule3::find()
            ->andWhere(['not', ['key' => ['nawabari', 'tricolor']]])
            ->andWhere(['key' => $key])
            ->limit(1)
            ->one($db);
    }

    /**
     * @return array<int, StatAbility3XUsage>
     */
    private function getData(
        Connection $db,
        Season3 $season,
        Rule3 $rule,
        StatWeapon3XUsageRange $xRange,
    ): array {
        return ArrayHelper::index(
            StatAbility3XUsage::find()
                ->andWhere([
                    'season_id' => $season->id,
                    'rule_id' => $rule->id,
                    'range_id' => $xRange->id,
                ])
                ->cache(600)
                ->all($db),
            'weapon_id',
        );
    }

    /**
     * @return array<int, Rule3>
     */
    private function getRules(Connection $db): array
    {
        return ArrayHelper::index(
            Rule3::find()
                ->andWhere(['not', ['key' => ['nawabari', 'tricolor']]])
                ->orderBy(['rank' => SORT_ASC])
                ->all($db),
            'id',
        );
    }

    private function getXPowerRange(
        Connection $db,
        Season3 $season,
        ?string $xpFilter,
    ): ?StatWeapon3XUsageRange {
        if (!$xpFilter = TypeHelper::intOrNull($xpFilter)) {
            return null;
        }

        return StatWeapon3XUsageRange::find()
            ->andWhere(['{{%stat_weapon3_x_usage_range}}.[[id]]' => $xpFilter])
            ->innerJoinWith(['term'], false)
            ->andWhere([
                '@>',
                '{{%stat_weapon3_x_usage_term}}.[[term]]',
                new Expression(
                    vsprintf('%s::TIMESTAMP(0) WITH TIME ZONE', [
                        $db->quoteValue($season->start_at),
                    ]),
                ),
            ])
            ->limit(1)
            ->cache(86400)
            ->one($db);
    }

    /**
     * @return StatWeapon3XUsageRange[]
     */
    private function getXPowerRanges(
        Connection $db,
        Season3 $season,
    ): array {
        return StatWeapon3XUsageRange::find()
            ->innerJoinWith(['term'], false)
            ->andWhere([
                '@>',
                '{{%stat_weapon3_x_usage_term}}.[[term]]',
                new Expression(
                    vsprintf('%s::TIMESTAMP(0) WITH TIME ZONE', [
                        $db->quoteValue($season->start_at),
                    ]),
                ),
            ])
            ->orderBy([
                '{{%stat_weapon3_x_usage_range}}.[[x_power_range]]' => SORT_ASC,
            ])
            ->cache(86400)
            ->all($db);
    }

    /**
     * @return Weapon3[]
     */
    private function getWeapons(Connection $db, Season3 $season): array
    {
        return Weapon3::find()
            ->with([
                'mainweapon',
                'subweapon',
                'special',
            ])
            ->innerJoinWith(
                [
                    'weapon3Aliases' => function (Query $query): void {
                        $query->andWhere(['~', '{{%weapon3_alias}}.[[key]]', '^[0-9]+$']);
                    },
                ],
                false,
            )
            ->andWhere(['and',
                ['<', '{{%weapon3}}.[[release_at]]', $season->end_at],
            ])
            ->orderBy([
                '({{%weapon3_alias}}.[[key]]::integer)' => SORT_ASC,
                '{{%weapon3}}.[[id]]' => SORT_ASC,
            ])
            ->cache(86400)
            ->all($db);
    }

    /**
     * @return Ability3[]
     */
    private function getAbilities(Connection $db): array
    {
        return Ability3::find()
            ->orderBy(['rank' => SORT_ASC])
            ->cache(86400)
            ->all($db);
    }
}
