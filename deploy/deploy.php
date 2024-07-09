<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/yii.php';

set('repository', 'git@github.com:fetus-hina/stat.ink.git');
set('shared_dirs', [
    'data/GeoIP',
    'runtime/logs',
]);
set('writable_dirs', [
    'runtime',
    'runtime/logs',
]);
add('shared_files', []);

set('bin/make', fn (): string => which('make'));
set('bin/npm', fn (): string => which('npm'));
set('branch', 'master');
set('composer_options', '--verbose --prefer-dist --no-progress --no-interaction --optimize-autoloader');
set('http_user', '{{remote_user}}');
set('nproc', 2);
set('update_code_strategy', 'clone');

// Hosts

host('app2.stat.ink')
    ->set('hostname', '192.168.0.27')
    ->set('remote_user', 'stat.ink')
    ->set('deploy_path', '~/app.dep')
    ->set('nproc', 8);

// Hooks
after('deploy:update_code', 'deploy:production');
after('deploy:update_code', 'deploy:copy_config');
after('deploy:update_code', 'deploy:link_storages');

task('deploy:production', function (): void {
    within('{{release_or_current_path}}', function (): void {
        run('touch .production');
    });
});

task('deploy:copy_config', function (): void {
    within('{{release_or_current_path}}', function (): void {
        run('rm -rf ./config');
        run('cp -a {{deploy_path}}/shared/config/ ./config/');
    });
});

task('deploy:link_storages', function (): void {
    $paths = [
        'runtime' => [
            'dl-stats' => '/mnt/dl-stats',
            'hasegaw-export' => '/mnt/hasegaw-export',
            'image-archive' => '/mnt/image-archive',
            'salmon-json3' => '/mnt/salmon-json3',
            'user-json' => '/mnt/user-json',
            'user-json3' => '/mnt/user-json3',
        ],
        'web' => [
            'profile-images' => '/mnt/profile-images',
        ],
    ];

    foreach ($paths as $baseDir => $pathMap) {
        within("{{release_or_current_path}}/{$baseDir}", function () use ($pathMap): void {
            foreach ($pathMap as $dstPath => $srcPath) {
                run(
                    vsprintf('ln -s %s %s', [
                        escapeshellarg($srcPath),
                        escapeshellarg($dstPath),
                    ]),
                );
            }
        });
    }
});

before('deploy:vendors', function (): void {
    within('{{release_or_current_path}}', function (): void {
        run(
            vsprintf('ln -sf %s %s', [
                escapeshellarg(which('composer')),
                escapeshellarg('composer.phar'),
            ]),
        );
    });
});

task('deploy:vendors', function (): void {
    within('{{release_or_current_path}}', function (): void {
        run('{{bin/composer}} {{composer_action}} {{composer_options}} 2>&1');
        run('{{bin/npm}} clean-install');
    });
});

after('deploy:vendors', 'deploy:build');

task('deploy:build', function (): void {
    within('{{release_or_current_path}}', function (): void {
        run('{{bin/make}} -j {{nproc}} init-no-resource resource');
        run('{{bin/php}} yii asset/up-revision');
    });
});

after('deploy:symlink', 'deploy:restart-queue-runner');

task('deploy:restart-queue-runner', function (): void {
    within('{{current_path}}/bin', function (): void {
        run('sudo ./restart-queue-runner.sh');
    });
});

after('deploy:failed', 'deploy:unlock');
