<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\downloadStats;

use Yii;
use app\models\Map;
use app\models\Rule;
use app\models\StatWeaponKDWinRate;
use app\models\Weapon;
use app\models\Language;
use yii\web\ViewAction;
use yii\db\Query;

class WeaponRuleMapAction extends ViewAction
{
    public function init()
    {
        $req = Yii::$app->request;
        $lang = $req->get('lang');
        if (is_scalar($lang)) {
            if ($obj = Language::findOne(['lang' => $lang])) {
                Yii::$app->language = $obj->lang;
            }
        }
    }

    public function run()
    {
        $resp = Yii::$app->response;
        $resp->setDownloadHeaders('weapon-rule-map.csv', 'text/cvs; charset=Shift_JIS', false, null);
        $resp->format = 'csv';
        return [
            'inputCharset' => 'UTF-8',
            'outputCharset' => 'CP932',
            'rows' => $this->generateData(),
        ];
    }

    public function generateData()
    {
        $query = (new Query())
            ->select([
                'weapon'    => "MAX({{weapon}}.[[key]])",
                'rule'      => "MAX({{rule}}.[[key]])",
                'map'       => "MAX({{map}}.[[key]])",
                'battle'    => "SUM({{stat_weapon_kd_win_rate}}.[[battle_count]])",
                'win'       => "SUM({{stat_weapon_kd_win_rate}}.[[win_count]])",
                'kill'      => "SUM({{stat_weapon_kd_win_rate}}.[[kill]])",
                'death'     => "SUM({{stat_weapon_kd_win_rate}}.[[death]])",
            ])
            ->from('stat_weapon_kd_win_rate')
            ->innerJoin('weapon', '{{stat_weapon_kd_win_rate}}.[[weapon_id]] = {{weapon}}.[[id]]')
            ->innerJoin('rule', '{{stat_weapon_kd_win_rate}}.[[rule_id]] = {{rule}}.[[id]]')
            ->innerJoin('map', '{{stat_weapon_kd_win_rate}}.[[map_id]] = {{map}}.[[id]]')
            ->groupBy([
                '{{stat_weapon_kd_win_rate}}.[[weapon_id]]',
                '{{stat_weapon_kd_win_rate}}.[[rule_id]]',
                '{{stat_weapon_kd_win_rate}}.[[map_id]]',
            ])
            ->having(['>', 'SUM({{stat_weapon_kd_win_rate}}.[[battle_count]])', 0])
            ->orderBy(implode(', ', [
                '{{stat_weapon_kd_win_rate}}.[[weapon_id]]',
                '{{stat_weapon_kd_win_rate}}.[[rule_id]]',
                '{{stat_weapon_kd_win_rate}}.[[map_id]]',
            ]));
        $dict = [
            'weapon' => $this->getWeapons(),
            'rule'   => $this->getRules(),
            'map'    => $this->getMaps(),
        ];
        
        return (function ($rows) use ($dict) {
            // header
            yield [
                '# weapon(key)',
                'weapon(name)',
                'mode(key)',
                'mode(name)',
                'stage(key)',
                'stage(name)',
                'battles',
                'win',
                'win rate',
                // 'avg kill',
                // 'avg death',
            ];

            foreach ($rows as $row) {
                yield [
                    $row['weapon'],
                    $dict['weapon'][$row['weapon']] ?? '',
                    $row['rule'],
                    $dict['rule'][$row['rule']] ?? '',
                    $row['map'],
                    $dict['map'][$row['map']] ?? '',
                    (string)(int)$row['battle'],
                    (string)(int)$row['win'],
                    sprintf('%.6f', (int)$row['win'] / (int)$row['battle']),
                    // sprintf('%.4f', (int)$row['kill'] / (int)$row['battle']),
                    // sprintf('%.4f', (int)$row['death'] / (int)$row['battle']),
                ];
            }
        })($query->createCommand()->queryAll());
    }

    public function getWeapons()
    {
        $ret = [];
        foreach (Weapon::find()->all() as $a) {
            $ret[$a->key] = Yii::t('app-weapon', $a->name);
        }
        return $ret;
    }

    public function getRules()
    {
        $ret = [];
        foreach (Rule::find()->all() as $a) {
            $ret[$a->key] = Yii::t('app-rule', $a->name);
        }
        return $ret;
    }

    public function getMaps()
    {
        $ret = [];
        foreach (Map::find()->all() as $a) {
            $ret[$a->key] = Yii::t('app-map', $a->name);
        }
        return $ret;
    }
}
