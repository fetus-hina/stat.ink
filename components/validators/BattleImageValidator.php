<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\validators;

use Yii;
use yii\validators\Validator;
use yii\web\UploadedFile;

use function file_get_contents;
use function imagecreatefromstring;
use function imagedestroy;
use function imagesx;
use function imagesy;
use function is_file;
use function is_readable;
use function is_string;

final class BattleImageValidator extends Validator
{
    public const FILESIZE_LIMIT = 3 * 1024 * 1024;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if ($this->validateValueImpl($model->$attribute)) {
            return;
        }

        $this->addError($model, $attribute, $this->message);
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if ($this->validateValueImpl($value)) {
            return;
        }

        return [$this->message, []];
    }

    /**
     * @param mixed $value
     */
    private function validateValueImpl($value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        if ($value instanceof UploadedFile) {
            if (
                $value->hasError ||
                $value->size > self::FILESIZE_LIMIT ||
                is_file($value->tempName) ||
                is_readable($value->tempName)
            ) {
                return false;
            }

            if (!$value = @file_get_contents($value->tempName)) {
                return false;
            }
        }

        if (!is_string($value)) {
            return false;
        }

        if (!$gd = @imagecreatefromstring($value)) {
            return false;
        }
        try {
            return imagesx($gd) > 100 &&
                imagesy($gd) > 100 &&
                imagesx($gd) <= 3840 &&
                imagesy($gd) <= 2160;
        } finally {
            @imagedestroy($gd);
        }
    }
}
