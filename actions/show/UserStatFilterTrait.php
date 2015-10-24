<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show;

use yii\db\Query;
use app\models\BattleFilterForm;

trait UserStatFilterTrait
{
    public function filter(Query $query, BattleFilterForm $filter)
    {
        return $this
            ->filterByLobby($query, $filter->lobby)
            ->filterByRule($query, $filter->rule)
            ->filterByMap($query, $filter->map)
            ->filterByWeapon($query, $filter->weapon)
            ->filterByResult($query, $filter->result)
            ->filterByTerm($query, $filter->term, [
                'from' => $filter->term_from,
                'to' => $filter->term_to,
            ]);
    }

    public function filterByLobby($query, $value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        $query->andWhere(['{{lobby}}.[[key]]' => $value]);
        return $this;
    }

    public function filterByRule($query, $value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        if (substr($value, 0, 1) === '@') {
            $query->andWhere(['{{game_mode}}.[[key]]' => substr($value, 1)]);
        } else {
            $query->andWhere(['{{rule}}.[[key]]' => $value]);
        }
        return $this;
    }

    public function filterByMap($query, $value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        $query->andWhere(['{{map}}.[[key]]' => $value]);
        return $this;
    }

    public function filterByWeapon($query, $value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        switch (substr($value, 0, 1)) {
            default:
                $query->andWhere(['{{weapon}}.[[key]]' => $value]);
                break;

            case '@':
                $query->andWhere(['{{weapon_type}}.[[key]]' => substr($value, 1)]);
                break;

            case '+':
                $query->andWhere(['{{subweapon}}.[[key]]' => substr($value, 1)]);
                break;

            case '*':
                $query->andWhere(['{{special}}.[[key]]' => substr($value, 1)]);
                break;
        }
        return $this;
    }

    public function filterByResult($query, $result)
    {
        if ($result === 'win' || $result === true) {
            $query->andWhere(['{{battle}}.[[is_win]]' => true]);
        } elseif ($result === 'lose' || $result === false) {
            $query->andWhere(['{{battle}}.[[is_win]]' => false]);
        }
        return $this;
    }

    public function filterByTerm($query, $value, array $options = [])
    {
        $now = @$_SERVER['REQUEST_TIME'] ?: time();
        $currentPeriod = \app\components\helpers\Battle::calcPeriod($now);

        switch ($value) {
            case 'this-period':
                $query->andWhere(['{{battle}}.[[period]]' => $currentPeriod]);
                break;
                
            case 'last-period':
                $query->andWhere(['{{battle}}.[[period]]' => $currentPeriod - 1]);
                break;
                
            case '24h':
                $query->andWhere(['>=', '{{battle}}.[[at]]', gmdate('Y-m-d\TH:i:sP', $now - 86400)]);
                break;

            case 'today':
                $t = mktime(0, 0, 0, date('n', $now), date('j', $now), date('Y', $now));
                $query->andWhere(['>=', '{{battle}}.[[at]]', gmdate('Y-m-d\TH:i:sP', $t)]);
                break;

            case 'yesterday':
                // 昨日の 00:00:00
                $t1 = mktime(0, 0, 0, date('n', $now), date('j', $now) - 1, date('Y', $now));
                // 今日の 00:00:00
                $t2 = mktime(0, 0, 0, date('n', $now), date('j', $now), date('Y', $now));
                $query->andWhere(['>=', '{{battle}}.[[at]]', gmdate('Y-m-d\TH:i:sP', $t1)]);
                $query->andWhere(['<', '{{battle}}.[[at]]', gmdate('Y-m-d\TH:i:sP', $t2)]);
                break;

            case 'term':
                if (isset($options['from']) && $options['from'] != '') {
                    if ($t = @strtotime($options['from'])) {
                        $query->andWhere(['>=', '{{battle}}.[[at]]', gmdate('Y-m-d\TH:i:sP', $t)]);
                    }
                }
                if (isset($options['to']) && $options['to'] != '') {
                    if ($t = @strtotime($options['to'])) {
                        $query->andWhere(['<=', '{{battle}}.[[at]]', gmdate('Y-m-d\TH:i:sP', $t)]);
                    }
                }
                break;
        }
        return $this;
    }
}
