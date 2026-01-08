<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\web;

use yii\base\Component;
use yii\web\ResponseFormatterInterface;

use function fseek;
use function fwrite;
use function json_encode;
use function tmpfile;

use const JSON_UNESCAPED_SLASHES;
use const SEEK_SET;

class IkalogJsonResponseFormatter extends Component implements ResponseFormatterInterface
{
    public function format($response)
    {
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
        return json_encode($row, JSON_UNESCAPED_SLASHES);
    }
}
