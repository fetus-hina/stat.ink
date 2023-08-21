<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons;

use Throwable;
use yii\base\InvalidConfigException;
use yii\web\AssetBundle as BaseAssetBundle;
use yii\web\ServerErrorHttpException;

use function basename;
use function chdir;
use function dirname;
use function file_exists;
use function getcwd;
use function is_array;
use function is_link;
use function is_string;
use function symlink;

abstract class AssetBundle extends BaseAssetBundle
{
    /**
     * @var array<string, string|string[]>
     */
    public array $fileNameMap = [];

    /**
     * @inheritdoc
     * @return void
     */
    public function init()
    {
        parent::init();

        if (!isset($this->publishOptions['afterCopy'])) {
            $this->publishOptions['afterCopy'] = fn (string $from, string $to) => $this->onAfterCopy(
                $from,
                $to,
            );
        }
    }

    protected function onAfterCopy(string $from, string $to): void
    {
        $name = basename($to);
        $linkNames = $this->fileNameMap[$name] ?? null;

        if ($linkNames === null) {
            return;
        }

        if (!is_string($linkNames) && !is_array($linkNames)) {
            throw new InvalidConfigException(
                'fileNameMap must be array<string, string|string[]>',
            );
        }

        foreach ((array)$linkNames as $linkName) {
            if (!is_string($linkName)) {
                throw new InvalidConfigException(
                    'fileNameMap must be array<string, string|string[]>',
                );
            }

            if (is_string($linkName) && !file_exists(dirname($to) . '/' . $linkName)) {
                $this->makeSymlink(
                    dirname($to),
                    $name,
                    $linkName,
                );
            }
        }
    }

    protected function makeSymlink(string $directory, string $from, string $to): void
    {
        $cwd = getcwd();
        if (!@chdir($directory)) {
            throw new ServerErrorHttpException(
                "Could not chdir to {$directory}",
            );
        }

        try {
            // マルチスレッドで実行した際にはここが失敗するらしいので、
            // 一旦例外を受け取ってから実際にリンクが張られているかどうかを確認する
            symlink($from, $to);
        } catch (Throwable $e) {
            if (!is_link($to)) {
                throw $e;
            }
        } finally {
            @chdir($cwd);
        }
    }
}
