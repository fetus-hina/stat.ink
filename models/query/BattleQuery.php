<?php
namespace app\models\query;

use yii\db\ActiveQuery;
use app\models\BattleFilterForm;

class BattleQuery extends ActiveQuery
{
    public function filter(BattleFilterForm $filter)
    {
        return $this
            ->filterByScreenName($filter->screen_name)
            ->filterByRule($filter->rule)
            ->filterByMap($filter->map)
            ->filterByWeapon($filter->weapon);
    }

    public function filterByScreenName($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        return $this->innerJoinWith('user')->andWhere(['{{user}}.[[screen_name]]' => $value]);
    }

    public function filterByRule($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        $this->innerJoinWith('rule');
        if (substr($value, 0, 1) === '@') {
            $this->innerJoinWith('rule.mode');
            $this->andWhere(['{{game_mode}}.[[key]]' => substr($value, 1)]);
        } else {
            $this->andWhere(['{{rule}}.[[key]]' => $value]);
        }
        return $this;
    }

    public function filterByMap($value)
    {
        return $this;
    }

    public function filterByWeapon($value)
    {
        return $this;
    }
}
