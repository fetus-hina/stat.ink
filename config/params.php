<?php

declare(strict_types=1);

return [
    'adminEmail' => 'admin@example.com',
    'amazonS3' => require(__DIR__ . '/amazon-s3.php'),
    'assetRevision' => @file_exists(__DIR__ . '/asset-revision.php')
        ? require(__DIR__ . '/asset-revision.php')
        : null,
    'deepl' => null,
    'gitRevision' => @file_exists(__DIR__ . '/git-revision.php')
        ? require(__DIR__ . '/git-revision.php')
        : null,
    'lepton' => require(__DIR__ . '/lepton.php'),
    'minimumPHP' => '7.4.0',
    'notifyEmail' => 'noreply@stat.ink',
    'twitter' => require(__DIR__ . '/twitter.php'),
    'useImgStatInk' => strpos($_SERVER['HTTP_HOST'] ?? '', 'stat.ink') !== false,
];
