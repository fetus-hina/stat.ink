<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\query;

use app\models\KDWin2FilterForm;
use app\models\Map2;
use app\models\RankGroup2;
use app\models\Rule2;
use app\models\SplatoonVersionGroup2;
use app\models\WeaponType2;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

final class StatWeapon2KdWinRateQuery extends ActiveQuery
{
    public function applyFilter(KDWin2FilterForm $form): self
    {
        if ($form->hasErrors()) {
            return $this->alwaysFalse();
        }

        $this->filterStage($form->map);
        $this->filterRank($form->rank);
        $this->filterWeapon($form->weapon);
        $this->filterVersion($form->version);

        return $this;
    }

    public function alwaysFalse(): self
    {
        $this->andWhere('0 = 1');
        return $this;
    }

    public function filterStage(?string $key): self
    {
        if ($key === null || $key === '') {
            return $this;
        }

        if ($key === 'mystery') {
            $this->andWhere([
                'map_id' => ArrayHelper::getColumn(
                    Map2::find()
                        ->andWhere(['like', 'key', 'mystery%', false])
                        ->asArray()
                        ->all(),
                    'id',
                ),
            ]);
        } else {
            $model = Map2::findOne(['key' => $key]);
            if (!$model) {
                return $this->alwaysFalse();
            }
            $this->andWhere(['map_id' => $model->id]);
        }

        return $this;
    }

    public function filterRank(?string $key): self
    {
        if ($key === null || $key === '') {
            return $this;
        }

        $model = RankGroup2::findOne(['key' => $key]);
        if (!$model) {
            return $this->alwaysFalse();
        }
        $this->andWhere(['rank_group_id' => $model->id]);

        $model = Rule2::findOne(['key' => 'nawabari']);
        if ($model) {
            $this->andWhere(['<>', 'rule_id', $model->id]);
        }

        return $this;
    }

    public function filterWeapon(?string $key): self
    {
        if ($key === null || $key === '') {
            return $this;
        }

        $model = WeaponType2::findOne(['key' => $key]);
        if (!$model) {
            return $this->alwaysFalse();
        }
        $this->andWhere(['weapon_type_id' => $model->id]);

        return $this;
    }

    public function filterVersion(?string $vstr): self
    {
        if ($vstr === null || $vstr === '' || $vstr === '*') {
            return $this;
        }

        $v = SplatoonVersionGroup2::findOne(['tag' => $vstr]);
        if (!$v) {
            return $this->alwaysFalse();
        }

        $this->andWhere(['version_group_id' => $v->id]);
        return $this;
    }
}
