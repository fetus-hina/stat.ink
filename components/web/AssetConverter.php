<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\web\AssetConverterInterface;

class AssetConverter extends Component implements AssetConverterInterface
{
    public $extensions = [
        'js',
        'css',
    ];

    public $forceConvert = false;

    public function convert($asset, $basePath)
    {
        $pos = strrpos($asset, '.');
        if ($pos !== false) {
            $ext = strtolower(substr($asset, $pos + 1));
            if (in_array($ext, $this->extensions, true)) {
                $inPath = $basePath . '/' . $asset;
                $outPath = $basePath . '/' . $asset . '.gz';
                if (file_exists($inPath)) {
                    if ($this->forceConvert ||
                            !file_exists($outPath) ||
                            @filemtime($inPath) < @filemtime($outPath)
                    ) {
                        $this->gzip($inPath, $outPath);
                    }
                }
            }
        }
        return $asset;
    }

    private function gzip($inPath, $outPath)
    {
        $inText = file_get_contents($inPath, false, null);
        if ($inText === false) {
            $this->error(__METHOD__, "AssetConverter failed to read input file: {$inPath}");
            return false;
        }
        $wrote = file_put_contents($outPath, gzencode($inText, 9, FORCE_GZIP), LOCK_EX);
        if ($wrote === false) {
            $this->error(__METHOD__, "AssetConvert failed to write output file: {$outPath}");
            return false;
        }
        return true;
    }

    private function error($method, $message)
    {
        if (YII_DEBUG) {
            throw new Exception($message);
        } else {
            Yii::error($message, $method);
        }
    }
}
