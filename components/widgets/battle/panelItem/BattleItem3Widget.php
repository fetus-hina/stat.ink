<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets\battle\panelItem;

use DateTimeImmutable;
use DateTimeZone;
use Yii;

use function sprintf;
use function vsprintf;

final class BattleItem3Widget extends BaseWidget
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
        return $this->model->is_knockout;
    }

    public function getIsWin(): ?bool
    {
        if ($result = $this->model->result) {
            return $result->is_win;
        }

        return null;
    }

    public function getIsDraw(): ?bool
    {
        if ($result = $this->model->result) {
            return $result->key === 'draw';
        }

        return null;
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
            'show-v3/battle',
            'screen_name' => $this->model->user->screen_name,
            'battle' => $this->model->uuid,
        ];
    }

    public function getMapName(): string
    {
        if (!$map = $this->model->map) {
            return Yii::t('app', 'Unknown');
        }
        return Yii::t('app-map3', $map->name);
    }

    public function getRuleName(): string
    {
        $rule = $this->model->rule;
        $lobby = $this->model->lobby;
        if (!$rule && !$lobby) {
            return Yii::t('app', 'Unknown');
        }

        return vsprintf('%s - %s', [
            $rule ? Yii::t('app-rule3', $rule->name) : Yii::t('app', 'Unknown'),
            $lobby ? Yii::t('app-lobby3', $lobby->name) : Yii::t('app', 'Unknown'),
        ]);
    }

    public function getWeaponName(): string
    {
        if (!$weapon = $this->model->weapon) {
            return Yii::t('app', 'Unknown');
        }
        return Yii::t('app-weapon3', $weapon->name);
    }

    public function getWeaponIcon(): ?string
    {
        return null;
    }

    public function getSubSpIcon(): ?string
    {
        return null;
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
