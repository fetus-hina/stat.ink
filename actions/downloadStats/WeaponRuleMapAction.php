<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\downloadStats;

use Yii;
use app\components\web\CsvResponseFormatter;
use app\models\Charset;
use yii\db\Query;

class WeaponRuleMapAction extends BaseAction
{
    public function run()
    {
        if ($this->config->lang) {
            Yii::$app->language = $this->config->lang;
        }

        $charset = Charset::findOne(['php_name' => $this->config->charset ?: 'UTF-8']);

        $resp = Yii::$app->response;
        $resp->setDownloadHeaders('weapon-rule-map.csv', 'text/cvs', false, null);
        $resp->format = 'csv';
        return [
            'separator' => ($this->config->tsv ?? '0') == '1'
                    ? CsvResponseFormatter::SEPARATOR_TSV
                    : CsvResponseFormatter::SEPARATOR_CSV,
            'inputCharset' => 'UTF-8',
            'outputCharset' => $charset->php_name ?? 'UTF-8',
            'substituteCharacter' => $charset->substitute ?? ord('?'),
            'appendBOM' => ($this->config->bom ?? '0') == '1',
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
}
