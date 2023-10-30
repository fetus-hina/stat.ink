<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\show\v2;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\Battle2;
use app\models\Spl2YearMonthForm;
use app\models\User;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

use function array_map;
use function implode;
use function sprintf;
use function strcmp;
use function time;
use function usort;

class UserStatReportAction extends BaseAction
{
    private $user;

    public function init()
    {
        $db = Yii::$app->db;
        $db->createCommand(
            $db->createCommand('SET TIMEZONE TO :tz')
                ->bindValue(':tz', Yii::$app->timeZone)
                ->rawSql,
        )->execute();

        $this->user = User::findOne(['screen_name' => Yii::$app->request->get('screen_name')]);
        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
    }

    public function run()
    {
        $now = (int)($_SERVER['REQUEST_TIME'] ?? time());
        $request = Yii::$app->request;

        $form = Yii::createObject(Spl2YearMonthForm::class);
        $form->attributes = $request->get();
        if (!$form->validate()) {
            $now = $form->getCurrentTimestamp();
            return $this->controller->redirect(['show-v2/user-stat-report',
                'screen_name' => $this->user->screen_name,
                'year' => (string)(int)$now->format('Y'),
                'month' => (string)(int)$now->format('n'),
            ]);
        }

        return $this->runMonth($form);
    }

    protected function runMonth(Spl2YearMonthForm $form)
    {
        $tz = new DateTimeZone(Yii::$app->timeZone);

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
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time());

        // クエリ可能な最小日時
        $lowerBound = (new DateTimeImmutable())
            ->setTimezone($tz)
            ->setTime(0, 0, 0)
            ->setDate(2017, 7, 1);

