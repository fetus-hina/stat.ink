<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\widgets;

use Override;
use Yii;
use app\assets\FlagIconCssAsset;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function array_filter;
use function array_merge_recursive;
use function trim;

final class FlagIcon extends Widget
{
    public string $tag = 'span';
    public bool $isBackground = false; // deprecated
    public bool $isSquare = false;
    public ?string $cc = null;
    /**
     * @var array<string, mixed>
     */
    public array $options = [];

    /**
     * @param array<string, mixed> $options
     */
    public static function fg(string $cc, array $options = []): self
    {
        return static::factory($cc, false, $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public static function bg(string $cc, array $options = []): self
    {
        return static::factory($cc, true, $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    protected static function factory(string $cc, bool $isBG, array $options): self
    {
        /** @var array{class: class-string<static>} $config */
        $config = ArrayHelper::merge([
            'class' => static::class,
            'isBackground' => $isBG,
            'cc' => $cc,
        ], $options);
        return Yii::createObject($config);
    }

    #[Override]
    public function init(): void
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
        echo $this->renderIcon();
    }

    protected function renderIcon(): string
    {
        return Html::tag(
            $this->tag,
            '',
            array_merge_recursive(
                [
                    'class' => array_filter(
                        [
                            'fi',
                            "fi-{$this->cc}",
                            $this->isSquare ? 'fis' : null,
                        ],
                        fn (?string $v): bool => trim((string)$v) !== '',
                    ),
                    'id' => $this->id,
                ],
                $this->options,
            ),
        );
    }
}
