<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\userMiniInfo\items;

use function mb_chr;

$zwsp = mb_chr(0x200b, 'UTF-8');

return [
    'label' => $zwsp,
    'value' => $zwsp,
];
