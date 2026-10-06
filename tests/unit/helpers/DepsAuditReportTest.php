<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\DepsAuditReport;

use function array_column;

final class DepsAuditReportTest extends Unit
{
    public function testParseComposerAudit(): void
    {
        $data = [
            'advisories' => [
                'guzzlehttp/psr7' => [
                    [
                        'advisoryId' => 'PKSA-vznr-tgp9-fd7d',
                        'packageName' => 'guzzlehttp/psr7',
                        'affectedVersions' => '<2.12.3',
                        'title' => 'Host Confusion',
                        'cve' => 'CVE-2026-59882',
                        'link' => 'https://github.com/advisories/GHSA-c2w2-prh8-qm98',
                        'severity' => 'medium',
                    ],
                ],
            ],
            'abandoned' => [],
        ];

        $this->assertSame(
            [[
                'package' => 'guzzlehttp/psr7',
                'id' => 'PKSA-vznr-tgp9-fd7d',
                'title' => 'Host Confusion',
                'severity' => 'medium',
                'cve' => 'CVE-2026-59882',
                'link' => 'https://github.com/advisories/GHSA-c2w2-prh8-qm98',
                'affected' => '<2.12.3',
                'fix' => null,
            ]],
            DepsAuditReport::parseComposerAudit($data),
        );
    }

    public function testParseComposerAuditWithNoAdvisories(): void
    {
        $this->assertSame([], DepsAuditReport::parseComposerAudit(['advisories' => [], 'abandoned' => []]));
        $this->assertSame([], DepsAuditReport::parseComposerAudit([]));
    }

    public function testParseComposerAbandoned(): void
    {
        $this->assertSame(
            ['a/old' => 'b/new', 'c/dead' => null],
            DepsAuditReport::parseComposerAbandoned([
                'abandoned' => ['c/dead' => null, 'a/old' => 'b/new'],
            ]),
        );
        $this->assertSame([], DepsAuditReport::parseComposerAbandoned(['abandoned' => []]));
    }

    public function testParseNpmAuditTakesOnlyAdvisoryEntries(): void
    {
        $data = [
            'auditReportVersion' => 2,
            'vulnerabilities' => [
                'braces' => [
                    'name' => 'braces',
                    'severity' => 'high',
                    'via' => [
                        [
                            'source' => 1240992,
                            'name' => 'braces',
                            'title' => 'Uncontrolled resource consumption',
                            'url' => 'https://github.com/advisories/GHSA-grv7-fg5c-xmjg',
                            'severity' => 'high',
                            'range' => '<3.0.3',
                        ],
                    ],
                    'fixAvailable' => true,
                ],
                // propagated only (via is a package name), not an advisory itself
                'chokidar' => [
                    'name' => 'chokidar',
                    'severity' => 'high',
                    'via' => ['braces'],
                    'fixAvailable' => true,
                ],
                'bootstrap' => [
                    'name' => 'bootstrap',
                    'severity' => 'moderate',
                    'via' => [
                        [
                            'source' => 1108098,
                            'name' => 'bootstrap',
                            'title' => 'XSS',
                            'url' => 'https://github.com/advisories/GHSA-q58r-hwc8-rm9j',
                            'severity' => 'moderate',
                            'range' => '=3.4.1',
                        ],
                    ],
                    'fixAvailable' => [
                        'name' => 'bootstrap',
                        'version' => '5.3.8',
                        'isSemVerMajor' => true,
                    ],
                ],
            ],
        ];

        $this->assertSame(
            [
                [
                    'package' => 'braces',
                    'id' => 'GHSA-grv7-fg5c-xmjg',
                    'title' => 'Uncontrolled resource consumption',
                    'severity' => 'high',
                    'cve' => null,
                    'link' => 'https://github.com/advisories/GHSA-grv7-fg5c-xmjg',
                    'affected' => '<3.0.3',
                    'fix' => '`npm audit fix`',
                ],
                [
                    'package' => 'bootstrap',
                    'id' => 'GHSA-q58r-hwc8-rm9j',
                    'title' => 'XSS',
                    'severity' => 'moderate',
                    'cve' => null,
                    'link' => 'https://github.com/advisories/GHSA-q58r-hwc8-rm9j',
                    'affected' => '=3.4.1',
                    'fix' => '`bootstrap@5.3.8` (major)',
                ],
            ],
            DepsAuditReport::parseNpmAudit($data),
        );
    }

    public function testCompareClassifiesAdvisories(): void
    {
        $fixed = $this->advisory('a/a', 'ID-1', 'low');
        $remaining = $this->advisory('b/b', 'ID-2', 'medium');
        $introduced = $this->advisory('c/c', 'ID-3', 'high');

        $result = DepsAuditReport::compare([$fixed, $remaining], [$remaining, $introduced]);

        $this->assertSame([$fixed], $result['fixed']);
        $this->assertSame([$introduced], $result['introduced']);
        $this->assertSame([$remaining], $result['remaining']);
    }

    public function testCompareSortsBySeverityDescending(): void
    {
        $low = $this->advisory('z/z', 'ID-1', 'low');
        $critical = $this->advisory('y/y', 'ID-2', 'critical');
        $moderate = $this->advisory('x/x', 'ID-3', 'moderate');
        $high = $this->advisory('w/w', 'ID-4', 'high');

        $result = DepsAuditReport::compare([], [$low, $critical, $moderate, $high]);

        $this->assertSame(
            ['ID-2', 'ID-4', 'ID-3', 'ID-1'],
            array_column($result['introduced'], 'id'),
        );
    }

