<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\downloadStats;

use Override;
use Yii;
use app\models\DownloadStatsForm;
use app\models\Map;
use app\models\Rule;
use app\models\Weapon;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction;

abstract class BaseAction extends ViewAction
{
    public ?DownloadStatsForm $config = null;

    #[Override]
    public function init(): void
    {
        parent::init();
        $req = Yii::$app->request;
        $config = DownloadStatsForm::validateData(
            [
                'lang' => $req->get('lang'),
                'charset' => $req->get('charset'),
                'bom' => $req->get('bom'),
                'tsv' => $req->get('tsv'),
            ],
        );
        if ($config->hasErrors()) {
            throw new BadRequestHttpException('Bad parameters');
        }
        $this->config = $config;
    }

    /**
     * @return array<string, string>
     */
    public function getWeapons(): array
    {
        $ret = [];
        foreach (Weapon::find()->all() as $a) {
            $ret[$a->key] = Yii::t('app-weapon', $a->name);
        }
        return $ret;
    }

    /**
     * @return array<string, string>
     */
    public function getRules(): array
    {
        $ret = [];
        foreach (Rule::find()->all() as $a) {
            $ret[$a->key] = Yii::t('app-rule', $a->name);
        }
        return $ret;
    }

    /**
     * @return array<string, string>
     */
    public function getMaps(): array
    {
        $ret = [];
        foreach (Map::find()->all() as $a) {
            $ret[$a->key] = Yii::t('app-map', $a->name);
        }
        return $ret;
    }
}
