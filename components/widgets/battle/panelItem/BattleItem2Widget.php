<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets\battle\panelItem;

use DateTimeImmutable;
use DateTimeZone;
use Yii;

use function sprintf;

class BattleItem2Widget extends BaseWidget
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
        if ($this->model->isGachi) {
            return $this->model->is_knockout;
        }
        return null;
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
            'show-v2/battle',
            'screen_name' => $this->model->user->screen_name,
            'battle' => $this->model->id,
        ];
    }

    public function getMapName(): string
    {
        if (!$map = $this->model->map) {
            return Yii::t('app', 'Unknown');
        }
        return Yii::t('app-map2', $map->name);
    }

    public function getRuleName(): string
    {
        if (!$rule = $this->model->rule) {
            return Yii::t('app', 'Unknown');
        }
        return Yii::t('app-rule2', $rule->name);
    }

    public function getWeaponName(): string
    {
        if (!$weapon = $this->model->weapon) {
            return Yii::t('app', 'Unknown');
        }
        return Yii::t('app-weapon2', $weapon->name);
    }

    protected function renderKillDeathHtml(): string
    {
        if ($this->model->kill_or_assist !== null) {
            return sprintf(
                '%s: %d',
                Yii::t('app', 'Kill or Assist'),
                $this->model->kill_or_assist,
            );
        }
        return parent::renderKillDeathHtml();
    }
}
