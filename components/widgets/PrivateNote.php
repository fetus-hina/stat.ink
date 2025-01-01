<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\FontAwesomeAsset;
use app\assets\PrivateNoteAsset;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\Html;

use function is_array;

class PrivateNote extends Widget
{
    public $text;
    public $formatter;

    public function init()
    {
        parent::init();

        if ($this->formatter === null) {
            $this->formatter = Yii::$app->formatter;
        }

        if (is_array($this->formatter)) {
            $this->formatter = Yii::createObject($this->formatter);
        }
    }

    public function run()
    {
        return Html::tag(
            'div',
            $this->renderButton() . $this->renderText(),
            ['id' => $this->id],
        );
    }

    protected function renderButton(): string
    {
        BootstrapAsset::register($this->view);
        FontAwesomeAsset::register($this->view);
        PrivateNoteAsset::register($this->view);

        $this->view->registerJs("jQuery('#{$this->buttonId}').privateNote();");

        return Html::button(
            Html::tag('span', '', ['class' => 'fas fa-lock fa-fw']),
            [
                'class' => 'btn btn-default',
                'id' => $this->buttonId,
                'data' => [
                    'target' => '#' . $this->textId,
                ],
            ],
        );
    }

    protected function renderText(): string
    {
        $this->view->registerCss("#{$this->textId}{display:none}");

        return Html::tag(
            'div',
            $this->formatter->asNtext($this->text),
            ['id' => $this->textId],
        );
    }

    public function getButtonId(): string
    {
        return $this->id . '-button';
    }

    public function getTextId(): string
    {
        return $this->id . '-text';
    }
}
