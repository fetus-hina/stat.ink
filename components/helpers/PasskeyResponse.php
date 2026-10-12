<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use yii\base\Model;
use yii\helpers\Html;

/**
 * Builds failure responses of the passkey JSON endpoints
 *
 * Failures carry a machine-readable code only; the client shows its own
 * translated message.
 */
final class PasskeyResponse
{
    /**
     * @return array{result: false, error: string}
     */
    public static function failure(string $error): array
    {
        return ['result' => false, 'error' => $error];
    }

    /**
     * Returns the validation errors keyed by the input ID, as yiiActiveForm('updateMessages') expects
     *
     * @return array{result: false, error: 'invalid_params', errors: array<string, string[]>}
     */
    public static function validationFailure(Model $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $attr => $messages) {
            $errors[Html::getInputId($form, $attr)] = $messages;
        }

        return [
            'result' => false,
            'error' => 'invalid_params',
            'errors' => $errors,
        ];
    }
}
