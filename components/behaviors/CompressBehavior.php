<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

use const FORCE_GZIP;

class CompressBehavior extends Behavior
{
    public const PREFIX_PLAIN = '[{';
    public const PREFIX_GZIP = 'gz';

    public $attribute;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND    => 'decompress',
            ActiveRecord::EVENT_BEFORE_INSERT => 'compress',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'compress',
        ];
    }

    public function compress(): void
    {
        $attribute = $this->attribute;
        if (substr($this->owner->$attribute, 0, 2) !== static::PREFIX_PLAIN) {
            return;
        }
        $gz = $this->compressGzip($this->owner->$attribute);
        if ($gz === null) {
            // 圧縮失敗
            return;
        }
        if (strlen($gz) >= strlen($this->owner->$attribute)) {
            // 圧縮率悪い
            return;
        }
        $this->owner->$attribute = $gz;
    }

    public function decompress(): void
    {
        $attribute = $this->attribute;
        switch (substr($this->owner->$attribute, 0, 2)) {
            case static::PREFIX_PLAIN:
            default:
                return;

            case static::PREFIX_GZIP:
                $value = $this->decompressGzip($this->owner->$attribute);
                if ($value !== null) {
                    $this->owner->$attribute = $value;
                }
                return;
        }
    }

    protected function compressGzip(string $plain): ?string
    {
        $compressed = @gzencode($plain, 9, FORCE_GZIP);
        if ($compressed === false) {
            return null;
        }
        $compressed = base64_encode($compressed);
        return static::PREFIX_GZIP . $compressed;
    }

    protected function decompressGzip(string $compressed): ?string
    {
        $compressed = @base64_decode(substr($compressed, 2), true);
        if ($compressed === false) {
            return null;
        }
        $decoded = @gzdecode($compressed);
        if ($decoded === false) {
            return null;
        }
        return $decoded;
    }
}
