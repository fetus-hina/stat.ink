<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\validators;

use Override;
use Yii;
use app\components\helpers\TypeHelper;
use yii\validators\Validator;
use yii\web\UploadedFile;

use function file_get_contents;
use function imagecreatefromstring;
use function imagesx;
use function imagesy;
use function is_file;
use function is_readable;
use function is_string;

final class BattleImageValidator extends Validator
{
    public const FILESIZE_LIMIT = 3 * 1024 * 1024;

    #[Override]
    public function init(): void
    {
        parent::init();
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function validateAttribute($model, $attribute): void
    {
        if ($this->validateValueImpl($model->$attribute)) {
            return;
        }

        $this->addError($model, $attribute, TypeHelper::string($this->message));
    }

    /**
     * @inheritdoc
     * @return array{string, array<string, mixed>}|null
     */
    #[Override]
    protected function validateValue($value)
    {
        if ($this->validateValueImpl($value)) {
            return null;
        }

        return [TypeHelper::string($this->message), []];
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
        return imagesx($gd) > 100 &&
            imagesy($gd) > 100 &&
            imagesx($gd) <= 3840 &&
            imagesy($gd) <= 2160;
    }
}
