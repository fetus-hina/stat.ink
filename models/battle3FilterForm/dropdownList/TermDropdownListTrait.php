<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models\battle3FilterForm\dropdownList;

use Yii;
use app\components\helpers\Season3Helper;
use app\models\Battle3FilterForm;
use app\models\Season3;
use yii\helpers\ArrayHelper;

use function array_merge;
use function sprintf;

trait TermDropdownListTrait
{
    public function getTermDropdown(): array
    {
        return [
            array_merge(
                $this->getPeriodDropdown(),
                $this->getDateDropdown(),
                $this->getTermSeasonDropdown(),
                $this->getSpecifyDropdown(),
            ),
            ['prompt' => Yii::t('app', 'Any Time')],
        ];
    }

    private function getPeriodDropdown(): array
    {
        return [
            'this-period' => Yii::t('app-filter', 'Current Period'),
            'last-period' => Yii::t('app-filter', 'Previous Period'),
        ];
    }

    private function getDateDropdown(): array
    {
        return [
            '24h' => Yii::t('app-filter', 'Last 24 Hours'),
            'today' => Yii::t('app-filter', 'Today'),
            'yesterday' => Yii::t('app-filter', 'Yesterday'),
        ];
    }

    private function getTermSeasonDropdown(): array
    {
        return [
            Yii::t('app', 'Season') => ArrayHelper::map(
                Season3Helper::getSeasons(),
                fn (Season3 $m): string => sprintf('%s%s', Battle3FilterForm::PREFIX_TERM_SEASON, $m->key),
                fn (Season3 $m): string => Yii::t('app-season3', $m->name),
            ),
        ];
    }

    private function getSpecifyDropdown(): array
    {
        return [
            'term' => Yii::t('app-filter', 'Specify Period'),
        ];
    }
}