    public function testRenderSectionWithoutVulnerabilities(): void
    {
        $md = DepsAuditReport::renderSection('PHP (composer.lock)', [], []);

        $this->assertStringContainsString('### PHP (composer.lock)', $md);
        $this->assertStringContainsString('No known vulnerabilities', $md);
    }

    public function testRenderSectionWithChanges(): void
    {
        $fixed = $this->advisory('a/a', 'ID-1', 'high', 'Bad | thing');
        $remaining = $this->advisory('b/b', 'ID-2', 'medium');

        $md = DepsAuditReport::renderSection('PHP (composer.lock)', [$fixed, $remaining], [$remaining]);

        $this->assertStringContainsString('Fixed / 解消: 1', $md);
        $this->assertStringContainsString('Introduced / 新規: 0', $md);
        $this->assertStringContainsString('Remaining / 残存: 1', $md);
        $this->assertStringContainsString('`a/a`', $md);
        $this->assertStringContainsString('Bad \\| thing', $md, 'pipe in table cell must be escaped');
        $this->assertStringContainsString('<details>', $md, 'remaining advisories are collapsed');
    }

    public function testRenderSectionWhenAuditFailed(): void
    {
        $md = DepsAuditReport::renderSection('JavaScript (package-lock.json)', [], null);

        $this->assertStringContainsString('Audit failed', $md);
    }

    public function testRenderSectionWhenBaselineUnavailable(): void
    {
        $md = DepsAuditReport::renderSection('PHP (composer.lock)', null, [$this->advisory('a/a', 'ID-1', 'high')]);

        $this->assertStringContainsString('Comparison unavailable', $md);
        $this->assertStringContainsString('`a/a`', $md);
    }

    public function testRenderSectionListsAbandonedPackages(): void
    {
        $md = DepsAuditReport::renderSection('PHP (composer.lock)', [], [], ['a/old' => 'b/new', 'c/dead' => null]);

        $this->assertStringContainsString('`a/old` (replacement: `b/new`)', $md);
        $this->assertStringContainsString('`c/dead`', $md);
    }

    public function testFilterIgnoredMatchesAdvisoryIdCveOrLinkedGhsa(): void
    {
        $byId = $this->advisory('bootstrap', 'GHSA-aaaa-aaaa-aaaa', 'moderate');
        $byCve = ['cve' => 'CVE-2026-0001'] + $this->advisory('a/a', 'PKSA-xxxx-xxxx-xxxx', 'high');
        $byLink = ['link' => 'https://github.com/advisories/GHSA-bbbb-bbbb-bbbb']
            + $this->advisory('b/b', 'PKSA-yyyy-yyyy-yyyy', 'low');
        $kept = $this->advisory('c/c', 'GHSA-cccc-cccc-cccc', 'high');

        $result = DepsAuditReport::filterIgnored(
            [$byId, $byCve, $byLink, $kept],
            [
                ['id' => 'ghsa-aaaa-aaaa-aaaa'],
                ['id' => 'CVE-2026-0001'],
                ['id' => 'GHSA-bbbb-bbbb-bbbb'],
            ],
        );

        $this->assertSame([$kept], $result['kept']);
        $this->assertSame([$byId, $byCve, $byLink], $result['ignored']);
    }

    public function testFilterIgnoredRespectsPackageRestriction(): void
    {
        $bootstrap = $this->advisory('bootstrap', 'GHSA-aaaa-aaaa-aaaa', 'moderate');
        $other = $this->advisory('other', 'GHSA-aaaa-aaaa-aaaa', 'moderate');

        $result = DepsAuditReport::filterIgnored(
            [$bootstrap, $other],
            [['id' => 'GHSA-aaaa-aaaa-aaaa', 'package' => 'bootstrap']],
        );

        $this->assertSame([$other], $result['kept']);
        $this->assertSame([$bootstrap], $result['ignored']);
    }

    public function testRenderSectionExcludesIgnoredAdvisories(): void
    {
        $ignored = $this->advisory('bootstrap', 'GHSA-aaaa-aaaa-aaaa', 'moderate');
        $rules = [['id' => 'GHSA-aaaa-aaaa-aaaa']];

        $md = DepsAuditReport::renderSection('JavaScript (package-lock.json)', [$ignored], [$ignored], [], $rules);

        $this->assertStringContainsString('No known vulnerabilities', $md);
        $this->assertStringNotContainsString('Remaining', $md);
        $this->assertStringContainsString('Ignored / 除外 (1)', $md);
    }

    public function testRenderSectionDoesNotReportIgnoredAdvisoryAsFixed(): void
    {
        $ignored = $this->advisory('bootstrap', 'GHSA-aaaa-aaaa-aaaa', 'moderate');
        $remaining = $this->advisory('b/b', 'ID-2', 'medium');
        $rules = [['id' => 'GHSA-aaaa-aaaa-aaaa']];

        $md = DepsAuditReport::renderSection('PHP (composer.lock)', [$ignored, $remaining], [$remaining], [], $rules);

        $this->assertStringContainsString('Fixed / 解消: 0', $md);
        $this->assertStringContainsString('Remaining / 残存: 1', $md);
        $this->assertStringNotContainsString('Ignored', $md);
    }

    /**
     * @return array{package: string, id: string, title: string, severity: string, cve: ?string, link: ?string, affected: ?string, fix: ?string}
     */
    private function advisory(string $package, string $id, string $severity, string $title = 'title'): array
    {
        return [
            'package' => $package,
            'id' => $id,
            'title' => $title,
            'severity' => $severity,
            'cve' => null,
            'link' => null,
            'affected' => null,
            'fix' => null,
        ];
    }
}
