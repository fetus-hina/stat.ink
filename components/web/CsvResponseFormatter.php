<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\web;

use Yii;
use app\components\helpers\Resource;
use yii\base\Component;
use yii\web\ResponseFormatterInterface;

use function array_map;
use function fseek;
use function fwrite;
use function implode;
use function in_array;
use function mb_convert_encoding;
use function mb_str_replace;
use function mb_substitute_character;
use function preg_match;
use function preg_quote;
use function sprintf;
use function tmpfile;

use const SEEK_SET;

class CsvResponseFormatter extends Component implements ResponseFormatterInterface
{
    public const SEPARATOR_CSV = ',';
    public const SEPARATOR_TSV = "\t";

    public $separator;
    public $inputCharset;
    public $outputCharset;
    public $substituteCharacter;
    public $appendBOM;

    public function format($response)
    {
        $this->separator = $response->data['separator'] ?? static::SEPARATOR_CSV;
        $this->inputCharset = $response->data['inputCharset'] ?? Yii::$app->charset;
        $this->outputCharset = $response->data['outputCharset'] ?? Yii::$app->charset;
        $this->substituteCharacter = $response->data['substituteCharacter'] ?? 0x3013;
        $this->appendBOM = $response->data['appendBOM'] ?? false;

        // 代替文字
        $substitute = new Resource(
            mb_substitute_character(),
            function ($old) {
                mb_substitute_character($old);
            },
        );
        mb_substitute_character($this->substituteCharacter);

        $tmpfile = tmpfile();
        if ($this->appendBOM && in_array($this->outputCharset, ['UTF-8', 'UTF-16', 'UTF-32'])) {
            fwrite($tmpfile, mb_convert_encoding("\xfe\xff", $this->outputCharset, 'UTF-16BE'));
        }
        foreach ($response->data['rows'] as $row) {
            fwrite($tmpfile, $this->formatRow($row));
            fwrite($tmpfile, mb_convert_encoding("\x0d\x0a", $this->outputCharset, 'UTF-8'));
        }
        fseek($tmpfile, 0, SEEK_SET);
        $response->content = null;
        $response->stream = $tmpfile;

        unset($substitute);
    }

    protected function formatRow(array $row)
    {
        $quoteRegex = sprintf('/["\x0d\x0a%s]/', preg_quote($this->separator, '/'));
        $ret = array_map(
            function ($cell) use ($quoteRegex) {
                $utf8 = mb_convert_encoding((string)$cell, 'UTF-8', $this->inputCharset);
                if (preg_match($quoteRegex, $cell)) {
                    $utf8 = sprintf('"%s"', mb_str_replace('"', '""', $utf8, 'UTF-8'));
                }
                return mb_convert_encoding($utf8, $this->outputCharset, 'UTF-8');
            },
            $row,
        );
        return implode(
            mb_convert_encoding($this->separator, $this->outputCharset, 'ASCII'),
            $ret,
        );
    }
}
