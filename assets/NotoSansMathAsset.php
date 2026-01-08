<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

use function http_build_query;
use function vsprintf;

final class NotoSansMathAsset extends AssetBundle
{
    public $css = [];

    public function init()
    {
        parent::init();

        $this->css[] = vsprintf('https://fonts.googleapis.com/css2?%s', [
            http_build_query(
                [
                    'family' => 'Noto Sans Math',
                    'display' => 'swap',
                ],
                '',
                '&',
            ),
        ]);
    }
}
