<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v2;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\helpers\T;
use app\models\Battle2;
use app\models\Spl2YearMonthForm;
use app\models\User;
use stdClass;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

final class UserStatMonthlyReportAction extends Action
{
    private ?User $user;

    public function init()
    {
        $db = Yii::$app->db;
        $db->createCommand("SET TIMEZONE TO 'Etc/UTC'")->execute();

        $this->user = User::findOne(['screen_name' => Yii::$app->request->get('screen_name')]);
        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    public function run()
    {
        $form = Yii::createObject([
            'class' => Spl2YearMonthForm::class,
            'timeZone' => new DateTimeZone('Etc/UTC'),
        ]);
        $form->attributes = Yii::$app->request->get();
        if (!$form->validate()) {
            $now = $form->getCurrentTimestamp();
            return T::webController($this->controller)
                ->redirect(['show-v2/user-stat-monthly-report',
                    'screen_name' => $this->user->screen_name,
                    'year' => (string)(int)$now->format('Y'),
                    'month' => (string)(int)$now->format('n'),
                ]);
        }

        $dates = $this->calcDates($form);
        return $this->controller->render('user-stat-monthly-report', [
            'user' => $this->user,
            'year' => (int)$form->year,
            'month' => (int)$form->month,
            'next' => $dates->nextMonth <= $dates->queryUpperBound
                ? Url::to(
                    ['show-v2/user-stat-monthly-report',
                        'screen_name' => $this->user->screen_name,
                        'year' => $dates->nextMonth->format('Y'),
                        'month' => $dates->nextMonth->format('n'),
                    ],
                    true
                )
                : null,
            'prev' => $dates->prevMonth >= $dates->queryLowerBound
                ? Url::to(
                    ['show-v2/user-stat-monthly-report',
                        'screen_name' => $this->user->screen_name,
                        'year' => $dates->prevMonth->format('Y'),
                        'month' => $dates->prevMonth->format('n'),
                    ],
                    true
                )
                : null,
            'abstract' => $this->queryAbstract($dates->from, $dates->to),
            'rulesAndStages' => $this->queryRulesAndStages($dates->from, $dates->to),
        ]);
    }

    private function calcDates(Spl2YearMonthForm $form): stdClass
    {
        $tz = new DateTimeZone('Etc/UTC');

        // 指定月初
        $from = (new DateTimeImmutable())
            ->setTimezone($tz)
            ->setTime(0, 0, 0)
            ->setDate((int)$form->year, (int)$form->month, 1);

        // 指定月末（翌月の直前を求める）
        $to = $from->add(new DateInterval('P1M'))
            ->sub(new DateInterval('PT1S'));

        // 指定の翌月・前月
        $next = $from->add(new DateInterval('P1M'));
        $prev = $from->sub(new DateInterval('P1M'));

        // クエリ可能な最大日時
        $upperBound = (new DateTimeImmutable())
            ->setTimezone($tz)
            ->setTimestamp((int)($_SERVER['REQUEST_TIME'] ?? time()));

        // クエリ可能な最小日時
        $lowerBound = (new DateTimeImmutable())
            ->setTimezone($tz)
            ->setTime(0, 0, 0)
            ->setDate(2017, 7, 1);

        return (object)[
            'from' => $from,
            'to' => $to,
            'nextMonth' => $next,
            'prevMonth' => $prev,
            'queryUpperBound' => $upperBound,
            'queryLowerBound' => $lowerBound,
        ];
    }

    private function queryAbstract(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $time = sprintf('(CASE %s END)', implode(' ', [
            'WHEN {{battle2}}.[[start_at]] IS NOT NULL THEN {{battle2}}.[[start_at]]',
            "WHEN {{battle2}}.[[end_at]] IS NOT NULL THEN {{battle2}}.[[end_at]] - '3 minutes'::interval",
            'WHEN {{battle2}}.[[period]] IS NOT NULL THEN PERIOD2_TO_TIMESTAMP({{battle2}}.[[period]])',
            "ELSE {{battle2}}.[[created_at]] - '4 minutes'::interval",
        ]));

        $group = sprintf('(CASE %s END)', implode(' ', [
            "WHEN {{lobby2}}.[[key]] = 'private' THEN 'private'",
            "WHEN {{rule2}}.[[key]] = 'nawabari' THEN 'nawabari'",
            "WHEN {{mode2}}.[[key]] = 'gachi' AND {{lobby2}}.[[key]] = 'standard' THEN 'gachi'",
            "WHEN {{mode2}}.[[key]] = 'gachi' AND {{lobby2}}.[[key]] IN ('squad_2', 'squad_4') THEN 'league'",
            'ELSE NULL',
        ]));

        $query = Battle2::find()
            ->innerJoinWith([
                'lobby',
                'mode',
                'rule',
            ], false)
            ->where(['and',
                ['{{battle2}}.[[user_id]]' => $this->user->id],
                ['not', ['{{battle2}}.[[is_win]]' => null]],
                ['not', ['{{battle2}}.[[lobby_id]]' => null]],
                ['not', ['{{battle2}}.[[mode_id]]' => null]],
                ['not', ['{{battle2}}.[[rule_id]]' => null]],
                ['between', $time, $from->format(DateTime::ATOM), $to->format(DateTime::ATOM)],
            ])
            ->andWhere("{$group} IS NOT NULL")
            ->groupBy([$group])
            ->select([
                'group' => $group,
                'battles' => 'COUNT(*)',
                'wins' => 'SUM(CASE WHEN {{battle2}}.[[is_win]] THEN 1 ELSE 0 END)',
            ]);

        return ArrayHelper::map(
            $query->asArray()->all(),
            'group',
            fn ($row) => (object)[
                'battles' => (int)$row['battles'],
                'wins' => (int)$row['wins'],
                'loses' => (int)$row['battles'] - (int)$row['wins'],
            ],
        );
    }

    private function queryRulesAndStages(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $time = sprintf('(CASE %s END)', implode(' ', [
            'WHEN {{battle2}}.[[start_at]] IS NOT NULL THEN {{battle2}}.[[start_at]]',
            "WHEN {{battle2}}.[[end_at]] IS NOT NULL THEN {{battle2}}.[[end_at]] - '3 minutes'::interval",
            'WHEN {{battle2}}.[[period]] IS NOT NULL THEN PERIOD2_TO_TIMESTAMP({{battle2}}.[[period]])',
            "ELSE {{battle2}}.[[created_at]] - '4 minutes'::interval",
        ]));

        $group = sprintf('(CASE %s END)', implode(' ', [
            "WHEN {{lobby2}}.[[key]] = 'private' THEN 'private'",
            "WHEN {{rule2}}.[[key]] = 'nawabari' THEN 'nawabari'",
            "WHEN {{mode2}}.[[key]] = 'gachi' AND {{lobby2}}.[[key]] = 'standard' THEN 'gachi'",
            "WHEN {{mode2}}.[[key]] = 'gachi' AND {{lobby2}}.[[key]] = 'squad_2' THEN 'league2'",
            "WHEN {{mode2}}.[[key]] = 'gachi' AND {{lobby2}}.[[key]] = 'squad_4' THEN 'league4'",
            'ELSE NULL',
        ]));

        $query = Battle2::find()
            ->innerJoinWith([
                'lobby',
                'mode',
                'rule',
                'map',
            ], false)
            ->where(['and',
                ['{{battle2}}.[[user_id]]' => $this->user->id],
                ['not', ['{{battle2}}.[[is_win]]' => null]],
                ['not', ['{{battle2}}.[[lobby_id]]' => null]],
                ['not', ['{{battle2}}.[[map_id]]' => null]],
                ['not', ['{{battle2}}.[[mode_id]]' => null]],
                ['not', ['{{battle2}}.[[rule_id]]' => null]],
                ['between', $time, $from->format(DateTime::ATOM), $to->format(DateTime::ATOM)],
            ])
            ->andWhere("{$group} IS NOT NULL")
            ->groupBy([
                $group,
                '{{battle2}}.[[rule_id]]',
                '{{battle2}}.[[map_id]]',
            ])
            ->select([
                'mode' => $group,
                'rule_key' => 'MAX({{rule2}}.[[key]])',
                'rule_name' => 'MAX({{rule2}}.[[name]])',
                'map_key' => 'MAX({{map2}}.[[key]])',
                'map_name' => 'MAX({{map2}}.[[name]])',
                'battles' => 'COUNT(*)',
                'wins' => 'SUM(CASE WHEN {{battle2}}.[[is_win]] THEN 1 ELSE 0 END)',
            ]);

        /**
         * @var array{
         *   battles: numeric,
         *   map_key: string,
         *   map_name: string,
         *   mode: 'gachi'|'league2'|'league4'|'nawabari'|'private',
         *   rule_key: string,
         *   rule_name: string,
         *   wins: numeric,
         * }[] $list
         */
        $list = $query->asArray()->all();

        $results = [];
        foreach ($list as $row) {
            $mode = $row['mode'];
            $rule = $row['rule_key'];
            $map = $row['map_key'];

            if (!isset($results[$mode])) {
                $results[$mode] = [
                    'key' => $mode,
                    'rules' => [],
                ];
            }

            if (!isset($results[$mode]['rules'][$rule])) {
                $results[$mode]['rules'][$rule] = [
                    'key' => $rule,
                    'name' => $row['rule_name'],
                    'maps' => [],
                ];
            }

            $results[$mode]['rules'][$rule]['maps'][$map] = [
                'key' => $map,
                'name' => $row['map_name'],
                'battles' => (int)$row['battles'],
                'wins' => (int)$row['wins'],
            ];
        }

        return $results;
    }
}
