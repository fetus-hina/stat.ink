<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\commands\i18n;

use Yii;
use app\models\Gear2;

trait GearNameTrait
{
    public function actionJapaneseGear2(): int
    {
        $path = implode(DIRECTORY_SEPARATOR, [
            dirname(dirname(__DIR__)),
            'messages',
            'ja',
            'gear2.php',
        ]);

        $this->stderr('[JapaneseGear2] Updating ' . $path . "\n");
        $data = require($path);

        // remove empty data
        $data = array_filter(
            $data,
            fn (string $value, string $key): bool => $value !== '',
            ARRAY_FILTER_USE_BOTH,
        );

        $changed = false;
        foreach (Gear2::find()->asArray()->all() as $gear) {
            $name = $gear['name'];
            if (!isset($data[$name])) {
                $data[$name] = '';
                $changed = true;
            }
        }

        if (!$changed) {
            $this->stderr('[JapaneseGear2] SKIP' . "\n");
            return 0;
        }

        uksort($data, fn (string $a, string $b): int => strcmp($a . "'", $b . "'"));

        $esc = fn (string $text): string => str_replace(["\\", "'"], ["\\\\", "\\'"], $text);

        $file = [];
        $file[] = '<?php';
        $file[] = '';
        $file[] = '/**';
        $file[] = ' * @copyright Copyright (C) 2015-' . gmdate('Y', time() + 9 * 3600) . ' AIZAWA Hina';
        $file[] = ' * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT';
        foreach ($this->getContributors($path) as $contributor) {
            $file[] = ' * @author ' . $contributor;
        }
        $file[] = ' */';
        $file[] = '';
        $file[] = 'declare(strict_types=1);';
        $file[] = '';
        $file[] = 'return [';
        foreach ($data as $k => $v) {
            $file[] = vsprintf("    '%s' => '%s',", [
                $esc($k),
                $esc($v),
            ]);
        }
        $file[] = '];';

        file_put_contents(
            $path,
            implode("\n", $file) . "\n",
        );

        return 0;
    }
}
