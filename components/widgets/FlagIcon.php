<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\FlagIconCssAsset;
use app\components\helpers\Html;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

final class FlagIcon extends Widget
{
    public $tag = 'span';
    public $isBackground = false;
    public $isSquare = false;
    public $cc = null;
    public $options = [];

    public static function fg(string $cc, array $options = []): self
    {
        return static::factory($cc, false, $options);
    }

    public static function bg(string $cc, array $options = []): self
    {
        return static::factory($cc, true, $options);
    }

    protected static function factory(string $cc, bool $isBG, array $options): self
    {
        return Yii::createObject(ArrayHelper::merge([
            'class' => static::class,
            'isBackground' => $isBG,
            'cc' => $cc,
        ], $options));
    }

    public function init()
    {
        parent::init();
        FlagIconCssAsset::register($this->view);
    }

    public function cc(string $cc): self
    {
        $this->cc = $cc;
        return $this;
    }

    public function setFG(): self
    {
        $this->isBackground = false;
        return $this;
    }

    public function setBG(): self
    {
        $this->isBackground = true;
        return $this;
    }

    public function square(): self
    {
        $this->isSquare = true;
        return $this;
    }

    public function rectangle(): self
    {
        $this->isSquare = false;
        return $this;
    }

    public function __toString()
    {
        return $this->renderIcon();
    }

    public function run()
    {
        return $this->renderIcon();
    }

    protected function renderIcon(): string
    {
        return Html::tag(
            $this->tag,
            '',
            array_merge_recursive(
                [
                    'id' => $this->id,
                    'class' => array_filter([
                        $this->isBackground ? 'flag-icon-background' : 'flag-icon',
                        $this->isSquare ? 'flag-icon-squared' : '',
                        "flag-icon-{$this->cc}",
                    ]),
                ],
                $this->options
            )
        );
    }
}
