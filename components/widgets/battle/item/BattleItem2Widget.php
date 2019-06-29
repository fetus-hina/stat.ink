<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\battle\item;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\widgets\GameModeIcon;
use app\models\User;
use statink\yii2\stages\spl2\Spl2Stage;

class BattleItem2Widget extends BaseWidget
{
    public function getHasBattleEndAt(): bool
    {
        return !!$this->model->end_at;
    }

    public function getBattleEndAt(): ?DateTimeImmutable
    {
        if (!$endAt = $this->model->end_at) {
            return null;
        }
        return (new DateTimeImmutable($endAt))
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone));
    }

    public function getDescription(): string
    {
        $rule   = '?';
        $map    = '?';
        $result = '?';
        if ($this->model->rule) {
            $rule = Yii::t('app-rule2', $this->model->rule->name);
        }
        if ($this->model->map) {
            $map = Yii::t('app-map2', $this->model->map->name);
        }
        if ($this->model->is_win !== null) {
            $result = Yii::t('app', $this->model->is_win ? 'Won' : 'Lost');
        }
        return implode(' / ', [
            $rule,
            $map,
            $result,
        ]);
    }

    public function getImageUrl(): string
    {
        if ($this->model->battleImageResult) {
            return $this->model->battleImageResult->url;
        } elseif ($this->model->map && $this->model->is_win !== null) {
            return Spl2Stage::url(
                $this->model->is_win ? 'daytime-blur' : 'gray-blur',
                $this->model->map->key
            );
        } else {
            return $this->getImagePlaceholderUrl();
        }
    }

    public function getLinkRoute(): array
    {
        $user = $this->getUser();
        return [
            'show-v2/battle',
            'screen_name' => $user->screen_name,
            'battle' => $this->model->id,
        ];
    }

    public function getModeIcons(): array
    {
        $icons = [];
        if ($this->model->mode) {
            switch ($this->model->mode->key) {
                case 'regular':
                    $icons[] = GameModeIcon::spl2('nawabari');
                    return $icons;

                case 'fest':
                    $icons[] = GameModeIcon::spl2('fest');
                    return $icons;

                case 'gachi':
                    if ($this->model->lobby) {
                        if ($this->model->lobby->key === 'standard') {
                            $icons[] = GameModeIcon::spl2('gachi');
                        } else {
                            $icons[] = GameModeIcon::spl2('league');
                        }
                    }
                    break;

                case 'private':
                    $icons[] = GameModeIcon::spl2('private');
                    break;
            }
        }

        if ($this->model->rule) {
            $icons[] = GameModeIcon::spl2($this->model->rule->key);
        }

        return $icons;
    }

    public function getRuleKey(): string
    {
        if (!$rule = $this->model->rule) {
            return 'unknown';
        }
        return $rule->key;
    }

    public function getRuleName(): string
    {
        if (!$rule = $this->model->rule) {
            return Yii::t('app', 'Unknown');
        }
        return Yii::t('app-rule', $rule->name);
    }

    public function getUser(): User
    {
        return $this->model->user;
    }

    public function getUserLinkRoute(): array
    {
        $user = $this->getUser();
        return [
            'show-user/profile',
            'screen_name' => $user->screen_name,
        ];
    }
}
