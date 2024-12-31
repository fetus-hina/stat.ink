<?php

/**
 * @copyright Copyright (C) 2017-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets\battle\panelItem;

use DateTimeImmutable;
use DateTimeZone;
use Yii;

class BattleItem1Widget extends BaseWidget
{
    public function getBattleEndAt(): ?DateTimeImmutable
    {
        if (!$endAt = $this->model->end_at) {
            return null;
        }
        return (new DateTimeImmutable($endAt))
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone));
    }

    public function getIsKO(): ?bool
    {
        return $this->model->is_knock_out;
    }

    public function getIsWin(): ?bool
    {
        return $this->model->is_win;
    }

    public function getKillDeath(): array
    {
        return [
            $this->model->kill,
            $this->model->death,
        ];
    }

    public function getLinkRoute(): array
    {
        return [
            'show/battle',
            'screen_name' => $this->model->user->screen_name,
            'battle' => $this->model->id,
        ];
    }

    public function getMapName(): string
    {
        if (!$map = $this->model->map) {
            return Yii::t('app', 'Unknown');
        }
        return Yii::t('app-map', $map->name);
    }

    public function getRuleName(): string
    {
        if (!$rule = $this->model->rule) {
            return Yii::t('app', 'Unknown');
        }
        return Yii::t('app-rule', $rule->name);
    }

    public function getWeaponName(): string
    {
        if (!$weapon = $this->model->weapon) {
            return Yii::t('app', 'Unknown');
        }
        return Yii::t('app-weapon', $weapon->name);
    }
}
