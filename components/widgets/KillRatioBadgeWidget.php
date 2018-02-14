<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\Html;

class KillRatioBadgeWidget extends Widget
{
    public $kill;
    public $death;
    public $defaultValue = '';

    public function init()
    {
        parent::init();
        $this->kill = static::toInteger($this->kill);
        $this->death = static::toInteger($this->death);
    }

    public function run()
    {
        if (!is_int($this->kill) || !is_int($this->death)) {
            return $this->defaultValue;
        }

        BootstrapAsset::register($this->view);

        if ($this->kill === $this->death) {
            return Html::tag('span', Html::encode('='), ['class' => 'label label-default']);
        }

        if ($this->kill > $this->death) {
            return Html::tag('span', Html::encode('>'), ['class' => 'label label-success']);
        }

        return Html::tag('span', Html::encode('<'), ['class' => 'label label-danger']);
    }

    protected static function toInteger($value) : ?int
    {
        if ($value === null || !is_scalar($value)) {
            return null;
        }

        $value = filter_var((string)$value, FILTER_VALIDATE_INT, [
            'min_range' => 0,
            'max_range' => 99,
        ]);
        return $value === false ? null : $value;
    }
}
