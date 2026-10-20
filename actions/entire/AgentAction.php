<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\entire;

use DateInterval;
use DateTime;
use DateTimeZone;
use Override;
use ParagonIE\ConstantTime\Base32;
use RangeException;
use Yii;
use app\models\AgentGroup;
use app\models\EntireAgentForm;
use app\models\StatAgentUser;
use yii\base\Action;
use yii\web\NotFoundHttpException;

use function array_keys;
use function array_values;
use function ksort;
use function max;
use function min;

use const SORT_ASC;

/**
 * @property-read array<int, array<string, mixed>> $combineds
 * @property-read list<array{date: string, battle: int, user: int}> $postStats
 */
final class AgentAction extends Action
{
    public ?EntireAgentForm $form = null;

    #[Override]
    public function init(): void
    {
        parent::init();

        Yii::$app->db
            ->createCommand("SET timezone TO 'UTC-6'")
            ->execute();

        $form = new EntireAgentForm(['b32name' => Yii::$app->request->get('b32name')]);
        if (!$form->validate()) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        $this->form = $form;
    }

    public function run(): string
    {
        try {
            $name = Base32::decode($this->form->b32name);
        } catch (RangeException $e) {
            $name = '';
        }
        return $this->controller->render('agent', [
            'name' => $name,
            'posts' => $this->postStats,
            'combineds' => $this->combineds,
        ]);
    }

    /**
     * @return list<array{date: string, battle: int, user: int}>
     */
    public function getPostStats(): array
    {
        try {
            $list = StatAgentUser::find()
                ->andWhere(['agent' => Base32::decode($this->form->b32name)])
                ->orderBy(['date' => SORT_ASC])
                ->asArray()
                ->all();
        } catch (RangeException $e) {
            $list = [];
        }
        $ret = [];
        foreach ($list as $a) {
            $ret[$a['date']] = [
                'date' => $a['date'],
                'battle' => (int)$a['battle_count'],
                'user' => (int)$a['user_count'],
            ];
        }

        if ($ret === []) {
            return [];
        }

        // 歯抜けデータの処理
        $minDate = $ret |> array_keys(...) |> min(...);
        $maxDate = $ret |> array_keys(...) |> max(...);
        if ($minDate !== $maxDate) {
            $min = new DateTime($minDate, new DateTimeZone('Etc/GMT-6'));
            $max = new DateTime($maxDate, new DateTimeZone('Etc/GMT-6'));
            while ($min->format('U') < $max->format('U')) {
                $min->add(new DateInterval('P1D'));
                $d = $min->format('Y-m-d');
                if (!isset($ret[$d])) {
                    $ret[$d] = [
                        'date' => $d,
                        'battle' => 0,
                        'user' => 0,
                    ];
                }
            }
        }
        ksort($ret);
        return array_values($ret);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getCombineds(): array
    {
        try {
            return AgentGroup::find()
                ->innerJoinWith([
                    'agentGroupMaps' => function ($q) {
                        $q->orderBy(null);
                    },
                ], false)
                ->where([
                    '{{agent_group_map}}.[[agent_name]]' => Base32::decode($this->form->b32name),
                ])
                ->orderBy(['{{agent_group}}.[[name]]' => SORT_ASC])
                ->asArray()
                ->all();
        } catch (RangeException $e) {
            return [];
        }
    }
}
