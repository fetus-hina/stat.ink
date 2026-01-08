<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use yii\base\Widget;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\Html;

use function abs;
use function filter_var;
use function is_scalar;

use const FILTER_VALIDATE_FLOAT;
use const FILTER_VALIDATE_INT;

class KillRatioBadgeWidget extends Widget
{
    public $killRatio;
    public $kill;
    public $death;
    public $defaultValue = '';

    public function init()
    {
        parent::init();
        $this->killRatio = static::toFloat($this->killRatio);
        if ($this->killRatio === null) {
            $this->kill = static::toInteger($this->kill);
            $this->death = static::toInteger($this->death);
            if ($this->kill !== null && $this->death !== null) {
                if ($this->death === 0) {
                    if ($this->kill === 0) {
                        $this->killRatio = 1.0;
                    } else {
                        $this->killRatio = 99.99;
                    }
                } else {
                    $this->killRatio = $this->kill / $this->death;
                }
            }
        }
    }

    public function run()
    {
        if ($this->killRatio === null) {
            return $this->defaultValue;
        }

        BootstrapAsset::register($this->view);

        if (abs($this->killRatio - 1.0) < 0.0001) {
            return Html::tag('span', Html::encode('='), ['class' => 'label label-default']);
        }

        if ($this->killRatio > 1.0) {
            return Html::tag('span', Html::encode('>'), ['class' => 'label label-success']);
        }

        return Html::tag('span', Html::encode('<'), ['class' => 'label label-danger']);
    }

    protected static function toInteger($value): ?int
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

    protected static function toFloat($value): ?float
    {
        if ($value === null || !is_scalar($value)) {
            return null;
        }

        $value = filter_var((string)$value, FILTER_VALIDATE_FLOAT);

        return $value === false ? null : $value;
    }
}
