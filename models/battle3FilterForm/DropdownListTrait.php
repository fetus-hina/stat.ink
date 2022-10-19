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
use app\models\LobbyGroup3;
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
        return $this->getGroupDropdown(
            Lobby3::find()
                ->innerJoinWith(['group'], true)
                ->orderBy([
                    '{{%lobby_group3}}.[[rank]]' => SORT_ASC,
                    '{{%lobby3}}.[[rank]]' => SORT_ASC,
                ])
                ->all(),
            'group',
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
        return $this->getGroupDropdown(
            Rule3::find()
                ->innerJoinWith(['group'], true)
                ->orderBy([
                    '{{%rule_group3}}.[[rank]]' => SORT_ASC,
                    '{{%rule3}}.[[rank]]' => SORT_ASC,
                ])
                ->all(),
            'group',
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
     */
    private function getGroupDropdown(
        array $models,
        string $groupAttr,
        ?string $translateCatalog = 'app',
        ?string $promptText = null,
        bool $sort = true
    ): array {
        // 使いやすい形に加工する

        /**
         * @var array<string, array{group: ActiveRecord, items: ActiveRecord[]}>
         */
        $groups = [];
        foreach ($models as $model) {
            $group = $model->{$groupAttr};
            if (!isset($groups[$group->key])) {
                $groups[$group->key] = [
                    'group' => $group,
                    'items' => [],
                ];
            }
            $groups[$group->key]['items'][] = $model;
        }

        $results = [];
        foreach ($groups as $groupInfo) {
            if (\count($groupInfo['items']) < 1) {
                continue;
            }

            $group = $groupInfo['group'];
            $tmp = [];
            if (\count($groupInfo['items']) > 1) {
                $tmp['@' . $group->key] = ($translateCatalog === null)
                    ? $group->name
                    : Yii::t($translateCatalog, $group->name);
            }

            [$items, ] = $this->getSimpleDropdown(
                $groupInfo['items'],
                $translateCatalog,
                null,
                $sort,
            );
            $tmp = \array_merge($tmp, $items);

            // make group
            $groupName = ($translateCatalog === null)
                ? $group->name
                : Yii::t($translateCatalog, $group->name);

            $results[$groupName] = $tmp;
        }

        return [
            $results,
            $promptText === null ? [] : ['prompt' => $promptText],
        ];
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
