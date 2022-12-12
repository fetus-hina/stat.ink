<?php

declare(strict_types=1);

$tryLoad = function (string $path, $default = null) {
    return @file_exists($path) && @is_file($path) && @is_readable($path) ? require $path : null;
};

return [
    'adminEmail' => 'admin@example.com',
    'agentRequirements' => require __DIR__ . '/agent-requirements.php',
    'amazonS3' => require __DIR__ . '/amazon-s3.php',
    'assetRevision' => $tryLoad(__DIR__ . '/asset-revision.php'),
    'deepl' => null,
    'gitRevision' => $tryLoad(__DIR__ . '/git-revision.php'),
    'lepton' => require __DIR__ . '/lepton.php',
    'minimumPHP' => '8.0.0',
    'notifyEmail' => 'noreply@stat.ink',
    'twitter' => require __DIR__ . '/twitter.php',
    'useImgStatInk' => strpos($_SERVER['HTTP_HOST'] ?? '', 'stat.ink') !== false,
];
