<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v3\stats;

use LogicException;
use Yii;
use app\models\Battle3;
use app\models\Battle3FilterForm;
use app\models\MedalCanonical3;
use app\models\Rule3;
use app\models\User;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

use function array_merge;
use function assert;
use function hash_hmac;

use const SORT_ASC;

final class MedalAction extends Action
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

        $filter = Yii::createObject(Battle3FilterForm::class);
        $filter->load($_GET);
        $filter->validate();

        $data = Yii::$app->db->transaction(
            fn (Connection $db): array => [
                'filter' => $filter,
                'medals' => $this->getMedals($db),
                'rules' => $this->getRules($db),
                'stats' => $this->makeStats($db, $user, $filter),
                'user' => $user,
            ],
            Transaction::REPEATABLE_READ,
        );

        $c = $this->controller;
        assert($c instanceof Controller);
        return $c->render('stats/medal', $data);
    }

    /**
     * @return array<string, MedalCanonical3>
     */
    private function getMedals(Connection $db): array
    {
        return Yii::$app->cache->getOrSet(
            __METHOD__,
            fn () => ArrayHelper::map(
                MedalCanonical3::find()->all($db),
                'key',
                fn (MedalCanonical3 $model): MedalCanonical3 => $model,
            ),
            86400,
        );
    }

    /**
     * @return array<string, Rule3>
     */
    private function getRules(Connection $db): array
    {
        return Yii::$app->cache->getOrSet(
            __METHOD__,
            fn () => ArrayHelper::map(
                Rule3::find()->orderBy(['rank' => SORT_ASC])->all($db),
                'key',
                fn (Rule3 $model): Rule3 => $model,
            ),
            86400,
        );
    }

    /**
     * @return array<string, array<string, int>>
     */
    private function makeStats(Connection $db, User $user, Battle3FilterForm $filter): array
    {
        $query = Battle3::find()
            ->innerJoinWith(
                [
                    'medals',
                    'medals.canonical',
                    'rule',
                ],
                false,
            )
            ->andWhere([
                '{{%battle3}}.[[user_id]]' => $user->id,
                '{{%battle3}}.[[is_deleted]]' => false,
            ])
            ->select([
                'medal_key' => 'MAX({{%medal_canonical3}}.[[key]])',
                'rule_key' => 'MAX({{%rule3}}.[[key]])',
                'count' => 'COUNT(*)',
            ])
            ->groupBy([
                '{{%medal_canonical3}}.[[id]]',
                '{{%rule3}}.[[id]]',
            ]);

        $filter->decorateQuery($query);

        $cacheKey = hash_hmac(
            'sha256',
            Json::encode(
                array_merge(
                    ['sql' => $query->createCommand($db)->rawSql],
                    (new Query())
                        ->select([
                            'max' => 'MAX([[id]])',
                            'count' => 'COUNT(*)',
                        ])
                        ->from('{{%battle3}}')
                        ->andWhere([
                            'user_id' => $user->id,
                            'is_deleted' => false,
                        ])
                        ->one($db),
                ),
            ),
            __METHOD__,
        );

        return Yii::$app->cache->getOrSet(
            $cacheKey,
            function () use ($db, $query): array {
                $results = [];
                foreach ($query->asArray()->all($db) as $row) {
                    if (!isset($results[$row['medal_key']])) {
                        $results[$row['medal_key']] = [];
                    }

                    $results[$row['medal_key']][$row['rule_key']] = (int)$row['count'];
                }

                return $results;
            },
            3600,
        );
    }
}
