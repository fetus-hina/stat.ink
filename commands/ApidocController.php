<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\commands;

use Yii;
use app\components\openapi\doc\V1 as V1Generator;
use yii\console\Controller;
use yii\helpers\FileHelper;

class ApidocController extends Controller
{
    public $defaultAction = 'create';
    public $layout = false;

    public $languages = [
        'en' => 'en-US',
        'ja' => 'ja-JP',
    ];

    public function actionCreate(): int
    {
        $successful = true;
        foreach ($this->languages as $langCodeShort => $langCodeLong) {
            if (!$this->create($langCodeShort, $langCodeLong)) {
                $successful = false;
            }
        }
        return $successful ? 0 : 1;
    }

    private function create(string $langCodeShort, string $langCodeLong): bool
    {
        Yii::$app->language = $langCodeLong;

        $successful = true;
        $successful = $this->createV1($langCodeShort) && $successful;
        return $successful;
    }

    private function createV1(string $langCode): bool
    {
        $generator = Yii::createObject([
            'class' => V1Generator::class,
        ]);

        $this->stderr(__METHOD__ . "(): {$langCode}: Creating JSON...\n");
        $jsonPath = vsprintf('%s/runtime/apidoc/%s.json', [
            Yii::getAlias('@app'),
            vsprintf('%d-%08x', [
                time(),
                mt_rand(0, 0xffffffff),
            ]),
        ]);
        FileHelper::createDirectory(dirname($jsonPath));
        $json = $generator->render();
        if (@file_put_contents($jsonPath, $json) === false) {
            $this->stderr(__METHOD__ . "(): {$langCode}: Failed to create a json file!\n");
            return false;
        }

        $this->stderr(__METHOD__ . "(): {$langCode}: Checking syntax...\n");
        $cmdline = vsprintf('/usr/bin/env %s lint %s', [
            escapeshellarg(Yii::getAlias('@app/node_modules/.bin/speccy')),
            escapeshellarg($jsonPath),
        ]);
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            $this->stderr(__METHOD__ . "(): {$langCode}: Lint failed (status={$status}).\n");
            $this->stderr("json: {$jsonPath}\n");
            $this->stderr(implode("\n", $lines) . "\n");
            return false;
        }

        $this->stderr(__METHOD__ . "(): {$langCode}: Creating HTML...\n");
        $outPath = vsprintf('%s/web/apidoc/v1.%s.html', [
            Yii::getAlias('@app'),
            $langCode,
        ]);
        $cmdline = vsprintf('/usr/bin/env %s bundle -o %s --title %s %s', [
            escapeshellarg(Yii::getAlias('@app/node_modules/.bin/redoc-cli')),
            escapeshellarg($outPath),
            escapeshellarg(Yii::t('app-apidoc1', 'stat.ink API for Splatoon 1')),
            escapeshellarg($jsonPath),
        ]);
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            $this->stderr(__METHOD__ . "(): {$langCode}: Create failed (status={$status}).\n");
            $this->stderr("json: {$jsonPath}\n");
            $this->stderr(implode("\n", $lines) . "\n");
            return false;
        }
        $this->stderr(__METHOD__ . "(): OK\n");

        return true;
    }
}
