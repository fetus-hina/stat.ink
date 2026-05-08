<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use function array_keys;
use function array_merge;
use function array_unique;
use function array_values;
use function is_array;
use function is_string;
use function ksort;
use function preg_match;
use function strcmp;
use function usort;

final class DepsLockfileDiff
{
    /**
     * Extract `owner/repo` from a git source URL when it points to GitHub.
     */
    public static function extractGithubRepo(?string $url): ?string
    {
        if (!is_string($url) || $url === '') {
            return null;
        }
        if (preg_match('~github\.com[:/]([^/]+)/([^/]+?)(?:\.git)?(?:[/?\#]|$)~', $url, $m) === 1) {
            return $m[1] . '/' . $m[2];
        }
        return null;
    }

    /**
     * Compute the version-level changes between two parsed `composer.lock` payloads.
     *
     * @param array<string, mixed> $oldData parsed old composer.lock (may be empty)
     * @param array<string, mixed> $newData parsed new composer.lock
     * @return list<array{name: string, oldVersion: ?string, newVersion: ?string, repo: ?string}>
     */
    public static function diffComposerPackages(array $oldData, array $newData): array
    {
        $oldMap = self::mapComposerPackages($oldData);
        $newMap = self::mapComposerPackages($newData);

        $names = array_unique(array_merge(array_keys($oldMap), array_keys($newMap)));
        $changes = [];
        foreach ($names as $name) {
            $old = $oldMap[$name] ?? null;
            $new = $newMap[$name] ?? null;
            $oldVersion = $old['version'] ?? null;
            $newVersion = $new['version'] ?? null;
            if ($oldVersion === $newVersion) {
                continue;
            }
            $repo = self::extractGithubRepo($new['sourceUrl'] ?? $old['sourceUrl'] ?? null);
            $changes[] = [
                'name' => $name,
                'oldVersion' => $oldVersion,
                'newVersion' => $newVersion,
                'repo' => $repo,
            ];
        }

        usort($changes, fn (array $a, array $b): int => strcmp($a['name'], $b['name']));
        return array_values($changes);
    }

    /**
     * Compute the version-level changes between two parsed `package-lock.json` payloads.
     *
     * Only top-level entries (`node_modules/<name>` or `node_modules/@scope/<name>`)
     * are considered; nested dependencies under `node_modules/<x>/node_modules/...`
     * are skipped to avoid duplicate noise.
     *
     * @param array<string, mixed> $oldData parsed old package-lock.json (may be empty)
     * @param array<string, mixed> $newData parsed new package-lock.json
     * @return list<array{name: string, oldVersion: ?string, newVersion: ?string}>
     */
    public static function diffNpmPackages(array $oldData, array $newData): array
    {
        $oldMap = self::mapNpmPackages($oldData);
        $newMap = self::mapNpmPackages($newData);

        $names = array_unique(array_merge(array_keys($oldMap), array_keys($newMap)));
        $changes = [];
        foreach ($names as $name) {
            $oldVersion = $oldMap[$name] ?? null;
            $newVersion = $newMap[$name] ?? null;
            if ($oldVersion === $newVersion) {
                continue;
            }
            $changes[] = [
                'name' => $name,
                'oldVersion' => $oldVersion,
                'newVersion' => $newVersion,
            ];
        }

        usort($changes, fn (array $a, array $b): int => strcmp($a['name'], $b['name']));
        return array_values($changes);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, array{version: string, sourceUrl: ?string}>
     */
    private static function mapComposerPackages(array $data): array
    {
        $map = [];
        foreach (['packages', 'packages-dev'] as $key) {
            $list = $data[$key] ?? null;
            if (!is_array($list)) {
                continue;
            }
            foreach ($list as $pkg) {
                if (!is_array($pkg)) {
                    continue;
                }
                $name = $pkg['name'] ?? null;
                $version = $pkg['version'] ?? null;
                if (!is_string($name) || !is_string($version)) {
                    continue;
                }
                $sourceUrl = null;
                $source = $pkg['source'] ?? null;
                if (is_array($source) && isset($source['url']) && is_string($source['url'])) {
                    $sourceUrl = $source['url'];
                }
                $map[$name] = [
                    'version' => $version,
                    'sourceUrl' => $sourceUrl,
                ];
            }
        }
        ksort($map);
        return $map;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    private static function mapNpmPackages(array $data): array
    {
        $map = [];
        $packages = $data['packages'] ?? null;
        if (!is_array($packages)) {
            return [];
        }
        foreach ($packages as $path => $pkg) {
            if (!is_string($path) || $path === '') {
                continue;
            }
            if (preg_match('#^node_modules/((?:@[^/]+/)?[^/]+)$#', $path, $m) !== 1) {
                continue;
            }
            if (!is_array($pkg)) {
                continue;
            }
            $version = $pkg['version'] ?? null;
            if (!is_string($version)) {
                continue;
            }
            $map[$m[1]] = $version;
        }
        ksort($map);
        return $map;
    }
}
