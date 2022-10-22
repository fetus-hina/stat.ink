<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\battle3FilterForm;

trait PermalinkTrait
{
    /**
     * @param string|false $formName
     */
    public function toPermLink($formName = false)
    {
        $formName = \trim(\is_string($formName) ? $formName : $this->formName());

        $ret = [];
        $push = function (string $key, string $value) use ($formName, &$ret): void {
            if ($formName !== '') {
                $key = \sprintf('%s[%s]', $formName, $key);
            }

            $ret[$key] = $value;
        };

        $copyKeys = [
            'lobby',
            'rule',
            'map',
            'weapon',
            'result',
            'knockout',
        ];
        foreach ($copyKeys as $key) {
            $value = \trim((string)$this->$key);
            if ($value !== '') {
                $push($key, $value);
            }
        }

        return $ret;
    }
}
