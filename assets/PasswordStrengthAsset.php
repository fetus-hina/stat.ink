<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\assets;

use Override;
use Yii;
use app\assets\zxcvbn\ZxcvbnLanguageCommonAsset;
use app\assets\zxcvbn\ZxcvbnLanguageEnAsset;
use app\assets\zxcvbn\ZxcvbnLanguageJaAsset;
use app\components\helpers\ZxcvbnLanguage;
use yii\web\AssetBundle;

final class PasswordStrengthAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources/.compiled/react';

    /**
     * @var list<string>
     */
    public $js = [
        'password-strength.js',
    ];

    /**
     * The language packages register themselves to window.zxcvbnts, and they must be
     * loaded before password-strength.js. The one for the current language is added
     * in init().
     *
     * @var list<class-string<AssetBundle>>
     */
    public $depends = [
        ZxcvbnLanguageCommonAsset::class,
        ZxcvbnLanguageEnAsset::class,
        ZxcvbnLanguageJaAsset::class,
    ];

    #[Override]
    public function init(): void
    {
        parent::init();

        $additional = ZxcvbnLanguage::getAdditionalAssetClass(Yii::$app->language);
        if ($additional !== null) {
            $this->depends[] = $additional;
        }
    }
}
