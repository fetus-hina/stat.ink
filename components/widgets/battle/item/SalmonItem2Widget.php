<?php

/**
 * @copyright Copyright (C) 2018 AIZAWA Hina
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

class SalmonItem2Widget extends BaseWidget
{
    public function getHasBattleEndAt(): bool
    {
        return !!$this->model->end_at || !!$this->model->start_at;
    }

    public function getBattleEndAt(): ?DateTimeImmutable
    {
        $at = $this->model->end_at ?? $this->model->start_at;
        if (!$at) {
            return null;
        }

        return (new DateTimeImmutable($at))
            ->setTimezone(new DateTimeZone(Yii::$app->timeZone));
    }

    public function getDescription(): string
    {
        $map = '?';
        $result = '?';
        if ($this->model->stage) {
            $map = Yii::t('app-salmon-map2', $this->model->stage->name);
        }

        if ($this->model->clear_waves !== null) {
            $result = Yii::t('app-salmon2', $this->model->isCleared ? 'Cleared' : 'Failed');
        }

        return implode(' / ', [
            Yii::t('app-salmon2', 'Salmon Run'),
            $map,
            $result,
        ]);
    }

    public function getImageUrl(): string
    {
        $isCleared = $this->model->isCleared;
        if ($isCleared !== null && $this->model->stage) {
            return Spl2Stage::url(
                $isCleared ? 'daytime-blur' : 'gray-blur',
                $this->model->stage->key
            );
        }

        return $this->getImagePlaceholderUrl();
    }

    public function getLinkRoute(): array
    {
        $user = $this->getUser();
        return ['salmon/view',
            'screen_name' => $user->screen_name,
            'id' => $this->model->id,
        ];
    }

    public function getModeIcons(): array
    {
        return [
            GameModeIcon::spl2('salmon'),
        ];
    }

    public function getRuleKey(): string
    {
        return 'salmon';
    }

    public function getRuleName(): string
    {
        return Yii::t('app-salmon2', 'Salmon Run');
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
