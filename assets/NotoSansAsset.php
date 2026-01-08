<?php

/**
 * @copyright Copyright (C) 2020-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

use function array_keys;
use function array_map;
use function array_values;
use function implode;
use function is_array;
use function rawurlencode;
use function vsprintf;

class NotoSansAsset extends AssetBundle
{
    public $css = [];

    public function init()
    {
        parent::init();

        $this->css[] = vsprintf('https://fonts.googleapis.com/css2?%s', [
            $this->buildQuery([
                'family' => array_map(
                    fn (string $family): string => $family . ':wght@400;700',
                    [
                        'Noto Sans',
                        'Noto Sans JP',
                        'Noto Sans KR',
                        'Noto Sans SC',
                        'Noto Sans TC',
                    ],
                ),
                'display' => 'swap',
            ]),
        ]);
    }

    private function buildQuery(array $parameters): string
    {
        return implode('&', array_map(
            function (string $key, $values): string {
                if (is_array($values)) {
                    return implode('&', array_map(
                        fn (string $value): string => rawurlencode($key) . '=' . rawurlencode($value),
                        $values,
                    ));
                } else {
                    return rawurlencode($key) . '=' . rawurlencode($values);
                }
            },
            array_keys($parameters),
            array_values($parameters),
        ));
    }
}
