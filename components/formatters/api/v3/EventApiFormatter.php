<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\Event3;

final class EventApiFormatter
{
    public static function toJson(?Event3 $model, bool $fullTranslate): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'name' => NameApiFormatter::toJson($model->name, 'db/event3', $fullTranslate),
        ];
    }
}
