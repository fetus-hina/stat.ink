<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use Yii;
use app\models\Language;
use yii\helpers\ArrayHelper;

use const SORT_ASC;

final class NameApiFormatter
{
    public static function toJson(?string $usName, string $msgCategory, bool $fullTranslate = false): ?array
    {
        return $usName === null
            ? null
            : self::translateToAll($usName, $msgCategory, self::getTargetLanguages($fullTranslate));
    }

    /**
     * @param string[] $languages
     * @phpstan-return array<string, string>
     */
    private static function translateToAll(string $usName, string $msgCategory, array $languages): array
    {
        static $i18n = null;
        if ($i18n === null) {
            $i18n = Yii::$app->i18n;
        }

        return ArrayHelper::map(
            $languages,
            fn (string $lang): string => \str_replace('-', '_', $lang),
            fn (string $lang): string => $i18n->translate($msgCategory, $usName, [], $lang),
        );
    }

    /**
     * @return string[]
     */
    private static function getTargetLanguages(bool $fullTranslate): array
    {
        return $fullTranslate
            ? self::getAvailableLanguageCodes()
            : ['en-US', 'ja-JP'];
    }

    /**
     * @return string[]
     */
    private static function getAvailableLanguageCodes(): array
    {
        /** @var string[]|null */
        static $cache = null;
        if ($cache === null) {
            $cache = \array_map(
                fn (Language $model): string => $model->lang,
                Language::find()
                    ->standard()
                    ->orderBy(['lang' => SORT_ASC])
                    ->all()
            );
        }

        return $cache;
    }
}
