<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\stage;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\GameMode;
use app\models\Map;
use app\models\PeriodMap;
use yii\base\DynamicModel;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class MonthAction extends BaseAction
{
    public $year;
    public $month;
    public $periodS;
    public $periodE;

    public function init()
    {
        parent::init();

        // タイムゾーンは UTC+6 にする
        Yii::$app->timeZone = 'Etc/GMT-6';
        Yii::$app->db->createCommand("SET timezone TO 'UTC-6'")->execute();

        $this->prepare();
    }

    private function prepare() // {{{
    {
        $req = Yii::$app->request;

        $this->year = $req->get('year');
        $this->month = $req->get('month');
        if (!is_scalar($this->year) ||
                !is_scalar($this->month) ||
                !static::isValidMonth($this->year, $this->month)
        ) {
            static::http404();
            return;
        }
        $this->periodS = BattleHelper::calcPeriod(mktime(0, 0, 0, $this->month, 1, $this->year));
        $this->periodE = BattleHelper::calcPeriod(mktime(0, 0, -1, $this->month + 1, 1, $this->year));
    } // }}}

    public function run()
    {
        return $this->controller->render('month.tpl', [
            'rules' => $this->buildData(),
            'prevUrl' => $this->prevMonthUrl,
            'nextUrl' => $this->nextMonthUrl,
            'month' => (new DateTimeImmutable())
                            ->setTimezone(new DateTimeZone(Yii::$app->timeZone))
                            ->setDate($this->year, $this->month, 1)
                            ->setTime(0, 0, 0),
        ]);
    }

    public function buildData() : array // {{{
    {
        $rules = $this->getRules();
        $maps = $this->getMaps();
        $counts = $this->getCountData();
        $data = [];
        foreach ($rules as $rule) {
            $data[] = (function () use ($rule, $maps, $counts) {
                $ret = (object)[
                    'rule' => $rule,
                    'maps' => array_map(
                        function ($map) use ($rule, $counts) {
                            $counts_ = array_filter($counts, function ($_) use ($rule, $map) {
                                return $_['rule_id'] == $rule->id && $_['map_id'] == $map->id;
                            });
                            return (object)[
                                'map' => $map,
                                'count' => $counts_ ? (int)array_shift($counts_)['count'] : 0,
                            ];
                        },
                        $maps
                    ),
                ];
                usort($ret->maps, function ($a, $b) {
                    return $b->count <=> $a->count
                        ?: strnatcasecmp(
                            Yii::t('app-map', $a->map->name),
                            Yii::t('app-map', $b->map->name)
                        );
                });
                return $ret;
            })();
        }
        return $data;
    } // }}}

    public function getMaps() : array // {{{
    {
        $q = Map::find()
            ->andWhere(['<=', 'release_at', date(
                'Y-m-d\TH:i:sP',
                BattleHelper::periodToRange($this->periodE)[1]
            )
            ]);
        $ret = [];
        foreach ($q->all() as $_) {
            $ret[$_->id] = $_;
        }
        return $ret;
    } // }}}

    public function getRules() : array // {{{
    {
        $ret = [];
        foreach (GameMode::find()->orderBy('id ASC')->all() as $mode) {
            $tmp = $mode->rules;
            usort($tmp, function ($a, $b) {
                return strnatcasecmp(
                    Yii::t('app-rule', $a->name),
                    Yii::t('app-rule', $b->name)
                );
            });
            foreach ($tmp as $o) {
                $ret[] = $o;
            }
        }
        return $ret;
    } // }}}

    public function getCountData() : array // {{{
    {
        $query = (new Query())
            ->select([
                'rule_id' => '{{period_map}}.[[rule_id]]',
                'map_id' => '{{period_map}}.[[map_id]]',
                'count' => 'COUNT(*)',
            ])
            ->from('period_map')
            ->where(['between', '{{period_map}}.[[period]]', $this->periodS, $this->periodE])
            ->groupBy(['{{period_map}}.[[rule_id]]', '{{period_map}}.[[map_id]]']);
        return $query->all();
    } // }}}

    public function getPrevMonthUrl() // {{{
    {
        return $this->getRelativeMonthUrl(-1);
    } // }}}

    public function getNextMonthUrl() // {{{
    {
        return $this->getRelativeMonthUrl(1);
    } // }}}

    public function getRelativeMonthUrl(int $rel) // {{{
    {
        $t = mktime(0, 0, 0, $this->month + $rel, 1, $this->year);
        $y = (int)date('Y', $t);
        $m = (int)date('n', $t);
        return static::isValidMonth($y, $m)
            ? Url::to(['stage/month', 'year' => $y, 'month' => $m])
            : null;
    } // }}}

    private static function hasStageData(int $periodS, int $periodE) : bool // {{{
    {
        return !!PeriodMap::find()
            ->select(['id' => '{{period_map}}.[[id]]'])
            ->andWhere(['between', '{{period_map}}.[[period]]', $periodS, $periodE])
            ->asArray()
            ->one();
    } // }}}

    private static function http404()
    {
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    private static function isValidMonth($year, $month) : bool // {{{
    {
        $now = (int)($_SERVER['REQUEST_TIME'] ?? time());

        if (!preg_match('/^\d+$/', $year) ||
                !preg_match('/^\d+$/', $month) ||
                $year < 2015 ||
                $year > date('Y', $now) ||
                $month < 1 ||
                $month > 12
        ) {
            return false;
        }

        // 今年なら今月以前のはず
        if (((int)$year === (int)date('Y', $now)) &&
                ((int)$month > (int)date('n', $now))
        ) {
            return false;
        }

        // データ持ってないかも
        $periodS = BattleHelper::calcPeriod(mktime(0, 0, 0, $month, 1, $year));
        $periodE = BattleHelper::calcPeriod(mktime(0, 0, -1, $month + 1, 1, $year));
        if (!static::hasStageData($periodS, $periodE)) {
            return false;
        }

        return true;
    } // }}}
}
