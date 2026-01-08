<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
    'api/internal/theme' => 'api-theme/set',
    'api/internal/<action:[\w-]+>' => 'api-internal/<action>',
];
