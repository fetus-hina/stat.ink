<?php
/**
 * @copyright Copyright (C) 2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\commands;

use Yii;
use yii\base\InvalidConfigException;
use yii\console\controllers\MigrateController as BaseController;

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
            'stage2' => "{$prefix}/map2_migration.php",
            'version2' => "{$prefix}/version2_migration.php",
            'weapon2' => "{$prefix}/weapon2_migration.php",
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
            $actionID === 'create' ? ['template'] : []
        );
    }

    protected function generateMigrationSourceCode($params)
    {
        switch ($this->template) {
            case 'gear2':
            case 'map2':
            case 'stage2':
            case 'version2':
            case 'weapon2':
                $this->templateFile = $this->generatorTemplateFiles[$this->template];
                break;

            case 'default':
                break;

            default:
                throw new InvalidConfigException(
                    'You must specify --template={gear2,map2,(stage2),version2,weapon2,default}'
                );
        }

        return parent::generateMigrationSourceCode($params);
    }
}
