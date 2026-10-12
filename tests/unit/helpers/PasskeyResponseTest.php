<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\PasskeyResponse;
use app\models\RegisterForm;

class PasskeyResponseTest extends Unit
{
    public function testFailureHasOnlyTheErrorCode(): void
    {
        $this->assertSame(
            ['result' => false, 'error' => 'no_challenge'],
            PasskeyResponse::failure('no_challenge'),
        );
    }

    public function testValidationFailureKeysErrorsByInputId(): void
    {
        $form = new RegisterForm();
        $form->addError('screen_name', 'first');
        $form->addError('screen_name', 'second');
        $form->addError('name', 'third');

        $this->assertSame(
            [
                'result' => false,
                'error' => 'invalid_params',
                'errors' => [
                    'registerform-screen_name' => ['first', 'second'],
                    'registerform-name' => ['third'],
                ],
            ],
            PasskeyResponse::validationFailure($form),
        );
    }

    public function testValidationFailureWithoutErrors(): void
    {
        $this->assertSame(
            ['result' => false, 'error' => 'invalid_params', 'errors' => []],
            PasskeyResponse::validationFailure(new RegisterForm()),
        );
    }
}
