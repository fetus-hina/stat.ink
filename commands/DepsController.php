<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\commands;

use RuntimeException;
use Throwable;
use Yii;
use app\components\helpers\DepsAuditReport;
use app\components\helpers\DepsLockfileDiff;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\CurlTransport;

use function basename;
use function bin2hex;
use function count;
use function escapeshellarg;
use function explode;
use function fclose;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function fwrite;
use function getenv;
use function implode;
use function is_array;
use function is_resource;
use function is_string;
use function proc_close;
use function proc_open;
use function random_bytes;
use function rawurlencode;
use function shell_exec;
use function sleep;
use function sprintf;
use function stream_get_contents;
use function strlen;
use function substr;
use function sys_get_temp_dir;
use function trim;
use function vfprintf;

use const STDERR;

final class DepsController extends Controller
{
    private const LLM_TIMEOUT = 180;
    private const LLM_RETRY = 2;
    private const HTTP_TIMEOUT = 30;
    private const NOTES_PER_PACKAGE_LIMIT = 1500;
    private const FALLBACK_BODY = 'This is an automated pull-request';

    public function actionSummarizeChanges(): int
    {
        $apiKey = (string)getenv('LLM_API_KEY');
        $endpoint = (string)getenv('LLM_API_ENDPOINT');
        $model = (string)getenv('LLM_MODEL');
        $githubToken = (string)getenv('GITHUB_TOKEN');

        if ($apiKey === '' || $endpoint === '' || $model === '') {
            fwrite(
                STDERR,
                "[error] Required env vars unset: LLM_API_KEY, LLM_API_ENDPOINT, LLM_MODEL\n",
            );
            return ExitCode::CONFIG;
        }

        $appComposer = $this->collectComposerChanges('composer.lock');
        $deployComposer = $this->collectComposerChanges('deploy/composer.lock');
        $npm = $this->collectNpmChanges('package-lock.json');

        $auditSection = $this->buildAuditSection();

        if (!$appComposer && !$deployComposer && !$npm) {
            fwrite(STDERR, "[info] No package version changes detected\n");
            echo $this->assembleBody(null, $auditSection);
            return ExitCode::OK;
        }

        vfprintf(STDERR, "[info] Detected %d composer (app) / %d composer (deploy) / %d npm changes\n", [
            count($appComposer),
            count($deployComposer),
            count($npm),
        ]);

        $appComposerDiff = $this->gitDiff('composer.lock');
        $deployComposerDiff = $this->gitDiff('deploy/composer.lock');
        $npmDiff = $this->gitDiff('package-lock.json');

        $this->enrichComposerReleaseNotes($appComposer, $githubToken);
        $this->enrichComposerReleaseNotes($deployComposer, $githubToken);
        $this->enrichNpmReleaseNotes($npm, $githubToken);

        $userPrompt = $this->buildUserPrompt(
            $appComposer,
            $deployComposer,
            $npm,
            $appComposerDiff,
            $deployComposerDiff,
            $npmDiff,
            $auditSection,
        );

        $body = $this->callLlm($endpoint, $apiKey, $model, $userPrompt);
        if ($body === null) {
            // The audit section is still worth publishing without the LLM summary
            fwrite(STDERR, "[error] LLM call failed after retries\n");
        }

        echo $this->assembleBody($body, $auditSection);
        return ExitCode::OK;
    }

    private function assembleBody(?string $summary, string $auditSection): string
    {
        $parts = [];
        if (is_string($summary) && trim($summary) !== '') {
            $parts[] = trim($summary);
        }
        $parts[] = trim($auditSection);
        $parts[] = '---';
        $parts[] = self::FALLBACK_BODY;

        return implode("\n\n", $parts) . "\n";
    }

    private function buildAuditSection(): string
    {
        $sections = [
            '## Security audit / セキュリティ監査',
            '',
            'Results of `composer audit` / `npm audit --omit=dev`, compared with the lock files before this update.',
            '',
        ];

        $ignoreRules = $this->loadAuditIgnoreRules();

        foreach (['' => '.', 'deploy/' => 'deploy'] as $prefix => $dir) {
            if (!file_exists($prefix . 'composer.lock')) {
                continue;
            }
            $new = $this->runComposerAudit($dir);
            $old = $this->withHeadFiles(
                [$prefix . 'composer.json', $prefix . 'composer.lock'],
                fn (string $tmpDir): ?array => $this->runComposerAudit($tmpDir),
            );
            $sections[] = DepsAuditReport::renderSection(
                sprintf('PHP (%scomposer.lock)', $prefix),
                $old === null ? null : DepsAuditReport::parseComposerAudit($old),
                $new === null ? null : DepsAuditReport::parseComposerAudit($new),
                $new === null ? [] : DepsAuditReport::parseComposerAbandoned($new),
                $ignoreRules,
            );
        }

        if (file_exists('package-lock.json')) {
            $new = $this->runNpmAudit('.');
            $old = $this->withHeadFiles(
                ['package.json', 'package-lock.json'],
                fn (string $tmpDir): ?array => $this->runNpmAudit($tmpDir),
            );
            $sections[] = DepsAuditReport::renderSection(
                'JavaScript (package-lock.json)',
                $old === null ? null : DepsAuditReport::parseNpmAudit($old),
                $new === null ? null : DepsAuditReport::parseNpmAudit($new),
                [],
                $ignoreRules,
            );
        }

        return implode("\n", $sections);
    }

