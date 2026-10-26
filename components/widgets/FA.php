<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\widgets;

use Override;
use Yii;
use app\assets\FontAwesomeAsset;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function array_filter;
use function sprintf;

class FA extends Widget
{
    public string $tag = 'span';
    public bool $isFW = false;
    public ?string $icon = null;
    public ?string $size = null;
    public int $rotate = 0;
    public string $type = 'fas';
    public ?string $content = null;
    /**
     * @var array<string, mixed>
     */
    public array $options = [];
    /**
     * @var array<string, mixed>
     */
    public array $contentOptions = [];

    /**
     * @param array<string, mixed> $options
     */
    public static function fas(?string $icon, array $options = []): self
    {
        return static::factory('fas', $icon, $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public static function far(?string $icon, array $options = []): self
    {
        return static::factory('far', $icon, $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public static function fab(?string $icon, array $options = []): self
    {
        return static::factory('fab', $icon, $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public static function fal(?string $icon, array $options = []): self
    {
        return static::factory('fal', $icon, $options);
    }

    public static function hack(string $content): self
    {
        return static::factory('fas', null, ['content' => $content]);
    }

    /**
     * @param array<string, mixed> $options
     */
    protected static function factory(string $type, ?string $icon, array $options): self
    {
        /** @var array{class: class-string<static>} $config */
        $config = ArrayHelper::merge([
            'class' => static::class,
            'type' => $type,
            'icon' => $icon,
        ], $options);
        return Yii::createObject($config);
    }

    #[Override]
    public function init(): void
    {
        parent::init();
        FontAwesomeAsset::register($this->view);
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

    /**
     * @param array<string, mixed> $contentOptions
     */
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
                        $this->isFW ? null : 'fa-width-auto',
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
