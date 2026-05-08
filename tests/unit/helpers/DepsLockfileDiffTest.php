<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\DepsLockfileDiff;

final class DepsLockfileDiffTest extends Unit
{
    public function testExtractGithubRepoFromHttpsUrl(): void
    {
        $this->assertSame(
            'cebe/markdown',
            DepsLockfileDiff::extractGithubRepo('https://github.com/cebe/markdown.git'),
        );
    }

    public function testExtractGithubRepoFromSshUrl(): void
    {
        $this->assertSame(
            'foo/bar',
            DepsLockfileDiff::extractGithubRepo('git@github.com:foo/bar.git'),
        );
    }

    public function testExtractGithubRepoReturnsNullForNonGithub(): void
    {
        $this->assertNull(DepsLockfileDiff::extractGithubRepo('https://gitlab.com/foo/bar.git'));
    }

    public function testExtractGithubRepoReturnsNullForNullInput(): void
    {
        $this->assertNull(DepsLockfileDiff::extractGithubRepo(null));
    }

    public function testDiffComposerPackagesDetectsVersionBump(): void
    {
        $old = [
            'packages' => [
                [
                    'name' => 'foo/bar',
                    'version' => '1.0.0',
                    'source' => ['url' => 'https://github.com/foo/bar.git'],
                ],
            ],
            'packages-dev' => [],
        ];
        $new = [
            'packages' => [
                [
                    'name' => 'foo/bar',
                    'version' => '1.1.0',
                    'source' => ['url' => 'https://github.com/foo/bar.git'],
                ],
            ],
            'packages-dev' => [],
        ];
        $this->assertSame(
            [[
                'name' => 'foo/bar',
                'oldVersion' => '1.0.0',
                'newVersion' => '1.1.0',
                'repo' => 'foo/bar',
            ]],
            DepsLockfileDiff::diffComposerPackages($old, $new),
        );
    }

    public function testDiffComposerPackagesDetectsAddedAndRemoved(): void
    {
        $old = [
            'packages' => [
                ['name' => 'a/removed', 'version' => '1.0.0'],
            ],
            'packages-dev' => [],
        ];
        $new = [
            'packages' => [
                ['name' => 'a/added', 'version' => '2.0.0'],
            ],
            'packages-dev' => [],
        ];
        $changes = DepsLockfileDiff::diffComposerPackages($old, $new);
        $this->assertCount(2, $changes);
        // Sorted alphabetically by name
        $this->assertSame('a/added', $changes[0]['name']);
        $this->assertNull($changes[0]['oldVersion']);
        $this->assertSame('2.0.0', $changes[0]['newVersion']);
        $this->assertSame('a/removed', $changes[1]['name']);
        $this->assertSame('1.0.0', $changes[1]['oldVersion']);
        $this->assertNull($changes[1]['newVersion']);
    }

    public function testDiffComposerPackagesIgnoresUnchanged(): void
    {
        $data = [
            'packages' => [
                ['name' => 'foo/bar', 'version' => '1.0.0'],
            ],
            'packages-dev' => [],
        ];
        $this->assertSame([], DepsLockfileDiff::diffComposerPackages($data, $data));
    }

    public function testDiffComposerPackagesIncludesDevPackages(): void
    {
        $old = [
            'packages' => [],
            'packages-dev' => [
                ['name' => 'foo/devtool', 'version' => '1.0.0'],
            ],
        ];
        $new = [
            'packages' => [],
            'packages-dev' => [
                ['name' => 'foo/devtool', 'version' => '1.0.1'],
            ],
        ];
        $changes = DepsLockfileDiff::diffComposerPackages($old, $new);
        $this->assertCount(1, $changes);
        $this->assertSame('foo/devtool', $changes[0]['name']);
    }

    public function testDiffNpmPackagesDetectsVersionBump(): void
    {
        $old = [
            'packages' => [
                '' => ['name' => 'root'],
                'node_modules/react' => ['version' => '18.2.0'],
            ],
        ];
        $new = [
            'packages' => [
                '' => ['name' => 'root'],
                'node_modules/react' => ['version' => '18.3.0'],
            ],
        ];
        $this->assertSame(
            [['name' => 'react', 'oldVersion' => '18.2.0', 'newVersion' => '18.3.0']],
            DepsLockfileDiff::diffNpmPackages($old, $new),
        );
    }

    public function testDiffNpmPackagesSkipsNestedDependencies(): void
    {
        $old = [
            'packages' => [
                'node_modules/foo' => ['version' => '1.0.0'],
                'node_modules/foo/node_modules/bar' => ['version' => '2.0.0'],
            ],
        ];
        $new = [
            'packages' => [
                'node_modules/foo' => ['version' => '1.0.0'],
                'node_modules/foo/node_modules/bar' => ['version' => '3.0.0'],
            ],
        ];
        $this->assertSame([], DepsLockfileDiff::diffNpmPackages($old, $new));
    }

    public function testDiffNpmPackagesHandlesScopedPackages(): void
    {
        $old = ['packages' => [
            'node_modules/@scope/pkg' => ['version' => '1.0.0'],
        ]];
        $new = ['packages' => [
            'node_modules/@scope/pkg' => ['version' => '1.1.0'],
        ]];
        $changes = DepsLockfileDiff::diffNpmPackages($old, $new);
        $this->assertCount(1, $changes);
        $this->assertSame('@scope/pkg', $changes[0]['name']);
        $this->assertSame('1.0.0', $changes[0]['oldVersion']);
        $this->assertSame('1.1.0', $changes[0]['newVersion']);
    }
}
