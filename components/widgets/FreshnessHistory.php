<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use app\assets\FreshnessHistoryAsset;
use app\models\Battle2;
use app\models\Mode2;
use app\models\Rule2;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class FreshnessHistory extends Widget
{
    public $user;
    public $current;

    public function init()
    {
        parent::init();

        if (!$this->user) {
            $this->user = $this->current->user;
        }
    }

    public function run(): string
    {
        if (!$history = $this->getHistory()) {
            return '';
        }

        FreshnessHistoryAsset::register($this->view);
        $this->view->registerJs(vsprintf('$(%s).freshnessHistory(%s);', [
            Json::encode('#' . $this->id),
            implode(', ', array_map([Json::class, 'encode'], [
                array_map(
                    fn (Battle2 $model): ?float => $model->freshness === null ? null : (float)$model->freshness,
                    $history,
                ),
            ])),
        ]));

        return Html::tag(
            'div',
            implode('', [
                Html::tag(
                    'div',
                    Html::tag('div', '', [
                        'id' => $this->id,
                        'class' => [
                            'freshness-history',
                        ],
                    ]),
                    ['class' => 'table-responsive'],
                ),
            ]),
            ['class' => [
                'freshness-history-container',
            ]],
        );
    }

    public function getHistory(): ?array
    {
        // {{{
        if (!$ruleTW = Rule2::findOne(['key' => 'nawabari'])) {
            return null;
        }

        if (!$modeFest = Mode2::findOne(['key' => 'fest'])) {
            return null;
        }

        if (!$modePrivate = Mode2::findOne(['key' => 'private'])) {
            return null;
        }

        if (
            !$this->current ||
            $this->current->rule_id !== $ruleTW->id ||
            $this->current->mode_id === $modeFest->id ||
            $this->current->mode_id === $modePrivate->id ||
            $this->current->weapon_id === null ||
            $this->current->freshness === null
        ) {
            return null;
        }

        $history = Battle2::find()
            ->andWhere(['and',
                [
                    '{{battle2}}.[[user_id]]' => $this->user->id,
                    '{{battle2}}.[[rule_id]]' => $ruleTW->id,
                    '{{battle2}}.[[weapon_id]]' => $this->current->weapon_id,
                ],
                ['not', ['{{battle2}}.[[mode_id]]' => [
                    $modeFest->id,
                    $modePrivate->id,
                ]]],
                ['<=', '{{battle2}}.[[id]]', $this->current->id],
                ['not', ['{{battle2}}.[[freshness]]' => null]],
            ])
            ->orderBy([
                '{{battle2}}.[[id]]' => SORT_DESC,
            ])
            ->limit(50)
            ->all();
        if (count($history) < 2) {
            return null;
        }

        // old -> new
        return array_reverse($history);
        // }}}
    }
}
