<?php

declare(strict_types=1);

use app\components\helpers\UuidRegexp;

$uuid = UuidRegexp::get(false);

return [
    "DELETE api/v3/battle/<uuid:{$uuid}>" => 'api-v3/delete-battle',
    "DELETE api/v3/salmon/<uuid:{$uuid}>" => 'api-v3/delete-salmon',

    'POST api/v3/salmon' => 'api-v3/post-salmon',
    'PUT api/v3/salmon' => 'api-v3/post-salmon',

    "api/v3/salmon/<uuid:{$uuid}>" => 'api-v3/single-salmon',
    'api/v3/salmon/<action:[\w-]+>' => 'api-v3/salmon-<action>',
    'api/v3/s3s/<action:[\w-]+>' => 'api-v3/s3s-<action>',
    'api/v3/<action:[\w-]+>.<format:[\w]+>' => 'api-v3/<action>',
    'api/v3/<action:[\w-]+>' => 'api-v3/<action>',
];
