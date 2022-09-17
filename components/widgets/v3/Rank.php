<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3;

use Yii;
use app\models\Rank3;
use yii\base\Widget;
use yii\bootstrap\Html;

final class Rank extends Widget
{
    public ?Rank3 $model = null;
    public ?int $splus = null;
    public ?int $pts = null;

    public function run(): string
    {
        $model = $this->model;
        if (!$model) {
            return '';
        }

        return $model->key === 's+'
            ? $this->renderSPlus()
            : $this->renderStandard();
    }

    private function renderSPlus(): string
    {
        return \trim(
            \vsprintf('%s%s %s', [
                $this->renderRank(),
                $this->renderSPlusNumber(),
                $this->renderPts(),
            ]),
        );
    }

    private function renderStandard(): string
    {
        return \trim(
            \vsprintf('%s %s', [
                $this->renderRank(),
                $this->renderPts(),
            ]),
        );
    }

    private function renderRank(): string
    {
        return Html::encode(Yii::t('app-rank3', $this->model->name));
    }

    private function renderSPlusNumber(): string
    {
        if ($this->splus === null) {
            return '';
        }

        return Html::tag('small', Html::encode((string)$this->splus));
    }

    private function renderPts(): string
    {
        if ($this->pts === null) {
            return '';
        }

        return Html::tag(
            'small',
            \vsprintf('(%s)', [
                Html::encode(
                    Yii::t('app', '{point}p', [
                        'point' => (string)(int)$this->pts,
                    ])
                ),
            ]),
            ['class' => 'text-muted']
        );
    }
}
