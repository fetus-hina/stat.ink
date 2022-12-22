<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\battle3FilterForm\dropdownList;

use Yii;
use app\models\Battle3FilterForm;
use app\models\Season3;
use yii\helpers\ArrayHelper;

use const SORT_DESC;

trait TermDropdownListTrait
{
    public function getTermDropdown(): array
    {
        return [
            // \array_merge(
            $this->getTermSeasonDropdown(),
            // ),
            ['prompt' => Yii::t('app', 'Any Time')],
        ];
    }

    private function getTermSeasonDropdown(): array
    {
        return ArrayHelper::map(
            Season3::find()->orderBy(['start_at' => SORT_DESC])->all(),
            fn (Season3 $m): string => sprintf('%s%s', Battle3FilterForm::PREFIX_TERM_SEASON, $m->key),
            fn (Season3 $m): string => Yii::t('app-season3', $m->name),
        );
    }
}
