<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\models\salmon3FilterForm\dropdownList;

use Yii;

trait TermDropdownListTrait
{
    /**
     * @return array{array<string, string>, array<string, string>}
     */
    public function getTermDropdown(): array
    {
        return [
            $this->getSpecifyDropdown(),
            ['prompt' => Yii::t('app', 'Any Time')],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function getSpecifyDropdown(): array
    {
        return [
            'term' => Yii::t('app-filter', 'Specify Period'),
        ];
    }
}
