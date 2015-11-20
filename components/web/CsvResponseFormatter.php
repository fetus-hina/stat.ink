<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use Yii;
use yii\base\Component;
use yii\web\ResponseFormatterInterface;
use app\components\helpers\Resource;

class CsvResponseFormatter extends Component implements ResponseFormatterInterface
{
    public $inputCharset;
    public $outputCharset;

    public function format($response)
    {
        $this->inputCharset = @$response->data['inputCharset'] ?: Yii::$app->charset;
        $this->outputCharset = @$response->data['outputCharset'] ?: Yii::$app->charset;
        
        // 代替文字
        $substitute = new Resource(
            mb_substitute_character(),
            function ($old) {
                mb_substitute_character($old);
            }
        );
        mb_substitute_character(0x3013);

        $tmpfile = tmpfile();
        foreach ($response->data['rows'] as $row) {
            fwrite($tmpfile, $this->formatRow($row) . "\x0d\x0a");
        }
        fseek($tmpfile, 0, SEEK_SET);
        $response->content = null;
        $response->stream = $tmpfile;
    }

    protected function formatRow(array $row)
    {
        $ret = array_map(
            function ($cell) {
                $cell = mb_convert_encoding((string)$cell, $this->outputCharset, $this->inputCharset);
                if (!preg_match('/[",\x0d\x0a]/', $cell)) {
                    return $cell;
                }
                return '"' . mb_str_replace('"', '""', $cell, $this->outputCharset)  . '"';
            },
            $row
        );
        return implode(',', $ret);
    }
}
