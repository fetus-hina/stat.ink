<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use yii\base\InvalidConfigException;
use yii\console\controllers\MigrateController as BaseController;

use function array_keys;
use function array_merge;
use function implode;
use function sprintf;

class MigrateController extends BaseController
{
    public $templateFile = '@app/views/migration/default_migration.php';
    public $template;

    public function init()
    {
        parent::init();
        $this->setUpGenerateTemplateFiles();
    }

    protected function setUpGenerateTemplateFiles(): void
    {
        $prefix = '@app/views/migration';
        $files = [
            'gear2' => "{$prefix}/gear2_migration.php",
            'map2' => "{$prefix}/map2_migration.php",
            'salmon-weapon2' => "{$prefix}/salmon_weapon2_migration.php",
            'stage2' => "{$prefix}/map2_migration.php",
            'version2' => "{$prefix}/version2_migration.php",
            'version3' => "{$prefix}/version3_migration.php",
            'weapon2' => "{$prefix}/weapon2_migration.php",
            'weapon3' => "{$prefix}/weapon3_migration.php",
        ];

        foreach ($files as $key => $file) {
            if (!isset($this->generatorTemplateFiles[$key])) {
                $this->generatorTemplateFiles[$key] = $file;
            }
        }
    }

    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            $actionID === 'create' ? ['template'] : [],
        );
    }

    protected function generateMigrationSourceCode($params)
    {
        if ($this->template !== 'default') {
            if (!isset($this->generatorTemplateFiles[$this->template])) {
                throw new InvalidConfigException(sprintf(
                    'You must specify --template={%s,default}',
                    implode(',', array_keys($this->generatorTemplateFiles)),
                ));
            }

            $this->templateFile = $this->generatorTemplateFiles[$this->template];
        }

        return parent::generateMigrationSourceCode($params);
    }
}
