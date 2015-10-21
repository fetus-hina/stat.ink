<?php
namespace app\models\query;

use yii\db\ActiveQuery;
use app\models\BattleFilterForm;
use app\models\BattleImageType;

class BattleQuery extends ActiveQuery
{
    public function hasResultImage()
    {
        return $this->innerJoinWith([
            'battleImages' => function ($query) {
                $query->onCondition(['{{battle_image}}.[[type_id]]' => BattleImageType::ID_RESULT]);
            },
        ], false);
    }

    public function filter(BattleFilterForm $filter)
    {
        return $this
            ->filterByScreenName($filter->screen_name)
            ->filterByLobby($filter->lobby)
            ->filterByRule($filter->rule)
            ->filterByMap($filter->map)
            ->filterByWeapon($filter->weapon)
            ->filterByResult($filter->result);
    }

    public function filterByScreenName($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        return $this->innerJoinWith('user')->andWhere(['{{user}}.[[screen_name]]' => $value]);
    }

    public function filterByLobby($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        $this->innerJoinWith('lobby');
        $this->andWhere(['{{lobby}}.[[key]]' => $value]);
        return $this;
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
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        return $this->innerJoinWith('map')->andWhere(['{{map}}.[[key]]' => $value]);
    }

    public function filterByWeapon($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return $this;
        }
        $this->innerJoinWith('weapon');
        switch (substr($value, 0, 1)) {
            default:
                $this->andWhere(['{{weapon}}.[[key]]' => $value]);
                break;

            case '@':
                $this->innerJoinWith('weapon.type');
                $this->andWhere(['{{weapon_type}}.[[key]]' => substr($value, 1)]);
                break;

            case '+':
                $this->innerJoinWith('weapon.subweapon');
                $this->andWhere(['{{subweapon}}.[[key]]' => substr($value, 1)]);
                break;

            case '*':
                $this->innerJoinWith('weapon.special');
                $this->andWhere(['{{special}}.[[key]]' => substr($value, 1)]);
                break;
        }
        return $this;
    }

    public function filterByResult($result)
    {
        if ($result === 'win' || $result === true) {
            $this->andWhere(['{{battle}}.[[is_win]]' => true]);
        } elseif ($result === 'lose' || $result === false) {
            $this->andWhere(['{{battle}}.[[is_win]]' => false]);
        }
        return $this;
    }

    public function getSummary()
    {
        return \app\components\helpers\BattleSummarizer::getSummary($this);
    }
}
