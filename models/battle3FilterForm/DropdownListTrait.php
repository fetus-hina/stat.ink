<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\battle3FilterForm;

use Yii;
use app\models\Battle3FilterForm;
use app\models\Lobby3;
use app\models\Map3;
use app\models\Result3;
use app\models\Rule3;
use app\models\battle3FilterForm\dropdownList\TermDropdownListTrait;
use app\models\battle3FilterForm\dropdownList\WeaponDropdownListTrait;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use const SORT_ASC;
use const SORT_LOCALE_STRING;

trait DropdownListTrait
{
    use TermDropdownListTrait;
    use WeaponDropdownListTrait;

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
     * @return array{array<string, string>, array}
     */
    public function getResultDropdown(): array
    {
        $order = [
            'win' => 1,
            'lose' => 3,
            'exempted_lose' => 5,
            'draw' => 7,
        ];

        $list = ArrayHelper::map(
            Result3::find()->all(),
            'key',
            fn (Result3 $model): string => Yii::t('app', $model->name),
        );
        \uksort($list, function (string $a, string $b) use ($order): int {
            // どちらかがソート順に定義されていないなら、定義されていない方が後
            if (isset($order[$a]) !== isset($order[$b])) {
                return isset($order[$a]) ? -1 : 1;
            }

            return isset($order[$a])
                ? ($order[$a] <=> $order[$b] ?: \strcmp($a, $b))
                : \strcmp($a, $b);
        });

        $list[Yii::t('app', 'Advanced Options')] = [
            Battle3FilterForm::RESULT_NOT_WIN => Yii::t('app', 'Not Winning'),
            Battle3FilterForm::RESULT_WIN_OR_LOSE => Yii::t('app', 'Victory or Defeat'),
            Battle3FilterForm::RESULT_VIRTUAL_LOSE => Yii::t('app', 'Consider to be Defeated'),
            Battle3FilterForm::RESULT_NOT_DRAW => Yii::t('app', 'Not Draws'),
        ];

        $list[Battle3FilterForm::RESULT_UNKNOWN] = Yii::t('app', 'Unknown Result');

        return [
            $list,
            ['prompt' => Yii::t('app', 'Any Result')],
        ];
    }

    /**
     * @return array{array<string, string>, array}
     */
    public function getKnockoutDropdown(): array
    {
        return [
            [
                'yes' => Yii::t('app', 'Knockout'),
                'no' => Yii::t('app', 'Time is up'),
            ],
            [
                'prompt' => \sprintf('%s / %s', Yii::t('app', 'Knockout'), Yii::t('app', 'Time')),
            ],
        ];
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

            [$items,] = $this->getSimpleDropdown(
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