        return $this->controller->render('user-stat-report-month', [
            'user' => $this->user,
            'list' => $this->query($from, $to),
            'next' => $next <= $upperBound
                ? Url::to(['show-v2/user-stat-report',
                    'screen_name' => $this->user->screen_name,
                    'year' => $next->format('Y'),
                    'month' => $next->format('n'),
                ], true)
                : null,
            'prev' => $prev >= $lowerBound
                ? Url::to(['show-v2/user-stat-report',
                    'screen_name' => $this->user->screen_name,
                    'year' => $prev->format('Y'),
                    'month' => $prev->format('n'),
                ], true)
                : null,
        ]);
    }

    private function query(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $date = sprintf('(CASE %s END)::date', implode(' ', [
            'WHEN {{battle2}}.[[start_at]] IS NOT NULL THEN {{battle2}}.[[start_at]]',
            "WHEN {{battle2}}.[[end_at]] IS NOT NULL THEN {{battle2}}.[[end_at]] - '3 minutes'::interval",
            'WHEN {{battle2}}.[[period]] IS NOT NULL THEN PERIOD2_TO_TIMESTAMP({{battle2}}.[[period]])',
            "ELSE {{battle2}}.[[created_at]] - '4 minutes'::interval",
        ]));
        $query = Battle2::find() // {{{
            ->innerJoinWith([
                'lobby',
                'mode',
                'rule',
                'map',
                'weapon',
                'version',
            ], false)
            ->where(['and',
                ['{{battle2}}.[[user_id]]' => $this->user->id],
                ['not', ['{{battle2}}.[[is_win]]' => null]],
                ['not', ['{{battle2}}.[[lobby_id]]' => null]],
                ['not', ['{{battle2}}.[[mode_id]]' => null]],
                ['not', ['{{battle2}}.[[rule_id]]' => null]],
                ['not', ['{{battle2}}.[[map_id]]' => null]],
                ['not', ['{{battle2}}.[[weapon_id]]' => null]],
                ['<>', '{{lobby2}}.[[key]]', 'private'],
                ['between', $date, $from->format(DateTime::ATOM), $to->format(DateTime::ATOM)],
            ])
            ->groupBy([
                $date,
                '{{battle2}}.[[lobby_id]]',
                '{{battle2}}.[[mode_id]]',
                '{{battle2}}.[[rule_id]]',
                '{{battle2}}.[[my_team_id]]',
                '{{battle2}}.[[map_id]]',
                '{{battle2}}.[[weapon_id]]',
                '{{battle2}}.[[version_id]]',
            ])
            ->select([
                'date' => $date,
                'lobby_id' => '{{battle2}}.[[lobby_id]]',
                'lobby_key' => 'MAX({{lobby2}}.[[key]])',
                'mode_id' => '{{battle2}}.[[mode_id]]',
                'mode_key' => 'MAX({{mode2}}.[[key]])',
                'rule_id' => '{{battle2}}.[[rule_id]]',
                'rule_key' => 'MAX({{rule2}}.[[key]])',
                'rule_name' => 'MAX({{rule2}}.[[name]])',
                'team_id' => '{{battle2}}.[[my_team_id]]',
                'map_key' => 'MAX({{map2}}.[[key]])',
                'map_name' => 'MAX({{map2}}.[[name]])',
                'weapon_key' => 'MAX({{weapon2}}.[[key]])',
                'weapon_name' => 'MAX({{weapon2}}.[[name]])',
                'version_tag' => 'MAX({{splatoon_version2}}.[[tag]])',
                'version_name' => 'MAX({{splatoon_version2}}.[[name]])',
                'battles' => 'COUNT(*)',
                'wins' => 'SUM(CASE WHEN {{battle2}}.[[is_win]] THEN 1 ELSE 0 END)',
                'kills_for_ratio' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle2}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[death]] IS NULL THEN 0',
                    'ELSE {{battle2}}.[[kill]]',
                ])),
                'deaths_for_ratio' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle2}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[death]] IS NULL THEN 0',
                    'ELSE {{battle2}}.[[death]]',
                ])),
                'avg_kill_or_assist' => 'AVG({{battle2}}.[[kill_or_assist]])',
                'avg_kill' => 'AVG({{battle2}}.[[kill]])',
                'avg_death' => 'AVG({{battle2}}.[[death]])',
                'avg_special' => 'AVG({{battle2}}.[[special]])',
                'min_kill_or_assist' => 'MIN({{battle2}}.[[kill_or_assist]])',
                'min_kill' => 'MIN({{battle2}}.[[kill]])',
                'min_death' => 'MIN({{battle2}}.[[death]])',
                'min_special' => 'MIN({{battle2}}.[[special]])',
                'max_kill_or_assist' => 'MAX({{battle2}}.[[kill_or_assist]])',
                'max_kill' => 'MAX({{battle2}}.[[kill]])',
                'max_death' => 'MAX({{battle2}}.[[death]])',
                'max_special' => 'MAX({{battle2}}.[[special]])',
                'med_kill_or_assist' => 'PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {{battle2}}.[[kill_or_assist]])',
                'med_kill' => 'PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {{battle2}}.[[kill]])',
                'med_death' => 'PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {{battle2}}.[[death]])',
                'med_special' => 'PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {{battle2}}.[[special]])',
                'mod_kill_or_assist' => 'MODE() WITHIN GROUP (ORDER BY {{battle2}}.[[kill_or_assist]])',
                'mod_kill' => 'MODE() WITHIN GROUP (ORDER BY {{battle2}}.[[kill]])',
                'mod_death' => 'MODE() WITHIN GROUP (ORDER BY {{battle2}}.[[death]])',
                'mod_special' => 'MODE() WITHIN GROUP (ORDER BY {{battle2}}.[[special]])',
            ]);
        // }}}
        $list = array_map(
            function (array $row): array {
                $row['rule_name'] = Yii::t('app-rule2', $row['rule_name']);
                $row['map_name'] = Yii::t('app-map2', $row['map_name']);
                $row['weapon_name'] = Yii::t('app-weapon2', $row['weapon_name']);
                return $row;
            },
            $query->asArray()->all(),
        );
        usort($list, fn (array $a, array $b): int => strcmp($b['date'], $a['date'])
                ?: $a['lobby_id'] <=> $b['lobby_id']
                ?: $a['mode_id'] <=> $b['mode_id']
                ?: strcmp($a['team_id'], $b['team_id'])
                ?: $a['rule_id'] <=> $b['rule_id']
                ?: strcmp($a['map_name'], $b['map_name'])
                ?: strcmp($a['weapon_name'], $b['weapon_name']));
        return $list;
    }
}
