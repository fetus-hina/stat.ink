<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\components\widgets\Label;
use app\models\Result3;
use app\models\Rule3;
use yii\base\Widget;

use function array_filter;
use function implode;

final class Result extends Widget
{
    public ?Result3 $result = null;
    public ?Rule3 $rule = null;
    public ?bool $isKnockout = null;
    public string $separator = ' ';

    public function run(): ?string
    {
        $result = $this->result;
        if ($result === null) {
            return null;
        }

        if ($result->key === 'draw') {
            return $this->renderResult($result);
        }

        return implode($this->separator, array_filter([
            $this->renderResult($result),
            $this->renderTimeKO($result, $this->isKnockout, $this->rule),
        ]));
    }

    private function renderResult(Result3 $result): string
    {
        return Label::widget([
            'content' => Yii::t('app', $result->name),
            'color' => $result->label_color,
        ]);
    }

    private function renderTimeKO(Result3 $result, ?bool $isKnockout, ?Rule3 $rule): ?string
    {
        if (
            $isKnockout === null ||
            ($rule && $rule->key === 'nawabari')
        ) {
            return null;
        }

        if ($isKnockout) {
            return Label::widget([
                'content' => Yii::t('app', 'Knockout'),
                'color' => 'info',
            ]);
        }

        return Label::widget([
            'content' => Yii::t('app', 'Time is up'),
            'color' => 'warning',
        ]);
    }
}
