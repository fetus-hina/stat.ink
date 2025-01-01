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
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function array_filter;
use function sprintf;

class FA extends Widget
{
    public $tag = 'span';
    public $isFW = false;
    public $icon = null;
    public $size = null;
    public int $rotate = 0;
    public $type = 'fas';
    public $content = null;
    public $options = [];
    public $contentOptions = [];

    private $asset;

    public static function fas(?string $icon, array $options = []): self
    {
        return static::factory('fas', $icon, $options);
    }

    public static function far(?string $icon, array $options = []): self
    {
        return static::factory('far', $icon, $options);
    }

    public static function fab(?string $icon, array $options = []): self
    {
        return static::factory('fab', $icon, $options);
    }

    public static function fal(?string $icon, array $options = []): self
    {
        return static::factory('fal', $icon, $options);
    }

    public static function hack(string $content): self
    {
        return static::factory('fas', null, ['content' => $content]);
    }

    protected static function factory(string $type, ?string $icon, array $options): self
    {
        return Yii::createObject(ArrayHelper::merge([
            'class' => static::class,
            'type' => $type,
            'icon' => $icon,
        ], $options));
    }

    public function init()
    {
        parent::init();
        $this->asset = FontAwesomeAsset::register($this->view);
    }

    public function fw(): self
    {
        $this->isFW = true;
        return $this;
    }

    public function icon(?string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function size(?string $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @param 0|90|180|270 $angle
     */
    public function rotate(int $angle): self
    {
        $this->rotate = $angle;
        return $this;
    }

    public function contentOptions(array $contentOptions): self
    {
        $this->contentOptions = $contentOptions;
        return $this;
    }

    public function __toString()
    {
        return $this->renderFA();
    }

    public function run()
    {
        echo $this->renderFA();
    }

    protected function renderFA(): string
    {
        return Html::tag(
            $this->tag,
            $this->content ?? '',
            ArrayHelper::merge(
                [
                    'id' => $this->id,
                    'class' => array_filter([
                        $this->type,
                        $this->isFW ? 'fa-fw' : null,
                        $this->icon ? 'fa-' . $this->icon : null,
                        $this->size ? 'fa-' . $this->size : null,
                        $this->rotate ? sprintf('fa-rotate-%d', $this->rotate) : null,
                    ]),
                ],
                $this->contentOptions,
            ),
        );
    }
}