    /**
     * @return list<array{id: string, package?: string}>
     */
    private function loadAuditIgnoreRules(): array
    {
        $path = (string)Yii::getAlias('@app/config/deps-audit-ignore.php');
        if (!file_exists($path)) {
            return [];
        }

        $config = require $path;
        if (!is_array($config)) {
            vfprintf(STDERR, "[warn] %s does not return an array\n", [$path]);
            return [];
        }

        $rules = [];
        foreach ($config as $i => $entry) {
            $id = $entry['id'] ?? null;
            $package = $entry['package'] ?? null;
            if (
                !is_string($id) ||
                trim($id) === '' ||
                ($package !== null && !is_string($package))
            ) {
                vfprintf(STDERR, "[warn] Invalid audit ignore rule #%s in %s; skipped\n", [$i, $path]);
                continue;
            }

            $rule = ['id' => trim($id)];
            if ($package !== null) {
                $rule['package'] = $package;
            }
            $rules[] = $rule;
        }
        return $rules;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function runComposerAudit(string $dir): ?array
    {
        return $this->runAuditCommand(
            [
                (string)Yii::getAlias('@app/composer.phar'),
                'audit',
                '--locked',
                '--format=json',
                '--no-plugins',
                '--no-interaction',
            ],
            $dir,
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    private function runNpmAudit(string $dir): ?array
    {
        return $this->runAuditCommand(
            ['npm', 'audit', '--package-lock-only', '--omit=dev', '--json'],
            $dir,
        );
    }

    /**
     * Audit commands exit non-zero when vulnerabilities are found, so success is judged
     * by whether stdout is a JSON object.
     *
     * @param list<string> $command
     * @return array<string, mixed>|null
     */
    private function runAuditCommand(array $command, string $cwd): ?array
    {
        $proc = proc_open(
            $command,
            [
                0 => ['file', '/dev/null', 'r'],
                1 => ['pipe', 'w'],
                2 => ['file', '/dev/null', 'w'],
            ],
            $pipes,
            $cwd,
        );
        if (!is_resource($proc)) {
            vfprintf(STDERR, "[warn] Failed to run %s\n", [implode(' ', $command)]);
            return null;
        }
        $stdout = (string)stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $status = proc_close($proc);

        try {
            $data = Json::decode($stdout);
            if (is_array($data)) {
                return $data;
            }
        } catch (Throwable) {
        }

        vfprintf(STDERR, "[warn] %s in %s failed (exit %d)\n", [
            implode(' ', $command),
            $cwd,
            $status,
        ]);
        return null;
    }

    /**
     * Materialize the HEAD versions of the given files into a temporary directory
     * (flattened by basename) and run the callback there.
     *
     * @template T
     * @param list<string> $paths
     * @param callable(string): T $callback
     * @return T|null
     */
    private function withHeadFiles(array $paths, callable $callback): mixed
    {
        $tmpDir = sys_get_temp_dir() . '/statink-deps-audit-' . bin2hex(random_bytes(8));
        FileHelper::createDirectory($tmpDir);
        try {
            foreach ($paths as $path) {
                $content = $this->gitFileAtHead($path);
                if ($content === null) {
                    vfprintf(STDERR, "[warn] %s does not exist at HEAD\n", [$path]);
                    return null;
                }
                file_put_contents($tmpDir . '/' . basename($path), $content);
            }
            return $callback($tmpDir);
        } finally {
            FileHelper::removeDirectory($tmpDir);
        }
    }

    /**
     * @return list<array{name: string, oldVersion: ?string, newVersion: ?string, repo: ?string, releaseNotes: ?string}>
     */
    private function collectComposerChanges(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $newJson = (string)file_get_contents($path);
        $oldJson = $this->gitFileAtHead($path) ?? '{}';

        try {
            $oldData = Json::decode($oldJson);
            $newData = Json::decode($newJson);
        } catch (Throwable) {
            return [];
        }

        if (!is_array($oldData) || !is_array($newData)) {
            return [];
        }

        $changes = DepsLockfileDiff::diffComposerPackages($oldData, $newData);
        $result = [];
        foreach ($changes as $c) {
            $result[] = [
                'name' => $c['name'],
                'oldVersion' => $c['oldVersion'],
                'newVersion' => $c['newVersion'],
                'repo' => $c['repo'],
                'releaseNotes' => null,
            ];
        }
        return $result;
    }

    /**
     * @return list<array{name: string, oldVersion: ?string, newVersion: ?string, repo: ?string, releaseNotes: ?string}>
     */
    private function collectNpmChanges(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $newJson = (string)file_get_contents($path);
        $oldJson = $this->gitFileAtHead($path) ?? '{}';

        try {
            $oldData = Json::decode($oldJson);
            $newData = Json::decode($newJson);
        } catch (Throwable) {
            return [];
        }

        if (!is_array($oldData) || !is_array($newData)) {
            return [];
        }

        $changes = DepsLockfileDiff::diffNpmPackages($oldData, $newData);
        $result = [];
        foreach ($changes as $c) {
            $result[] = [
                'name' => $c['name'],
                'oldVersion' => $c['oldVersion'],
                'newVersion' => $c['newVersion'],
                'repo' => null,
                'releaseNotes' => null,
            ];
        }
        return $result;
    }

    /**
     * @param list<array{name: string, oldVersion: ?string, newVersion: ?string, repo: ?string, releaseNotes: ?string}> $changes
     */
    private function enrichComposerReleaseNotes(array &$changes, string $token): void
    {
        foreach ($changes as &$c) {
            if (!is_string($c['repo']) || !is_string($c['newVersion'])) {
                continue;
            }
            $c['releaseNotes'] = $this->fetchGithubReleaseNotes($c['repo'], $c['newVersion'], $token);
        }
        unset($c);
    }

    /**
     * @param list<array{name: string, oldVersion: ?string, newVersion: ?string, repo: ?string, releaseNotes: ?string}> $changes
     */
    private function enrichNpmReleaseNotes(array &$changes, string $token): void
    {
        foreach ($changes as &$c) {
            if (!is_string($c['newVersion'])) {
                continue;
            }
            $repo = $this->fetchNpmRepo($c['name']);
            $c['repo'] = $repo;
            if (is_string($repo)) {
                $c['releaseNotes'] = $this->fetchGithubReleaseNotes($repo, $c['newVersion'], $token);
            }
        }
        unset($c);
    }

    private function fetchNpmRepo(string $packageName): ?string
    {
        try {
            $response = $this->httpClient()
                ->createRequest()
                ->setOptions([
                    'timeout' => self::HTTP_TIMEOUT,
                    'maxRedirects' => 5,
                    'userAgent' => 'stat.ink-deps-summarizer',
                ])
                ->setMethod('GET')
                ->setUrl(sprintf('https://registry.npmjs.org/%s', rawurlencode($packageName)))
                ->send();
            if (!$response->isOk) {
                return null;
            }
            $data = Json::decode((string)$response->content);
            if (!is_array($data)) {
                return null;
            }
            $repository = $data['repository'] ?? null;
            $url = is_array($repository) ? ($repository['url'] ?? null) : null;
            return DepsLockfileDiff::extractGithubRepo(is_string($url) ? $url : null);
        } catch (Throwable $e) {
            vfprintf(STDERR, "[warn] npm registry fetch failed for %s: %s\n", [
                $packageName,
                $e->getMessage(),
            ]);
            return null;
        }
    }

    private function fetchGithubReleaseNotes(string $repo, string $newVersion, string $token): ?string
    {
        foreach (['v' . $newVersion, $newVersion] as $tag) {
            $body = $this->fetchGithubReleaseByTag($repo, $tag, $token);
            if (is_string($body)) {
                return $body;
            }
        }
        return null;
    }

    private function fetchGithubReleaseByTag(string $repo, string $tag, string $token): ?string
    {
        try {
            $headers = [
                'Accept' => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
            ];
            if ($token !== '') {
                $headers['Authorization'] = 'Bearer ' . $token;
            }
            $response = $this->httpClient()
                ->createRequest()
                ->setOptions([
                    'timeout' => self::HTTP_TIMEOUT,
                    'maxRedirects' => 5,
                    'userAgent' => 'stat.ink-deps-summarizer',
                ])
                ->setMethod('GET')
                ->setUrl(sprintf(
                    'https://api.github.com/repos/%s/releases/tags/%s',
                    $repo,
                    rawurlencode($tag),
                ))
                ->setHeaders($headers)
                ->send();
            if ($response->statusCode === 404) {
                return null;
            }
            if (!$response->isOk) {
                vfprintf(STDERR, "[warn] GitHub releases HTTP %d for %s tag %s\n", [
                    $response->statusCode,
                    $repo,
                    $tag,
                ]);
                return null;
            }
            $data = Json::decode((string)$response->content);
            if (!is_array($data)) {
                return null;
            }
            $body = $data['body'] ?? null;
            if (!is_string($body) || trim($body) === '') {
                return null;
            }
            return trim($body);
        } catch (Throwable $e) {
            vfprintf(STDERR, "[warn] GitHub releases fetch failed for %s/%s: %s\n", [
                $repo,
                $tag,
                $e->getMessage(),
            ]);
            return null;
        }
    }

    private function httpClient(): HttpClient
    {
        $client = Yii::createObject([
            'class' => HttpClient::class,
            'transport' => CurlTransport::class,
        ]);
        if (!$client instanceof HttpClient) {
            throw new RuntimeException('Failed to create HttpClient');
        }
        return $client;
    }

    private function gitFileAtHead(string $path): ?string
    {
        $out = shell_exec(sprintf('git show HEAD:%s 2>/dev/null', escapeshellarg($path)));
        return is_string($out) && $out !== '' ? $out : null;
    }

    private function gitDiff(string $path): string
    {
        $out = shell_exec(sprintf('git diff HEAD -- %s 2>/dev/null', escapeshellarg($path)));
        return is_string($out) ? $out : '';
    }

    /**
     * @param list<array{name: string, oldVersion: ?string, newVersion: ?string, repo: ?string, releaseNotes: ?string}> $appComposer
     * @param list<array{name: string, oldVersion: ?string, newVersion: ?string, repo: ?string, releaseNotes: ?string}> $deployComposer
     * @param list<array{name: string, oldVersion: ?string, newVersion: ?string, repo: ?string, releaseNotes: ?string}> $npm
     * @param string $auditSection Markdown produced by DepsAuditReport
     */
    private function buildUserPrompt(
        array $appComposer,
        array $deployComposer,
        array $npm,
        string $appComposerDiff,
        string $deployComposerDiff,
        string $npmDiff,
        string $auditSection,
    ): string {
        $sections = [
            '# Dependency update summary input',
            '',
            'Below is the data for an automated dependency-update pull request.',
            'Generate the PR body per the system prompt instructions.',
            '',
            '## Changed packages (PHP / composer.lock)',
            $this->renderChangeList($appComposer),
        ];

        if ($deployComposer) {
            $sections[] = '## Changed packages (PHP / deploy/composer.lock)';
            $sections[] = $this->renderChangeList($deployComposer);
        }

        $sections[] = '## Changed packages (JavaScript / package-lock.json)';
        $sections[] = $this->renderChangeList($npm);

        $sections[] = '## Security audit results (appended to the PR body verbatim)';
        $sections[] = $auditSection;

        if ($appComposerDiff !== '') {
            $sections[] = '## Raw lock-file diff (composer.lock)';
            $sections[] = '```diff';
            $sections[] = $appComposerDiff;
            $sections[] = '```';
        }
        if ($deployComposerDiff !== '') {
            $sections[] = '## Raw lock-file diff (deploy/composer.lock)';
            $sections[] = '```diff';
            $sections[] = $deployComposerDiff;
            $sections[] = '```';
        }
        if ($npmDiff !== '') {
            $sections[] = '## Raw lock-file diff (package-lock.json)';
            $sections[] = '```diff';
            $sections[] = $npmDiff;
            $sections[] = '```';
        }

        return implode("\n", $sections);
    }

    /**
     * @param list<array{name: string, oldVersion: ?string, newVersion: ?string, repo: ?string, releaseNotes: ?string}> $changes
     */
    private function renderChangeList(array $changes): string
    {
        if (!$changes) {
            return "(no changes)\n";
        }
        $lines = [];
        foreach ($changes as $c) {
            $line = sprintf(
                '- %s: %s -> %s',
                $c['name'],
                $c['oldVersion'] ?? '(none)',
                $c['newVersion'] ?? '(removed)',
            );
            if (is_string($c['repo'])) {
                $line .= sprintf(' (https://github.com/%s)', $c['repo']);
            }
            $lines[] = $line;
            if (is_string($c['releaseNotes']) && $c['releaseNotes'] !== '') {
                $notes = $c['releaseNotes'];
                if (strlen($notes) > self::NOTES_PER_PACKAGE_LIMIT) {
                    $notes = substr($notes, 0, self::NOTES_PER_PACKAGE_LIMIT) . "\n... (truncated)";
                }
                $lines[] = sprintf('  Release notes for %s:', $c['newVersion'] ?? '?');
                foreach (explode("\n", $notes) as $noteLine) {
                    $lines[] = '  > ' . $noteLine;
                }
            }
        }
        return implode("\n", $lines) . "\n";
    }

    private function buildSystemPrompt(): string
    {
        return <<<'PROMPT'
You are an assistant that summarizes a dependency-update pull request for a PHP/JavaScript project (stat.ink).

Output only Markdown. Do not wrap the entire output in a code block.

Use this exact structure:

## Highlights / 注目すべき変更

(Up to 6 bullets surfacing security fixes, breaking changes, major version bumps, or notable new features. Each bullet is one short line written first in Japanese, then a slash and the same content in English. If there is nothing notable, output a single bullet "- 大きな変更はありません / No notable changes.")

## PHP packages (composer)

For each changed package in the PHP / composer.lock list, one bullet:
- `<name>`: `<old>` -> `<new>` — Japanese summary / English summary

If `deploy/composer.lock` has any changes, add a sub-section "### deploy/composer.lock" after the main composer list and bullet those packages there in the same format.

## JavaScript packages (npm)

Same format as PHP packages, for each changed npm package.

Rules:
- Be concise: one short sentence per package, mirrored in Japanese / English.
- Surface CVE / security / breaking-change information prominently when release notes mention it.
- Do not invent details that are not present in the input.
- If a package was added or removed (no old or new version), say so explicitly.
- Patch updates with no notable release notes may be grouped at the end of the section as "- and N other patch updates / その他 N 件のパッチ更新".
- Do not include a trailing footer like "This is an automated pull-request" — that line is appended separately.
- The security audit results are appended to the PR body separately; do not reproduce them as a section or table.
  However, if the update fixed or introduced any advisory, mention it in Highlights (fixed ones first).
PROMPT;
    }

    private function callLlm(string $endpoint, string $apiKey, string $model, string $userPrompt): ?string
    {
        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $this->buildSystemPrompt()],
                ['role' => 'user', 'content' => $userPrompt],
            ],
            'temperature' => 0.2,
        ];
        $payloadJson = Json::encode($payload);

        for ($attempt = 0; $attempt <= self::LLM_RETRY; $attempt++) {
            if ($attempt > 0) {
                $wait = 2 ** $attempt;
                vfprintf(STDERR, "[info] Retrying LLM call in %ds (attempt %d/%d)...\n", [
                    $wait,
                    $attempt + 1,
                    self::LLM_RETRY + 1,
                ]);
                sleep($wait);
            }
            try {
                $response = $this->httpClient()
                    ->createRequest()
                    ->setOptions([
                        'timeout' => self::LLM_TIMEOUT,
                        'maxRedirects' => 5,
                        'userAgent' => 'stat.ink-deps-summarizer',
                    ])
                    ->setMethod('POST')
                    ->setUrl($endpoint)
                    ->setHeaders([
                        'Authorization' => 'Bearer ' . $apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->setContent($payloadJson)
                    ->send();
                if (!$response->isOk) {
                    vfprintf(STDERR, "[warn] LLM HTTP %d: %s\n", [
                        $response->statusCode,
                        substr((string)$response->content, 0, 500),
                    ]);
                    continue;
                }
                $data = Json::decode((string)$response->content);
                if (!is_array($data)) {
                    fwrite(STDERR, "[warn] Could not decode LLM response\n");
                    continue;
                }
                $content = $this->extractMessageContent($data);
                if (is_string($content) && trim($content) !== '') {
                    return $content;
                }
                fwrite(STDERR, "[warn] Empty content in LLM response\n");
            } catch (Throwable $e) {
                vfprintf(STDERR, "[warn] LLM call exception: %s\n", [$e->getMessage()]);
            }
        }
        return null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function extractMessageContent(array $data): ?string
    {
        $choices = $data['choices'] ?? null;
        if (!is_array($choices) || !isset($choices[0]) || !is_array($choices[0])) {
            return null;
        }
        $message = $choices[0]['message'] ?? null;
        if (!is_array($message)) {
            return null;
        }
        $content = $message['content'] ?? null;
        return is_string($content) ? $content : null;
    }
}
