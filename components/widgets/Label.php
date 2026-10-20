<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\widgets;

use Override;
use Yii;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\i18n\Formatter;

use function is_array;
use function preg_replace;
use function trim;

final class Label extends Widget
{
    /**
     * @var string|array<mixed>|null
     */
    public $link;
    /**
     * @var string|array<mixed>
     */
    public $format = 'text';
    /**
     * @var mixed
     */
    public $content;
    /**
     * @var string|null
     */
    public $color = 'default';
    /**
     * @var array<string, mixed>
     */
    public $options = [];
    public Formatter $formatter;

    #[Override]
    public function init(): void
    {
        parent::init();

        if (!isset($this->formatter)) {
            $this->formatter = Yii::$app->formatter;
        }
    }

    public function run()
    {
        BootstrapAsset::register($this->view);

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag');

        if (!isset($options['id'])) {
            $options['id'] = $this->getId();
        }

        $class = ArrayHelper::getValue($options, 'class');
        if (is_array($class)) {
            $class[] = 'label';
            $class[] = "label-{$this->color}";
        } else {
            $class = trim(preg_replace(
                '/\s+/',
                ' ',
                (string)$class . " label label-{$this->color}",
            ));
        }
        $options['class'] = $class;

        if ($this->link) {
            return Html::a(
                $this->formatter->format($this->content, $this->format),
                $this->link,
                $options,
            );
        } else {
            return Html::tag(
                $tag ?: 'span',
                $this->formatter->format($this->content, $this->format),
                $options,
            );
        }
    }
}
