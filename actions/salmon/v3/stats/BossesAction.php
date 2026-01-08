<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats;

use LogicException;
use Yii;
use app\actions\salmon\v3\stats\bosses\BadgeStats;
use app\models\Salmon3;
use app\models\Salmon3FilterForm;
use app\models\SalmonBoss3;
use app\models\User;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use function assert;

final class BossesAction extends Action
{
    use BadgeStats;

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

        $filter = Yii::createObject(Salmon3FilterForm::class);
        $filter->load($_GET);
        $filter->validate();

        $data = Yii::$app->db->transaction(
            function (Connection $db) use ($filter, $user): array {
                $cacheCondition = (new Query())
                    ->select([
                        'max' => 'MAX([[id]])',
                        'count' => 'COUNT(*)',
                    ])
                    ->from('{{%salmon3}}')
                    ->andWhere([
                        'user_id' => $user->id,
                        'is_deleted' => false,
                    ])
                    ->one($db);

                return [
                    'badges' => $this->makeStatsForBadge($db, $user, $cacheCondition),
                    'bosses' => $this->getBosses($db),
                    'filter' => $filter,
                    'stats' => $this->makeStats($db, $user, $filter, $cacheCondition),
                    'user' => $user,
                ];
            },
            Transaction::REPEATABLE_READ,
        );

        $c = $this->controller;
        assert($c instanceof Controller);
        return $c->render('stats/bosses', $data);
    }

    /**
     * @return array<string, SalmonBoss3>
     */
    private function getBosses(Connection $db): array
    {
        return Yii::$app->cache->getOrSet(
            __METHOD__,
            fn () => ArrayHelper::map(
                SalmonBoss3::find()->all($db),
                'key',
                fn (SalmonBoss3 $model): SalmonBoss3 => $model,
            ),
            86400,
        );
    }

    /**
     * @return array<string, array{boss_key: string, appearances: int, defeated: int, defeated_by_me: int}>
     */
    private function makeStats(
        Connection $db,
        User $user,
        Salmon3FilterForm $filter,
        mixed $cacheCondition,
    ): array {
        $query = Salmon3::find()
            ->innerJoinWith(
                [
                    'salmonBossAppearance3s',
                    'salmonBossAppearance3s.boss',
                ],
                false,
            )
            ->andWhere([
                '{{%salmon3}}.[[user_id]]' => $user->id,
                '{{%salmon3}}.[[is_deleted]]' => false,
            ])
            ->select([
                'boss_key' => 'MAX({{%salmon_boss3}}.[[key]])',
                'appearances' => 'SUM({{%salmon_boss_appearance3}}.[[appearances]])',
                'defeated' => 'SUM({{%salmon_boss_appearance3}}.[[defeated]])',
                'defeated_by_me' => 'SUM({{%salmon_boss_appearance3}}.[[defeated_by_me]])',
            ])
            ->groupBy([
                '{{%salmon_boss_appearance3}}.[[boss_id]]',
            ]);

        $filter->decorateQuery($query);

        return Yii::$app->cache->getOrSet(
            [
                $query->createCommand($db)->rawSql,
                $cacheCondition,
            ],
            fn (): array => ArrayHelper::map(
                $query->asArray()->all($db),
                'boss_key',
                fn (array $row): array => $row,
            ),
            3600,
        );
    }
}
