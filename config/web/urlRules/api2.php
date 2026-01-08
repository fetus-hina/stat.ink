<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
    'GET,HEAD api/v2/battle' => 'api-v2-battle/index',
    'GET,HEAD api/v2/user-battle' => 'api-v2-battle/index-with-auth',
    'GET,HEAD api/v2/battle/<id:\d+>' => 'api-v2-battle/view',
    'POST api/v2/battle' => 'api-v2-battle/create',
    'OPTIONS api/v2/battle' => 'api-v2-battle/postable-options',
    'OPTIONS api/v2/user-battle' => 'api-v2-battle/options',
    'OPTIONS api/v2/battle/<id:\d+>' => 'api-v2-battle/options',

    // Splatoon 2 Salmon Run
    'OPTIONS api/v2/salmon/<id:\d+>' => 'api-v2-salmon/options',
    'OPTIONS api/v2/salmon-stats' => 'api-v2-salmon/options',
    'OPTIONS api/v2/salmon' => 'api-v2-salmon/options',
    'OPTIONS api/v2/user-salmon' => 'api-v2-salmon/options',
    'GET,HEAD api/v2/salmon/<id:\d+>' => 'api-v2-salmon/view',
    'GET,HEAD api/v2/salmon-stats' => 'api-v2-salmon/view-stats',
    'GET,HEAD api/v2/salmon' => 'api-v2-salmon/index',
    'GET,HEAD api/v2/user-salmon' => 'api-v2-salmon/index-with-auth',
    'POST api/v2/salmon-stats' => 'api-v2-salmon/create-stats',
    'POST api/v2/salmon' => 'api-v2-salmon/create',

    'api/v2/<action:[\w-]+>.<format:[\w]+>' => 'api-v2/<action>',
    'api/v2/<action:[\w-]+>' => 'api-v2/<action>',
];
