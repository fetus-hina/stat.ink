<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\query;

use app\models\Battle2FilterForm;
use app\models\Weapon2StageFilterForm;
use yii\db\ActiveQuery;

final class StatWeapon2ResultQuery extends ActiveQuery
{
    /**
     * @param Battle2FilterForm|Weapon2StageFilterForm $filter
     */
    public function applyFilter($filter): self
    {
        if ($filter instanceof Weapon2StageFilterForm) {
            return $this->applyStageFilter($filter);
        } elseif ($filter instanceof Battle2FilterForm) {
            return $this->applyBattleFilter($filter);
        } else {
            return $this;
        }
    }

    private function applyStageFilter(Weapon2StageFilterForm $filter): self
    {
        if ($filter->hasErrors()) {
            return $this;
        }
        if ($filter->rank != '') {
            $this->innerJoinWith('rank.group', false)
                ->andWhere([
                    'rank_group2.key' => $filter->rank,
                ]);
        }
        if ($filter->version != '') {
            $this->innerJoinWith('version', false)
                ->andWhere([
                    'splatoon_version2.tag' => $filter->version,
                ]);
        }
        return $this;
    }

    private function applyBattleFilter(Battle2FilterForm $filter): self
    {
        if ($filter->hasErrors()) {
            return $this;
        }

        if ($filter->map != '') {
            $this->innerJoinWith('map', false)
                ->andWhere(['map2.key' => $filter->map]);
        }

        if ($filter->rank != '') {
            if (substr($filter->rank, 0, 1) === '~') {
                $this->innerJoinWith('rank.group', false)
                    ->andWhere(['rank_group2.key' => substr($filter->rank, 1)]);
            } else {
                $this->innerJoinWith('rank', false)
                    ->andWhere(['rank2.key' => $filter->rank]);
            }
        }

        if ($filter->weapon != '') {
            if (substr($filter->weapon, 0, 1) === '@') {
                $this->innerJoinWith('weapon.type', false)
                    ->andWhere(['weapon_type2.key' => substr($filter->weapon, 1)]);
            } else {
                $this->innerJoinWith('weapon', false)
                    ->andWhere(['weapon2.key' => $filter->weapon]);
            }
        }

        if ($filter->term != '') {
            if (substr($filter->term, 0, 1) === 'v') {
                $this->innerJoinWith('version', false)
                    ->andWhere(['splatoon_version2.tag' => substr($filter->term, 1)]);
            } elseif (substr($filter->term, 0, 2) === '~v') {
                $this->innerJoinWith('version.group', false)
                    ->andWhere(['splatoon_version_group2.tag' => substr($filter->term, 2)]);
            }
        }

        return $this;
    }
}
