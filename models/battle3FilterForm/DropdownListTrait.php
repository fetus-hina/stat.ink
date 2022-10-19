<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\battle3FilterForm;

use Yii;
use app\models\Lobby3;
use app\models\Map3;
use app\models\Rule3;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use const SORT_ASC;
use const SORT_LOCALE_STRING;

trait DropdownListTrait
{
    /**
     * @return array{array<string, string>, array}
     */
    public function getLobbyDropdown(): array
    {
        // FIXME: group
        return $this->getSimpleDropdown(
            Lobby3::find()->orderBy(['rank' => SORT_ASC])->all(),
            'app-lobby3',
            Yii::t('app-lobby3', 'Any Lobby'),
            false,
        );
    }
    /**
     * @return array{array<string, string>, array}
     */
    public function getRuleDropdown(): array
    {
        // FIXME: gachi
        return $this->getSimpleDropdown(
            Rule3::find()->orderBy(['rank' => SORT_ASC])->all(),
            'app-rule3',
            Yii::t('app-rule3', 'Any Mode'),
            false,
        );
    }
    /**
     * @return array{array<string, string>, array}
     */
    public function getMapDropdown(): array
    {
        return $this->getSimpleDropdown(
            Map3::find()->all(),
            'app-map3',
            Yii::t('app-map3', 'Any Stage'),
            true,
        );
    }

    /**
     * @param ActiveRecord[] $models
     * @return array{array<string, string>, array}
     */
    private function getSimpleDropdown(
        array $models,
        ?string $translateCatalog = 'app',
        ?string $promptText = null,
        bool $sort = true
    ): array {
        $list = ArrayHelper::map(
            $models,
            'key',
            fn (ActiveRecord $model): string => ($translateCatalog === null)
                ? $model->name
                : Yii::t($translateCatalog, $model->name),
        );

        return [
            $sort ? ArrayHelper::asort($list, SORT_LOCALE_STRING) : $list,
            $promptText === null ? [] : ['prompt' => $promptText],
        ];
    }
}
