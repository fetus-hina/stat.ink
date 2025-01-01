<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\IrasutoyaAsset;
use app\models\User;
use statink\yii2\anonymizer\AnonymizerAsset;
use yii\base\Widget;
use yii\bootstrap\Html;

use function hash;
use function hex2bin;
use function implode;
use function preg_match;
use function str_repeat;
use function substr;
use function trim;
use function vsprintf;

class PlayerName2Widget extends Widget
{
    public $player;
    public $user;
    public $nameOnly = false;
    public $isMyTeam = false; // 自チームかつ4人チームマッチ
    public $isPrivate = false;

    public function run()
    {
        $this->view->registerCss(vsprintf('#%s{%s}', [
            $this->id,
            Html::cssStyleFromArray([
                'display' => 'flex',
                'align-items' => 'center',
                'justify-content' => 'space-between',
                'white-space' => 'nowrap',
            ]),
        ]));

        return Html::tag(
            'div',
            $this->nameOnly
                ? $this->renderName($this->player->user ?? null)
                : $this->renderNamePart() . $this->renderSpeciesPart(),
            ['id' => $this->id],
        );
    }

    private function renderNamePart(): string
    {
        $playerUser = $this->player->user ?? null;
        return $playerUser
            ? Html::a(
                $this->renderInnerNamePart($playerUser),
                ['show-user/profile', 'screen_name' => $playerUser->screen_name],
            )
            : Html::tag('span', $this->renderInnerNamePart($playerUser));
    }

    private function renderInnerNamePart(?User $user): string
    {
        return implode(' ', [
            $this->renderIdenticon($user),
            $this->renderTopPlayer($user),
            $this->renderName($user),
        ]);
    }

    private function renderIdenticon(?User $user): string
    {
        if (!$url = $this->player->iconUrl) {
            return '';
        }

        $splatnetId = trim($this->player->splatnet_id);
        return Html::img(
            $url,
            [
                'class' => 'auto-tooltip',
                'title' => $splatnetId !== '' ? $splatnetId : '',
                'style' => [
                    'width' => '1.2em',
                    'height' => 'auto',
                ],
            ],
        );
    }

    private function renderTopPlayer(?User $user): string
    {
        if (!$this->player->top_500 ?? null) {
            return '';
        }

        return Html::tag('span', '', [
            'class' => 'fas fa-fw fa-chess-queen',
        ]);
    }

    private function renderName(?User $playerUser): string
    {
        // {{{
        $anonymize = $this->shouldAnonymize($playerUser);
        if ($anonymize) {
            AnonymizerAsset::register($this->view);
            $anonId = substr(
                hash(
                    'sha256',
                    preg_match('/^([0-9a-f]{2}+)[0-9a-f]?$/', $this->player->anonymizeSeed, $match)
                        ? hex2bin($match[1])
                        : $this->player->anonymizeSeed,
                ),
                0,
                40,
            );

            return Html::tag(
                'span',
                Html::encode(str_repeat('*', 10)),
                [
                    'title' => Yii::t('app', 'Anonymized'),
                    'class' => 'auto-tooltip anonymize',
                    'data' => [
                        'anonymize' => $anonId,
                    ],
                    'style' => [
                        'display' => 'inline-block',
                        'white-space' => 'nowrap',
                        'overflow' => 'hidden!important',
                        'text-overflow' => 'ellipsis',
                        'max-width' => 'calc(100% - 2.8rem)',
                    ],
                ],
            );
        } else {
            return Html::encode(trim($this->player->name));
        }
        // }}}
    }

    private function shouldAnonymize(?User $playerUser): bool
    {
        //TODO: $playerUser->link_mode_id を見て匿名化するか決める
        // {{{
        if ($this->player->is_me) {
            // プレーヤーのデータは常時表示
            return false;
        } elseif ($this->player->isForceBlackouted) {
            // 要求によるブラックリスト
            return true;
        } elseif (trim($this->player->name) === '') {
            // 名前が空だと匿名化名しか表示できない
            return true;
        }

        $loggedInUser = Yii::$app->user;
        if (!$loggedInUser->isGuest && $loggedInUser->identity->id == $this->user->id) {
            // このデータのオーナーがログインしているので全員表示
            return false;
        }

        // ユーザ設定に従う
        $blackoutMode = $this->user->blackout_list ?? 'always';
        switch ($blackoutMode) {
            // 誰も匿名化しない
            case User::BLACKOUT_NOT_BLACKOUT:
                return false;

            // プラベでは匿名化しない
            case User::BLACKOUT_NOT_PRIVATE:
                return !$this->isPrivate;

            // 自チームがフレンドと確定していれば表示する
            case User::BLACKOUT_NOT_FRIEND:
                if ($this->isPrivate) {
                    return false;
                }

                return !$this->isMyTeam;

            case User::BLACKOUT_ALWAYS:
            default:
                return true;
        }
        // }}}
    }

    private function renderSpeciesPart(): string
    {
        return Html::tag('span', $this->renderSpeciesIcon());
    }

    private function renderSpeciesIcon(): string
    {
        if (!isset($this->player->species) || !$this->player->species) {
            return '';
        }

        $asset = IrasutoyaAsset::register($this->view);
        return Html::tag(
            'span',
            $asset->img(
                $this->player->species->key . '.png',
                [
                    'alt' => Yii::t('app', $this->player->species->name),
                    'title' => Yii::t('app', $this->player->species->name),
                    'class' => 'auto-tooltip',
                    'style' => [
                        'height' => 'calc(1.2em - 2px)',
                        'width' => 'auto',
                    ],
                ],
            ),
            [
                'style' => [
                    'display' => 'inline-block',
                    'line-height' => '1',
                    'padding' => '1px',
                    'background' => $this->player->species->key === 'inkling' ? '#333' : '#ddd',
                    'border-radius' => '4px',
                ],
            ],
        );
    }
}
