<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\salmon3FilterForm;

use Yii;
use app\models\Map3;
use app\models\Salmon3FilterForm;
use app\models\SalmonMap3;
use app\models\salmon3FilterForm\dropdownList\TermDropdownListTrait;
use yii\helpers\ArrayHelper;

use function vsprintf;

use const SORT_NATURAL;

trait DropdownListTrait
{
    use TermDropdownListTrait;

    public function getLobbyDropdown(): array
    {
        return [
            [
                Salmon3FilterForm::LOBBY_NOT_PRIVATE => Yii::t('app-lobby3', 'Except Private'),
                Salmon3FilterForm::LOBBY_NORMAL => Yii::t('app-salmon3', 'Normal Job'),
                Salmon3FilterForm::LOBBY_BIG_RUN => Yii::t('app-salmon3', 'Big Run'),
                Salmon3FilterForm::LOBBY_PRIVATE => Yii::t('app-salmon3', 'Private Job'),
            ],
            [
                'prompt' => Yii::t('app-lobby3', 'Any Lobby'),
            ],
        ];
    }

    public function getMapDropdown(): array
    {
        return [
            [
                Yii::t('app-salmon3', 'Normal Job') => ArrayHelper::asort(
                    ArrayHelper::map(
                        SalmonMap3::find()->all(),
                        'key',
                        fn (SalmonMap3 $model): string => Yii::t('app-map3', $model->name),
                    ),
                    SORT_NATURAL,
                ),
                Yii::t('app-salmon3', 'Big Run') => ArrayHelper::asort(
                    ArrayHelper::map(
                        Map3::find()
                            ->andWhere([
                                'key' => [
                                    'amabi',
                                    'sumeshi',
                                ],
                            ])
                            ->all(),
                        'key',
                        fn (Map3 $model): string => Yii::t('app-map3', $model->name),
                    ),
                    SORT_NATURAL,
                ),
            ],
            [
                'prompt' => Yii::t('app-map3', 'Any Stage'),
            ],
        ];
    }

    public function getResultDropdown(): array
    {
        return [
            [
                self::RESULT_CLEARED => Yii::t('app-salmon2', 'Cleared'),
                self::RESULT_CLEARED_KING_APPEAR => vsprintf('%s / %s: %s', [
                    Yii::t('app-salmon2', 'Cleared'),
                    Yii::t('app-salmon3', 'King'),
                    Yii::t('app-salmon3', 'Appeared'),
                ]),
                self::RESULT_CLEARED_KING_DEFEAT => vsprintf('%s / %s: %s', [
                    Yii::t('app-salmon2', 'Cleared'),
                    Yii::t('app-salmon3', 'King'),
                    Yii::t('app-salmon3', 'Defeated'),
                ]),
                self::RESULT_CLEARED_KING_FAILED => vsprintf('%s / %s: %s', [
                    Yii::t('app-salmon2', 'Cleared'),
                    Yii::t('app-salmon3', 'King'),
                    Yii::t('app-salmon3', 'Not Defeated'),
                ]),
                self::RESULT_FAILED => Yii::t('app-salmon2', 'Failed'),
                self::RESULT_FAILED_W1 => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', ['waveNumber' => 1]),
                self::RESULT_FAILED_W2 => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', ['waveNumber' => 2]),
                self::RESULT_FAILED_W3 => Yii::t('app-salmon2', 'Failed in wave {waveNumber}', ['waveNumber' => 3]),
            ],
            [
                'prompt' => Yii::t('app', 'Any Result'),
            ],
        ];
    }
}
