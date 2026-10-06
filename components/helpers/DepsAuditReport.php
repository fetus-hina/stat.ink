<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use function array_values;
use function basename;
use function count;
use function implode;
use function is_array;
use function is_int;
use function is_string;
use function ksort;
use function parse_url;
use function sprintf;
use function str_replace;
use function strcmp;
use function strtolower;
use function trim;
use function usort;

use const PHP_URL_PATH;

/**
 * Builds the "security audit" part of the dependency-update PR body
 * from `composer audit --format=json` / `npm audit --json` outputs.
 *
 * @phpstan-type Advisory array{
 *   package: string,
 *   id: string,
 *   title: string,
 *   severity: string,
 *   cve: ?string,
 *   link: ?string,
 *   affected: ?string,
 *   fix: ?string
 * }
 */
final class DepsAuditReport
{
    private const SEVERITY_RANK = [
        'critical' => 4,
        'high' => 3,
        'moderate' => 2,
        'medium' => 2,
        'low' => 1,
        'info' => 0,
    ];

    /**
     * @param array<string, mixed> $data parsed output of `composer audit --format=json`
     * @return list<Advisory>
     */
    public static function parseComposerAudit(array $data): array
    {
        $advisories = $data['advisories'] ?? null;
        if (!is_array($advisories)) {
            return [];
        }

        $result = [];
        foreach ($advisories as $package => $list) {
            if (!is_string($package) || !is_array($list)) {
                continue;
            }
            foreach ($list as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $id = self::stringOrNull($item['advisoryId'] ?? null);
                if ($id === null) {
                    continue;
                }
                $result[] = [
                    'package' => $package,
                    'id' => $id,
                    'title' => self::stringOrNull($item['title'] ?? null) ?? '',
                    'severity' => strtolower(self::stringOrNull($item['severity'] ?? null) ?? 'unknown'),
                    'cve' => self::stringOrNull($item['cve'] ?? null),
                    'link' => self::stringOrNull($item['link'] ?? null),
                    'affected' => self::stringOrNull($item['affectedVersions'] ?? null),
                    'fix' => null,
                ];
            }
        }
        return $result;
    }

    /**
     * @param array<string, mixed> $data parsed output of `composer audit --format=json`
     * @return array<string, ?string> abandoned package name => replacement package (if any)
     */
    public static function parseComposerAbandoned(array $data): array
    {
        $abandoned = $data['abandoned'] ?? null;
        if (!is_array($abandoned)) {
            return [];
        }

        $result = [];
        foreach ($abandoned as $package => $replacement) {
            if (!is_string($package)) {
                continue;
            }
            $result[$package] = self::stringOrNull($replacement);
        }
        ksort($result);
        return $result;
    }

    /**
     * Only actual advisory entries are collected. Entries that are vulnerable merely because
     * one of their dependencies is (`via` is a package name) are skipped to avoid noise.
     *
     * @param array<string, mixed> $data parsed output of `npm audit --json` (auditReportVersion 2)
     * @return list<Advisory>
     */
    public static function parseNpmAudit(array $data): array
    {
        $vulnerabilities = $data['vulnerabilities'] ?? null;
        if (!is_array($vulnerabilities)) {
            return [];
        }

        $result = [];
        foreach ($vulnerabilities as $package => $vuln) {
            if (!is_string($package) || !is_array($vuln)) {
                continue;
            }
            $via = $vuln['via'] ?? null;
            if (!is_array($via)) {
                continue;
            }
            $fix = self::formatNpmFix($vuln['fixAvailable'] ?? null);
            foreach ($via as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $link = self::stringOrNull($item['url'] ?? null);
                $id = self::extractNpmAdvisoryId($link, $item['source'] ?? null);
                if ($id === null) {
                    continue;
                }
                $result[] = [
                    'package' => $package,
                    'id' => $id,
                    'title' => self::stringOrNull($item['title'] ?? null) ?? '',
                    'severity' => strtolower(self::stringOrNull($item['severity'] ?? null) ?? 'unknown'),
                    'cve' => null,
                    'link' => $link,
                    'affected' => self::stringOrNull($item['range'] ?? null),
                    'fix' => $fix,
                ];
            }
        }
        return $result;
    }

    /**
     * @param list<Advisory> $old advisories before the update
     * @param list<Advisory> $new advisories after the update
     * @return array{fixed: list<Advisory>, introduced: list<Advisory>, remaining: list<Advisory>}
     */
    public static function compare(array $old, array $new): array
    {
        $oldMap = self::mapByKey($old);
        $newMap = self::mapByKey($new);

        $fixed = [];
        $introduced = [];
        $remaining = [];
        foreach ($oldMap as $key => $advisory) {
            if (!isset($newMap[$key])) {
                $fixed[] = $advisory;
            }
        }
        foreach ($newMap as $key => $advisory) {
            if (isset($oldMap[$key])) {
                $remaining[] = $advisory;
            } else {
                $introduced[] = $advisory;
            }
        }

        return [
            'fixed' => self::sort($fixed),
            'introduced' => self::sort($introduced),
            'remaining' => self::sort($remaining),
        ];
    }

