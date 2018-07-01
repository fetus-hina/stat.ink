<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Curl\Curl;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\FileHelper;
use yii\helpers\Json;

class MapImage2Controller extends Controller
{
    public $dlApiUrl = 'https://splapi2.stat.ink/stages';
    public $baseDir = '@app/resources/maps2';

    public function actionGenerate() : int
    {
        $this->generateFromSplapi2();
        $this->generateFromTwitter();
        return 0;
    }

    private function generateFromSplapi2()
    {
        $json = $this->downloadFromSplapi2();
        foreach ($json as $stage) {
            if (!$stage->key || !$stage->image) {
                continue;
            }
            $this->stderr("Processing " . $stage->key . " ...\n");
            $inputPngPath = Yii::getAlias($this->baseDir) . '/' . $stage->key . '.png';
            if (!file_exists($inputPngPath) || filesize($inputPngPath) < 1024) {
                $this->stderr("  Download base image...\n");
                file_put_contents(
                    $inputPngPath,
                    $this->downloadImage($stage->image)
                );
            }
            $this->convertToImages($inputPngPath, $stage->key);
        }
    }

    private function generateFromTwitter()
    {
        $list = [
            'ajifry' => 'https://pbs.twimg.com/media/DXLF40vUMAEQR_C.jpg:orig',
            'arowana' => 'https://pbs.twimg.com/media/DPSOeyvVoAAGnWL.jpg:orig',
            'bbass' => 'https://pbs.twimg.com/media/DNEAZ9cUIAAphwV.jpg:orig',
            'devon' => 'https://pbs.twimg.com/media/DPOdfAvVAAArv3f.jpg:orig',
            'engawa' => 'https://pbs.twimg.com/media/DLXOzECUEAAyMkl.jpg:orig',
            'hakofugu' => 'https://pbs.twimg.com/media/DPNH39SVQAAIPiE.jpg:orig',
            'mongara' => 'https://pbs.twimg.com/media/DbhDqsRVAAAgk3U.jpg:orig',
            'mozuku' => 'https://pbs.twimg.com/media/DJo7HTzUEAESeuF.jpg:orig',
            'mystery' => 'https://pbs.twimg.com/media/DGDTWhAUwAA4Bri.jpg:orig',
            'otoro' => 'https://pbs.twimg.com/media/Dg09LC9V4AEpYc9.jpg:orig',
            'shottsuru' => 'https://pbs.twimg.com/media/DYPMhr4UMAAkrG5.jpg:orig',
            'sumeshi' => 'https://pbs.twimg.com/media/DYPMwDjVQAAyI2_.jpg:orig',
            'zatou' => 'https://pbs.twimg.com/media/DPKV9mCV4AExVBF.jpg:orig',

            'damu' => 'https://cdn.wikimg.net/splatoonwiki/images/2/29/S2_Stage_Spawning_Grounds.png',
            'donburako' => 'https://cdn.wikimg.net/splatoonwiki/images/6/6c/S2_Stage_Marooner%27s_Bay.png',
            'shaketoba' => 'https://cdn.wikimg.net/splatoonwiki/images/6/68/S2_Stage_Lost_Outpost.png',
            'tokishirazu' => 'https://cdn.wikimg.net/splatoonwiki/images/c/c7/S2_Stage_Salmonid_Smokeyard.png',
        ];
        foreach ($list as $stage => $url) {
            $this->stderr("Processing " . $stage . " ...\n");
            $inputJpgPath = Yii::getAlias($this->baseDir) . '/' . $stage . '.jpg';
            if (!file_exists($inputJpgPath) || filesize($inputJpgPath) < 1024) {
                $this->stderr("  Download base image...\n");
                file_put_contents(
                    $inputJpgPath,
                    $this->downloadImage($url)
                );
            }
            $this->convertToImages($inputJpgPath, $stage);
        }
    }

    private function convertToImages(string $inPath, string $stage) : void
    {
        $paths = [
            'daytime' => Yii::getAlias($this->baseDir) . '/assets/daytime/' . $stage . '.jpg',
            'daytime-blur' => Yii::getAlias($this->baseDir) . '/assets/daytime-blur/' . $stage . '.jpg',
            'gray-blur' => Yii::getAlias($this->baseDir) . '/assets/gray-blur/' . $stage . '.jpg',
        ];
        foreach ($paths as $pattern => $path) {
            if (!file_exists($path) || filesize($path) < 1024) {
                $this->stderr("  Create image " . $pattern . "...\n");
                $this->convert($pattern, $inPath, $path);
            }
        }
    }

    private function convert(string $pattern, string $inPath, string $outPath)
    {
        switch ($pattern) {
            case 'daytime':
                return $this->execConvert(
                    '-resize 320x180',
                    $inPath,
                    $outPath
                );

            case 'daytime-blur':
                return $this->execConvert(
                    '-resize 320x180 -blur 2x2',
                    $inPath,
                    $outPath
                );

            case 'gray-blur':
                return $this->execConvert(
                    sprintf(
                        '-fx %s -colorspace Gray -resize 320x180 -blur 2x2',
                        escapeshellarg('r*0.299+g*0.587+b*0.114')
                    ),
                    $inPath,
                    $outPath
                );
        }
    }

    private function execConvert(string $commands, string $inPath, string $outPath)
    {
        FileHelper::createDirectory(dirname($outPath));
        $cmdline = sprintf(
            'convert %s %s -quality 95 %s',
            escapeshellarg($inPath),
            $commands,
            escapeshellarg($outPath)
        );
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            throw new \Exception('Could not execute ' . $cmdline);
        }
        $cmdline = sprintf('jpegoptim -qs %s', escapeshellarg($outPath));
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            throw new \Exception('Could not execute ' . $cmdline);
        }
    }

    private function downloadFromSplapi2() : array
    {
        return Json::decode(
            $this->download($this->dlApiUrl),
            false
        );
    }

    private function downloadImage(string $url) : string
    {
        $binary = $this->download($url);
        if (!$gd = @imagecreatefromstring($binary)) {
            throw new \Exception('Downloaded binary is not an image');
        }
        imagedestroy($gd);
        return $binary;
    }

    private function download(string $url) : string
    {
        $curl = new Curl();
        $curl->setUserAgent(sprintf(
            '%s/%s (+%s)',
            'stat.ink',
            Yii::$app->version,
            'https://github.com/fetus-hina/stat.ink'
        ));
        $curl->get($url);
        if ($curl->error) {
            throw new \Exception("Request failed: url={$url}, code={$curl->errorCode}, msg={$curl->errorMessage}");
        }
        return $curl->rawResponse;
    }
}
