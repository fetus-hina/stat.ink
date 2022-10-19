<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\battle3FilterForm;

use app\models\Map3;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use Yii;

use const SORT_LOCALE_STRING;

trait DropdownListTrait
{
    /**
     * @return array{array<string, string>, array}
     */
    public function getMapDropdown(): array
    {
        return $this->getSimpleDropdown(
            Map3::find()->all(),
            'app-map3',
            Yii::t('app-map3', 'Any Stage'),
        );
    }

    /**
     * @param ActiveRecord[] $models
     * @return array{array<string, string>, array}
     */
    private function getSimpleDropdown(
        array $models,
        ?string $translateCatalog = 'app',
        ?string $promptText = null
    ): array {
        return [
            ArrayHelper::asort(
                ArrayHelper::map(
                    $models,
                    'key',
                    fn (ActiveRecord $model): string => ($translateCatalog === null)
                        ? $model->name
                        : Yii::t($translateCatalog, $model->name),
                ),
                SORT_LOCALE_STRING,
            ),
            $promptText === null ? [] : ['prompt' => $promptText],
        ];
    }
}
