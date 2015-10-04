<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\internal;

use Yii;
use yii\db\Query;

class StatByMapAction extends BaseStatAction
{
    protected function makeData()
    {
        $query = (new Query())
            ->select([
                'map_key'  => 'MAX({{map}}.[[key]])',
                'map_name' => 'MAX({{map}}.[[name]])',
                'result'    => '(CASE WHEN {{battle}}.[[is_win]] = TRUE THEN \'win\' ELSE \'lose\' END)',
                'count'     => 'COUNT(*)',
            ])
            ->from('battle')
            ->innerJoin('map', '{{battle}}.[[map_id]] = {{map}}.[[id]]')
            ->andWhere(['{{battle}}.[[user_id]]' => $this->user->id])
            ->andWhere(['in', '{{battle}}.[[is_win]]', [ true, false ]])
            ->groupBy(['{{battle}}.[[map_id]]', '{{battle}}.[[is_win]]']);
        $maps = [];
        foreach ($query->createCommand()->queryAll() as $row) {
            $row = (object)$row;
            if (!isset($maps[$row->map_key])) {
                $maps[$row->map_key] = [
                    'name' => $row->map_name,
                    'win' => 0,
                    'lose' => 0,
                ];
            }
            $maps[$row->map_key][$row->result] = (int)$row->count;
        }
        return $maps;
    }

    protected function decorate($maps)
    {
        foreach ($maps as &$map) {
            $map['name'] = Yii::t('app-map', $map['name']);
        }
        return $maps;
    }
}
