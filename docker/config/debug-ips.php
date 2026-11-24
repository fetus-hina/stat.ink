<?php

declare(strict_types=1);

// The container is a development environment. Requests through the port
// forwarding come from the container network, not from the loopback.
return [
    '127.0.0.1',
    '::1',
    '10.0.0.0/8',
    '172.16.0.0/12',
    '192.168.0.0/16',
    'fc00::/7',
];
