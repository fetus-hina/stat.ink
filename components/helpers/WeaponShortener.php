<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\helpers;

use Override;
use Yii;
use yii\base\Component;

use function array_filter;
use function array_map;
use function array_merge;
use function file_exists;
use function implode;
use function is_array;
use function preg_match;
use function trim;

use const DIRECTORY_SEPARATOR;

class WeaponShortener extends Component
{
    public mixed $dictionary = null;

    public static function makeShorter(string $name): string
    {
        $instance = Yii::createObject(['class' => static::class]);
        return $instance->get($name);
    }

    #[Override]
    public function init(): void
    {
        parent::init();
        if (!$this->dictionary || !is_array($this->dictionary)) {
            $this->dictionary = $this->setupDictionary();
        }
    }

    public function get(string $localizedName): string
    {
        return $this->dictionary[$localizedName] ?? $localizedName;
    }

    /**
     * @return array<string, string>
     */
    protected function setupDictionary(): array
    {
        if (!preg_match('/^([[:alnum:]]+)/', (string)Yii::$app->language, $match)) {
            return [];
        }

        // try to load "@app/messages/<lang>/weapon-short.php"
        $paths = array_map(
            fn (string $langCode): string => implode(DIRECTORY_SEPARATOR, [
                Yii::getAlias('@app'),
                'messages',
                $langCode,
                'weapon-short.php',
            ]),
            [
                $match[1],
                Yii::$app->language,
            ],
        );

        $list = [];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $list = array_merge($list, array_filter(
                    include($path),
                    fn (string $value): bool => trim((string)$value) !== '',
                ));
            }
        }

        return $list;
    }
}