    /**
     * @param list<Advisory>|null $old advisories before the update; null if that audit failed
     * @param list<Advisory>|null $new advisories after the update; null if that audit failed
     * @param array<string, ?string> $abandoned
     */
    public static function renderSection(string $heading, ?array $old, ?array $new, array $abandoned = []): string
    {
        $lines = ['### ' . $heading, ''];

        if ($new === null) {
            $lines[] = '- :warning: Audit failed / 監査に失敗しました';
        } elseif ($old === null) {
            $lines[] = '- :warning: Comparison unavailable (audit of the previous lock file failed) / '
                . '更新前の監査に失敗したため比較できません';
            if ($new) {
                $lines[] = '';
                $lines[] = self::renderTable(self::sort($new));
            } else {
                $lines[] = '- No known vulnerabilities / 既知の脆弱性はありません';
            }
        } else {
            $result = self::compare($old, $new);
            if (!$result['fixed'] && !$result['introduced'] && !$result['remaining']) {
                $lines[] = '- No known vulnerabilities / 既知の脆弱性はありません';
            } else {
                $lines[] = sprintf('- Fixed / 解消: %d', count($result['fixed']));
                $lines[] = sprintf('- Introduced / 新規: %d', count($result['introduced']));
                $lines[] = sprintf('- Remaining / 残存: %d', count($result['remaining']));
                if ($result['fixed']) {
                    $lines[] = '';
                    $lines[] = '#### Fixed / 解消';
                    $lines[] = '';
                    $lines[] = self::renderTable($result['fixed']);
                }
                if ($result['introduced']) {
                    $lines[] = '';
                    $lines[] = '#### Introduced / 新規';
                    $lines[] = '';
                    $lines[] = self::renderTable($result['introduced']);
                }
                if ($result['remaining']) {
                    $lines[] = '';
                    $lines[] = sprintf(
                        '<details><summary>Remaining / 残存 (%d)</summary>',
                        count($result['remaining']),
                    );
                    $lines[] = '';
                    $lines[] = self::renderTable($result['remaining']);
                    $lines[] = '';
                    $lines[] = '</details>';
                }
            }
        }

        if ($abandoned) {
            $lines[] = '';
            $lines[] = 'Abandoned packages / 放棄されたパッケージ:';
            $lines[] = '';
            foreach ($abandoned as $package => $replacement) {
                $lines[] = $replacement === null
                    ? sprintf('- `%s`', $package)
                    : sprintf('- `%s` (replacement: `%s`)', $package, $replacement);
            }
        }

        return implode("\n", $lines) . "\n";
    }

    /**
     * @param list<Advisory> $advisories
     */
    private static function renderTable(array $advisories): string
    {
        $lines = [
            '| Severity | Package | Advisory | Affected | Fix |',
            '| --- | --- | --- | --- | --- |',
        ];
        foreach ($advisories as $a) {
            $label = $a['cve'] ?? $a['id'];
            $advisory = $a['link'] !== null
                ? sprintf('[%s](%s)', self::escapeCell($label), $a['link'])
                : self::escapeCell($label);
            if ($a['title'] !== '') {
                $advisory .= ' ' . self::escapeCell($a['title']);
            }
            $lines[] = sprintf(
                '| %s | `%s` | %s | %s | %s |',
                self::escapeCell($a['severity']),
                $a['package'],
                $advisory,
                $a['affected'] !== null ? '`' . self::escapeCell($a['affected']) . '`' : '-',
                $a['fix'] !== null ? self::escapeCell($a['fix']) : '-',
            );
        }
        return implode("\n", $lines);
    }

    private static function escapeCell(string $text): string
    {
        return str_replace(
            ['|', "\r\n", "\n", "\r"],
            ['\\|', ' ', ' ', ' '],
            trim($text),
        );
    }

    private static function formatNpmFix(mixed $fixAvailable): ?string
    {
        if ($fixAvailable === true) {
            return '`npm audit fix`';
        }
        if (!is_array($fixAvailable)) {
            return null;
        }
        $name = self::stringOrNull($fixAvailable['name'] ?? null);
        $version = self::stringOrNull($fixAvailable['version'] ?? null);
        if ($name === null || $version === null) {
            return null;
        }
        return sprintf(
            '`%s@%s`%s',
            $name,
            $version,
            ($fixAvailable['isSemVerMajor'] ?? false) === true ? ' (major)' : '',
        );
    }

    private static function extractNpmAdvisoryId(?string $url, mixed $source): ?string
    {
        if ($url !== null) {
            $path = parse_url($url, PHP_URL_PATH);
            if (is_string($path) && $path !== '' && $path !== '/') {
                return basename($path);
            }
            return $url;
        }
        return is_int($source) ? sprintf('npm-%d', $source) : null;
    }

    /**
     * @param list<Advisory> $advisories
     * @return array<string, Advisory>
     */
    private static function mapByKey(array $advisories): array
    {
        $map = [];
        foreach ($advisories as $advisory) {
            $map[$advisory['package'] . "\0" . $advisory['id']] = $advisory;
        }
        return $map;
    }

    /**
     * @param list<Advisory> $advisories
     * @return list<Advisory>
     */
    private static function sort(array $advisories): array
    {
        usort(
            $advisories,
            fn (array $a, array $b): int => (self::SEVERITY_RANK[$b['severity']] ?? -1)
                    <=> (self::SEVERITY_RANK[$a['severity']] ?? -1)
                ?: strcmp($a['package'], $b['package'])
                ?: strcmp($a['id'], $b['id']),
        );
        return array_values($advisories);
    }

    private static function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }
}
