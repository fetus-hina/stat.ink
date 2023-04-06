#!/usr/bin/env php
<?php

declare(strict_types=1);

$list = [
    'rules/nawabari/0.png' => null,
    'rules/nawabari/1.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Pnt_Lv00.png',
    'rules/nawabari/2.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Pnt_Lv01.png',
    'rules/nawabari/3.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Pnt_Lv02.png',
    'rules/tricolor-attacker/0.png' => null,
    'rules/tricolor-attacker/1.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Tcl_Atk_Lv00.png',
    'rules/tricolor-attacker/2.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Tcl_Atk_Lv01.png',
    'rules/tricolor-defender/0.png' => null,
    'rules/tricolor-defender/1.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Tcl_Def_Lv00.png',
    'rules/tricolor-defender/2.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Tcl_Def_Lv01.png',
    'rules/area/0.png' => null,
    'rules/area/1.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Var_Lv00.png',
    'rules/area/2.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Var_Lv01.png',
    'rules/asari/0.png' => null,
    'rules/asari/1.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Vcl_Lv00.png',
    'rules/asari/2.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Vcl_Lv01.png',
    'rules/hoko/0.png' => null,
    'rules/hoko/1.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Vgl_Lv00.png',
    'rules/hoko/2.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Vgl_Lv01.png',
    'rules/yagura/0.png' => null,
    'rules/yagura/1.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Vlf_Lv00.png',
    'rules/yagura/2.png' => 'https://leanny.github.io/splat3/images/badge/Badge_WinCount_Vlf_Lv01.png',
    'salmonids/yokozuna/0.png' => null,
    'salmonids/yokozuna/1.png' => 'https://leanny.github.io/splat3/images/badge/Badge_CoopBossKillNum_SakelienGiant_Lv00.png',
    'salmonids/yokozuna/2.png' => 'https://leanny.github.io/splat3/images/badge/Badge_CoopBossKillNum_SakelienGiant_Lv01.png',
    'salmonids/yokozuna/3.png' => 'https://leanny.github.io/splat3/images/badge/Badge_CoopBossKillNum_SakelienGiant_Lv02.png',
    'salmonids/tatsu/0.png' => null,
    'salmonids/tatsu/1.png' => 'https://leanny.github.io/splat3/images/badge/Badge_CoopBossKillNum_SakeRope_Lv00.png',
    'salmonids/tatsu/2.png' => 'https://leanny.github.io/splat3/images/badge/Badge_CoopBossKillNum_SakeRope_Lv01.png',
    'salmonids/tatsu/3.png' => 'https://leanny.github.io/splat3/images/badge/Badge_CoopBossKillNum_SakeRope_Lv02.png',
];

$specials = [
    'amefurashi' => 'SpInkStorm',
    'decoy' => 'SpFirework',
    'energystand' => 'SpEnergyStand',
    'greatbarrier' => 'SpGreatBarrier',
    'hopsonar' => 'SpShockSonar',
    'jetpack' => 'SpJetpack',
    'kanitank' => 'SpChariot',
    'kyuinki' => 'SpBlower',
    'megaphone51' => 'SpMicroLaser',
    'missile' => 'SpMultiMissile',
    'nicedama' => 'SpNiceBall',
    'sameride' => 'SpSkewer',
    'shokuwander' => 'SpSuperHook',
    'teioika' => 'SpCastle',
    'tripletornado' => 'SpTripleTornado',
    'ultrahanko' => 'SpUltraStamp',
    'ultrashot' => 'SpUltraShot',
];
foreach ($specials as $key => $urlKey) {
    $list["specials/{$key}/0.png"] = null;
    foreach (range(1, 3) as $i) {
        $list["specials/{$key}/{$i}.png"] = sprintf(
            'https://leanny.github.io/splat3/images/badge/Badge_WinCount_WeaponSp_%s_Lv%02d.png',
            $urlKey,
            $i - 1,
        );
    }
}

$salmonids = [
    'bakudan' => 'SakelienBomber',
    'diver' => 'SakeDolphin',
    'hashira' => 'SakePillar',
    'hebi' => 'SakelienSnake',
    'katapad' => 'SakelienCupTwins',
    'koumori' => 'Sakerocket',
    'mogura' => 'Sakediver',
    'nabebuta' => 'SakeSaucer',
    'tekkyu' => 'SakeArtillery',
    'teppan' => 'SakelienShield',
    'tower' => 'SakelienTower',
];
foreach ($salmonids as $key => $urlKey) {
    $list["salmonids/{$key}/0.png"] = null;
    foreach (range(1, 3) as $i) {
        $list["salmonids/{$key}/{$i}.png"] = sprintf(
            'https://leanny.github.io/splat3/images/badge/Badge_CoopRareEnemyKillNum_%s_Lv%02d.png',
            $urlKey,
            $i - 1,
        );
    }
}

foreach ($list as $dstRelPath => $srcUrl) {
    $dstPath = __DIR__ . '/' . $dstRelPath;
    if (file_exists($dstPath)) {
        echo "Exists: {$dstRelPath}\n";
        continue;
    }

    if (!file_exists(dirname($dstPath))) {
        mkdir(dirname($dstPath), 0755, true);
    }

    if ($srcUrl === null) {
        symlink('../../__empty__.png', $dstPath);
        echo "Link created: {$dstRelPath}\n";
        continue;
    }

    try {
        $tmpPath = __DIR__ . '/tmp-' . uniqid('', true) . '.png';
        echo "Downloading {$srcUrl}\n";
        cmdexec(
            vsprintf('/usr/bin/curl -fsSL %s | convert - -resize 40x40 %s', [
                escapeshellarg($srcUrl),
                escapeshellarg($tmpPath),
            ]),
        );
        echo "Optimizing\n";
        cmdexec(
            vsprintf('/usr/bin/pngcrush -new -brute -force -q -rem allb %s %s', [
                escapeshellarg($tmpPath),
                escapeshellarg($dstPath),
            ]),
        );
    } finally {
        @unlink($tmpPath);
    }
}

function cmdExec(string $cmdline): void
{
    exec($cmdline, $lines, $status);
    if ($status !== 0) {
        throw new Exception("status={$status}, cmdline={$cmdline}");
    }
}
