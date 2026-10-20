<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\components\web;

use Override;
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
    #[Override]
    public function format($response): void
    {
        $tmpfile = tmpfile();
        foreach ($response->data['rows'] as $row) {
            fwrite($tmpfile, $this->formatRow($row) . "\x0d\x0a");
        }
        fseek($tmpfile, 0, SEEK_SET);
        $response->content = null;
        $response->stream = $tmpfile;
    }

    /**
     * @param array<mixed> $row
     */
    protected function formatRow(array $row): string|false
    {
        return json_encode($row, JSON_UNESCAPED_SLASHES);
    }
}
