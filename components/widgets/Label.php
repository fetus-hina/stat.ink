<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

final class Label extends Widget
{
    public $link;
    public $format = 'text';
    public $content;
    public $color = 'default';
    public $options = [];
    public $formatter;

    public function init()
    {
        parent::init();

        if ($this->formatter === null) {
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
