<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v3\stats;

use LogicException;
use Yii;
use app\models\Rule3;
use app\models\User;
use yii\base\Action;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use function array_map;
use function assert;
use function implode;
use function sprintf;
use function vsprintf;

final class WinRateAction extends Action
{
    public ?User $user = null;

    /**
     * @inheritdoc
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->user = User::find()
            ->andWhere([
                'screen_name' => (string)Yii::$app->request->get('screen_name'),
            ])
            ->limit(1)
            ->one();
        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    public function run(): string
    {
        if (!($user = $this->user)) {
            throw new LogicException();
        }

        $c = $this->controller;
        assert($c instanceof Controller);
        return $c->render('stats/win-rate', [
            'user' => $user,
            'stats' => $this->makeStats($user),
        ]);
    }

    private function makeStats(User $user): array
    {
        $list = (new Query())
            ->select([
                'lobby_id' => '{{%battle3}}.[[lobby_id]]',
                'lobby_group_id' => 'MAX({{%lobby3}}.[[group_id]])',
                'rule_id' => '{{%battle3}}.[[rule_id]]',
                'win_unknown' => self::buildStatsAggregate(true, null),
                'win_knockout' => self::buildStatsAggregate(true, true),
                'win_time' => self::buildStatsAggregate(true, false),
                'lose_unknown' => self::buildStatsAggregate(false, null),
                'lose_knockout' => self::buildStatsAggregate(false, true),
                'lose_time' => self::buildStatsAggregate(false, false),
                'total_seconds' => vsprintf('SUM(%s)', [
                    vsprintf('%s - %s', [
                        'EXTRACT(EPOCH FROM {{%battle3}}.[[end_at]])',
                        'EXTRACT(EPOCH FROM {{%battle3}}.[[start_at]])',
                    ]),
                ]),
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
            ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[user_id]]' => (int)$user->id,
                    '{{%result3}}.[[aggregatable]]' => true,
                ],
                ['not', ['{{%battle3}}.[[end_at]]' => null]],
                ['not', ['{{%battle3}}.[[lobby_id]]' => null]],
                ['not', ['{{%battle3}}.[[map_id]]' => null]],
                ['not', ['{{%battle3}}.[[result_id]]' => null]],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{%battle3}}.[[start_at]]' => null]],
                ['<>', '{{%lobby3}}.[[key]]', 'private'],
                '{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]',
            ])
            ->groupBy([
                '{{%battle3}}.[[user_id]]',
                '{{%battle3}}.[[lobby_id]]',
                '{{%battle3}}.[[rule_id]]',
            ])
            ->all();

        return array_map(
            function (array $row): array {
                foreach ($row as $k => $v) {
                    $row[$k] = (int)$v;
                }

                return $row;
            },
            $list,
        );
    }

    private static function buildStatsAggregate(bool $filterIsWin, ?bool $filterIsKO): string
    {
        return vsprintf('SUM(%s)', [
            vsprintf('CASE WHEN %s THEN 1 ELSE 0 END', [
                implode(' AND ', [
                    sprintf('{{%%result3}}.[[is_win]] = %s', $filterIsWin ? 'TRUE' : 'FALSE'),
                    $filterIsKO === null
                        ? sprintf('%s IS NULL', self::isKnockout())
                        : sprintf('%s = %s', self::isKnockout(), $filterIsKO ? 'TRUE' : 'FALSE'),
                ]),
            ]),
        ]);
    }

    private static function isKnockout(): string
    {
        return vsprintf('(CASE %s END)', [
            implode(' ', [
                // ナワバリバトル系統なら is_knockout は全て無視されるべき
                vsprintf('WHEN {{%%battle3}}.[[rule_id]] IN (%s) THEN FALSE', [
                    implode(', ', array_map(
                        fn (int $id): string => (string)$id,
                        self::getNawabariRuleIdList(),
                    )),
                ]),

                // それ以外（ガチバトル系統）なら is_knockout を返す (will returns TRUE, FALSE, NULL)
                'ELSE {{%battle3}}.[[is_knockout]]',
            ]),
        ]);
    }

    /**
     * @return int[]
     */
    private static function getNawabariRuleIdList(): array
    {
        /**
         * @var int[]|null $ret
         */
        static $ret = null;
        if ($ret === null) {
            $ret = ArrayHelper::getColumn(
                Rule3::find()
                    ->innerJoinWith(['group'], false)
                    ->andWhere(['{{%rule_group3}}.[[key]]' => 'nawabari'])
                    ->all(),
                fn (Rule3 $model): int => (int)$model->id,
            );
        }

        return $ret;
    }
}
