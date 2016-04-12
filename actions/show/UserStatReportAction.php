<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show;

use Yii;
use yii\base\DynamicModel;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\User;

class UserStatReportAction extends BaseAction
{
    private $user;

    public function init()
    {
        Yii::$app->db->createCommand(
            Yii::$app->db
                ->createCommand('SET TIMEZONE TO :tz')
                ->bindValue(':tz', Yii::$app->timeZone)
                ->rawSql
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
        $form = DynamicModel::validateData(
            [
                'year' => $request->get('year'),
                'month' => $request->get('month'),
            ],
            [
                [['year'], 'required'],
                [['year'], 'integer', 'min' => 2015, 'max' => date('Y', $now)],
                [['month'], 'integer', 'min' => 1, 'max' => 12]
            ]
        );
        if ($form->hasErrors()) {
            $this->controller->redirect(['show/user-stat-report',
                'screen_name' => $this->user->screen_name,
                'year' => date('Y', $now),
                'month' => date('n', $now),
            ]);
            return;
        }
        return $form->month
            ? $this->runMonth($form)
            : $this->runYear($form);
    }

    protected function runMonth($form)
    {
        $from = mktime(0, 0, 0, (int)$form->month, 1, (int)$form->year);
        $to = mktime(0, 0, -1, (int)$form->month + 1, 1, (int)$form->year);

        $next = mktime(24, 0, 0, date('n', $to), date('j', $to), date('Y', $to));
        $prev = mktime(0, 0, -1, date('n', $from), date('j', $from), date('Y', $from));
        $upperBound = $_SERVER['REQUEST_TIME'] ?? time();
        $lowerBound = strtotime('2015-09-01T00:00:00+09:00');
        return $this->controller->render('user-stat-report-month.tpl', [
            'user' => $this->user,
            'list' => $this->query(
                date('Y-m-d\TH:i:sP', $from),
                date('Y-m-d\TH:i:sP', $to),
                '{{battle}}.[[at]]::date'
            ),
            'next' => $next <= $upperBound
                ? Url::to(['show/user-stat-report',
                        'screen_name' => $this->user->screen_name,
                        'year' => date('Y', $next),
                        'month' => date('n', $next),
                    ], true)
                : null,
            'prev' => $prev >= $lowerBound
                ? Url::to(['show/user-stat-report',
                        'screen_name' => $this->user->screen_name,
                        'year' => date('Y', $prev),
                        'month' => date('n', $prev),
                    ], true)
                : null,
        ]);
    }

    protected function runYear($form)
    {
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        $from = mktime(0, 0, 0, 1, 1, (int)$form->year);
        $to = mktime(0, 0, -1, 1, 1, (int)$form->year + 1);
        $list = $this->query(
            date('Y-m-d\TH:i:sP', $from),
            date('Y-m-d\TH:i:sP', $to),
            "TO_CHAR({{battle}}.[[at]], 'YYYY-MM')"
        );
    }

    private function query($from, $to, $date)
    {
        $query = (new \yii\db\Query())
            ->select([
                'date'          => $date,
                'lobby_id'      => '{{battle}}.[[lobby_id]]',
                'lobby_key'     => 'MAX({{lobby}}.[[key]])',
                'lobby_name'    => 'MAX({{lobby}}.[[name]])',
                'rule_key'      => 'MAX({{rule}}.[[key]])',
                'rule_name'     => 'MAX({{rule}}.[[name]])',
                'map_key'       => 'MAX({{map}}.[[key]])',
                'map_name'      => 'MAX({{map}}.[[name]])',
                'weapon_key'    => 'MAX({{weapon}}.[[key]])',
                'weapon_name'   => 'MAX({{weapon}}.[[name]])',
                'battles'       => 'COUNT(*)',
                'wins'          => 'SUM(CASE WHEN {{battle}}.[[is_win]] THEN 1 ELSE 0 END)',
                'kill'          => 'SUM({{battle}}.[[kill]])',
                'death'         => 'SUM({{battle}}.[[death]])',
            ])
            ->from('battle')
            ->innerJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->innerJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->innerJoin('map', '{{battle}}.[[map_id]] = {{map}}.[[id]]')
            ->innerJoin('weapon', '{{battle}}.[[weapon_id]] = {{weapon}}.[[id]]')
            ->where(['and',
                ['{{battle}}.[[user_id]]' => $this->user->id],
                ['not', ['{{battle}}.[[is_win]]' => null]],
                ['not', ['{{battle}}.[[kill]]' => null]],
                ['not', ['{{battle}}.[[death]]' => null]],
                ['<>', '{{lobby}}.[[key]]', 'private'],
                ['between', '{{battle}}.[[at]]', $from, $to],
            ])
            ->groupBy([
                $date,
                '{{battle}}.[[lobby_id]]',
                '{{battle}}.[[rule_id]]',
                '{{battle}}.[[map_id]]',
                '{{battle}}.[[weapon_id]]',
            ]);
        $list = array_map(
            function ($row) {
                $row['lobby_name']  = Yii::t('app-rule', $row['lobby_name']);
                $row['rule_name']   = Yii::t('app-rule', $row['rule_name']);
                $row['map_name']    = Yii::t('app-map', $row['map_name']);
                $row['weapon_name'] = Yii::t('app-weapon', $row['weapon_name']);
                return $row;
            },
            $query->createCommand()->queryAll()
        );
        usort($list, function ($a, $b) {
            return strcmp($b['date'], $a['date'])
                ?: $a['lobby_id'] <=> $b['lobby_id']
                ?: strcmp($a['rule_name'], $b['rule_name'])
                ?: strcmp($a['map_name'], $b['map_name'])
                ?: strcmp($a['weapon_name'], $b['weapon_name']);
        });
        return $list;
    }
}
