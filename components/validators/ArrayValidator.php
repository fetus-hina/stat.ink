<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\validators;

use ArrayAccess;
use yii\validators\EachValidator;

final class ArrayValidator extends EachValidator
{
    public ?int $min = null;
    public ?int $max = null;

    public ?string $tooBig = null;
    public ?string $tooSmall = null;

    /**
     * @inheritdoc
     * @return void
     */
    public function init()
    {
        parent::init();

        if ($this->tooBig === null) {
            $this->tooBig = '{attribute} must not contain more than {max} elements';
        }

        if ($this->tooBig === null) {
            $this->tooBig = '{attribute} must contain at least {min} elements';
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $arrayOfValues = $model->$attribute;
        if (\is_array($arrayOfValues) || $arrayOfValues instanceof ArrayAccess) {
            if ($this->min !== null && \count($arrayOfValues) < $this->min) {
                $this->addError($model, $attribute, $this->tooSmall, ['min' => $this->min]);
                return;
            }

            if ($this->max !== null && \count($arrayOfValues) > $this->max) {
                $this->addError($model, $attribute, $this->tooBig, ['max' => $this->max]);
                return;
            }
        }

        parent::validateAttribute($model, $attribute);
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if (\is_array($value) || $value instanceof ArrayAccess) {
            if ($this->min !== null && \count($value) < $this->min) {
                return [$this->tooSmall, ['min' => $this->min]];
            }

            if ($this->max !== null && \count($value) > $this->max) {
                return [$this->tooBig, ['max' => $this->max]];
            }
        }

        return parent::validateValue($value);
    }
}
