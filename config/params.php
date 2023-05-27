<?php

declare(strict_types=1);

$tryLoad = fn (string $path, $default = null) => @file_exists($path) && @is_file($path) && @is_readable($path) ? require $path : $default;

$isOfficialStatink = ($_SERVER['HTTP_HOST'] ?? 'stat.ink') === 'stat.ink';
$isProductionDB = false;
if ($isOfficialStatink) {
    if ($db = $tryLoad(__DIR__ . '/db.php')) {
        $isProductionDB = str_ends_with($db['dsn'], 'dbname=statink');
    }
}

return [
    'adminEmail' => 'admin@example.com',
    'agentRequirements' => require __DIR__ . '/agent-requirements.php',
    'amazonS3' => require __DIR__ . '/amazon-s3.php',
    'assetRevision' => $tryLoad(__DIR__ . '/asset-revision.php'),
    'deepl' => null,
    'discordInviteCode' => 'DyWTsKRNvT',
    'gitRevision' => $tryLoad(__DIR__ . '/git-revision.php'),
    'lepton' => require __DIR__ . '/lepton.php',
    'minimumPHP' => '8.0.0',
    'notifyEmail' => 'noreply@stat.ink',
    'twitter' => require __DIR__ . '/twitter.php',
    'useImgStatInk' => $isOfficialStatink,
    'useS3ImgGen' => false, // $isOfficialStatink && $isProductionDB,
];
