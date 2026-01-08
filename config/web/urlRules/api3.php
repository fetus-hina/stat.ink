<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\UuidRegexp;

$uuid = UuidRegexp::get(false);

return [
    'OPTIONS api/v3/battle' => 'api-v3-preflight/post-options',
    "OPTIONS api/v3/battle/<uuid:{$uuid}>" => 'api-v3-preflight/delete-options',
    'OPTIONS api/v3/salmon' => 'api-v3-preflight/post-options',
    "OPTIONS api/v3/salmon/<uuid:{$uuid}>" => 'api-v3-preflight/delete-options',

    "DELETE api/v3/battle/<uuid:{$uuid}>" => 'api-v3/delete-battle',
    "DELETE api/v3/salmon/<uuid:{$uuid}>" => 'api-v3/delete-salmon',
    'POST api/v3/battle' => 'api-v3/post-battle',
    'POST api/v3/salmon' => 'api-v3/post-salmon',
    'PUT api/v3/battle' => 'api-v3/post-battle',
    'PUT api/v3/salmon' => 'api-v3/post-salmon',

    "api/v3/battle/<uuid:{$uuid}>" => 'api-v3/single-battle',
    "api/v3/salmon/<uuid:{$uuid}>" => 'api-v3/single-salmon',
    'api/v3/salmon/<action:[\w-]+>' => 'api-v3/salmon-<action>',
    '@<screen_name:\w+>/salmon3.json' => 'api-v3/user-salmon',

    'api/v3/s3s/<action:[\w-]+>' => 'api-v3/s3s-<action>',

    'api/v3/<action:[\w-]+>.<format:[\w]+>' => 'api-v3/<action>',
    'api/v3/<action:[\w-]+>' => 'api-v3/<action>',
];
