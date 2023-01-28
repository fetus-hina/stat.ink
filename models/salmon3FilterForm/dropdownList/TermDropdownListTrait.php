<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\salmon3FilterForm\dropdownList;

use Yii;

trait TermDropdownListTrait
{
    public function getTermDropdown(): array
    {
        return [
            $this->getSpecifyDropdown(),
            ['prompt' => Yii::t('app', 'Any Time')],
        ];
    }

    private function getSpecifyDropdown(): array
    {
        return [
            'term' => Yii::t('app', 'Specify Period'),
        ];
    }
}
