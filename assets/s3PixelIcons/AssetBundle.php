<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets\s3PixelIcons;

use Throwable;
use Yii;
use app\components\helpers\TypeHelper;
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
     * @var string[]
     */
    public array $dummyFiles = [];

    private bool $isDummyFilesProcessed = false;

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
        Yii::beginProfile("onAfterCopy({$from}, {$to})", __METHOD__);
        try {
            $this->makeSymlinks($from, $to);

            if (!$this->isDummyFilesProcessed) {
                $this->makeDummyFiles($from, $to);
                $this->isDummyFilesProcessed = true;
            }
        } finally {
            Yii::endProfile("onAfterCopy({$from}, {$to})", __METHOD__);
        }
    }

    protected function makeSymlinks(string $from, string $to): void
    {
        Yii::beginProfile("makeSymlinks({$from}, {$to})", __METHOD__);
        try {
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

                if (is_string($linkName)) {
                    $this->makeSymlink(
                        dirname($to),
                        $name,
                        $linkName,
                    );
                }
            }
        } finally {
            Yii::endProfile("makeSymlinks({$from}, {$to})", __METHOD__);
        }
    }

    protected function makeDummyFiles(string $from, string $to): void
    {
        Yii::beginProfile("makeDummyFiles({$from}, {$to})", __METHOD__);
        try {
            if (!$this->dummyFiles) {
                return;
            }

            $dummyLinkTo = TypeHelper::string(Yii::getAlias('@app/resources/stat.ink/1x1.png'));
            foreach ($this->dummyFiles as $dummyFile) {
                $this->makeSymlink(
                    dirname($to),
                    $dummyLinkTo,
                    $dummyFile,
                );
            }
        } finally {
            Yii::endProfile("makeDummyFiles({$from}, {$to})", __METHOD__);
        }
    }

    protected function makeSymlink(string $directory, string $from, string $to): void
    {
        Yii::beginProfile("symlink({$from}, {$to})", __METHOD__);
        try {
            $cwd = getcwd();
            if (!@chdir($directory)) {
                throw new ServerErrorHttpException(
                    "Could not chdir to {$directory}",
                );
            }

            try {
                if (file_exists($to)) {
                    Yii::info("Skip create symlink: {$from} -> {$to} ({$directory})", __METHOD__);
                    return;
                }

                // マルチスレッドで実行した際にはここが失敗するらしいので、
                // 一旦例外を受け取ってから実際にリンクが張られているかどうかを確認する
                symlink($from, $to);

                Yii::info("Created symlink: {$from} -> {$to} ({$directory})", __METHOD__);
            } catch (Throwable $e) {
                if (!is_link($to)) {
                    throw $e;
                }
            } finally {
                @chdir($cwd);
            }
        } finally {
            Yii::endProfile("symlink({$from}, {$to})", __METHOD__);
        }
    }
}
