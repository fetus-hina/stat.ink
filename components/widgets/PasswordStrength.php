<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\assets\PasswordStrengthAsset;
use app\components\helpers\ZxcvbnLanguage;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Renders the password strength gauge and feedback for the password input
 */
final class PasswordStrength extends Widget
{
    /**
     * The selector of the password input to check, e.g. "#registerform-password"
     */
    public string $selector;

    public function run(): string
    {
        PasswordStrengthAsset::register($this->view);

        return Html::tag('div', '', [
            'class' => 'password-strength',
            'id' => $this->id,
            'data' => [
                'input' => $this->selector,
                'translations' => ZxcvbnLanguage::getTranslationsPackageName(Yii::$app->language),
            ],
        ]);
    }
}
