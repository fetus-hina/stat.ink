<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\widgets;

use Override;
use Yii;
use app\assets\UserMiniinfoAsset;
use app\components\i18n\Formatter;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function is_array;
use function is_string;

class MiniinfoData extends Widget
{
    public ?string $label = null;
    public ?string $labelTitle = null;
    /**
     * @var string|array<mixed>
     */
    public string|array $labelFormat = 'text';
    public mixed $value = null;
    public ?string $valueTitle = null;
    /**
     * @var string|array<mixed>
     */
    public string|array $valueFormat = 'text';
    public ?string $nullDisplay = null;
    /**
     * @var string|array<mixed>
     */
    public string|array $nullDisplayFormat = 'text';
    /**
     * @var array<string, mixed>
     */
    public array $options = [
        'class' => 'col-xs-4',
    ];
    /**
     * @var array<string, mixed>
     */
    public array $labelOptions = [
        'class' => 'user-label auto-tooltip',
    ];
    /**
     * @var array<string, mixed>
     */
    public array $valueOptions = [
        'class' => 'user-number text-right auto-tooltip',
    ];
    /**
     * @var \yii\i18n\Formatter|class-string|array{class: class-string, ...}|null
     */
    public $formatter;

    #[Override]
    public function init(): void
    {
        parent::init();
        if ($this->formatter === null) {
            $this->formatter = Formatter::class;
        }
        if (is_array($this->formatter) || is_string($this->formatter)) {
            $this->formatter = Yii::createObject($this->formatter);
        }
        if (!($this->formatter instanceof \yii\i18n\Formatter)) {
            $this->formatter = Yii::createObject(Formatter::class);
        }
    }

    public function run(): string
    {
        BootstrapAsset::register($this->view);
        UserMiniinfoAsset::register($this->view);

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        if (!isset($options['id'])) {
            $options['id'] = $this->id;
        }
        return Html::tag(
            $tag,
            $this->renderHeader() . $this->renderValue(),
            $options,
        );
    }

    protected function renderHeader(): string
    {
        return $this->renderElement(
            $this->label,
            $this->labelTitle,
            $this->labelFormat,
            $this->labelOptions,
            null,
            'text',
        );
    }

    protected function renderValue(): string
    {
        return $this->renderElement(
            $this->value,
            $this->valueTitle,
            $this->valueFormat,
            $this->valueOptions,
            $this->nullDisplay,
            $this->nullDisplayFormat,
        );
    }

    /**
     * @param string|array<mixed> $format
     * @param array<string, mixed> $options
     * @param string|array<mixed> $nullDisplayFormat
     */
    protected function renderElement(
        mixed $value,
        ?string $valueTitle,
        string|array $format,
        array $options,
        ?string $nullDisplay,
        string|array $nullDisplayFormat,
    ): string {
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        if (!isset($options['title']) && $valueTitle != '') {
            $options['title'] = $valueTitle;
        }

        $f = $this->formatter;
        if ($value === null && $nullDisplay !== null) {
            $value = $nullDisplay;
            $format = $nullDisplayFormat;
        }

        return Html::tag(
            $tag,
            $f->format($value, $format),
            $options,
        );
    }
}
