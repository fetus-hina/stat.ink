<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
    'assetRevision' => $tryLoad(__DIR__ . '/asset-revision.php'),
    'cloudflareTurnstile' => $tryLoad(__DIR__ . '/cloudflare/turnstile.php'),
    'deepl' => null,
    'discordInviteCode' => 'DyWTsKRNvT',
    'gitRevision' => $tryLoad(__DIR__ . '/git-revision.php'),
    'lepton' => require __DIR__ . '/lepton.php',
    'minimumPHP' => '8.2.0',
    'notifyEmail' => 'noreply@stat.ink',
    'twitter' => require __DIR__ . '/twitter.php',
    'useAvatarStatsInk' => $isOfficialStatink,
    'useImgStatInk' => $isOfficialStatink,
    'useS3ImgGen' => $isOfficialStatink && $isProductionDB,
];
