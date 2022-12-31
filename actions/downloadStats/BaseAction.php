<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\downloadStats;

use Yii;
use app\models\Charset;
use app\models\Language;
use app\models\Map;
use app\models\Rule;
use app\models\Weapon;
use yii\base\DynamicModel;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction;

abstract class BaseAction extends ViewAction
{
    public $config;

    public function init()
    {
        parent::init();
        $req = Yii::$app->request;
        $config = DynamicModel::validateData(
            [
                'lang'      => $req->get('lang'),
                'charset'   => $req->get('charset'),
                'bom'       => $req->get('bom'),
                'tsv'       => $req->get('tsv'),
            ],
            [
                [['lang', 'charset'], 'string'],
                [['bom', 'tsv'], 'boolean'],
                [['lang'], 'exist', 'skipOnError' => true,
                    'targetClass' => Language::class,
                    'targetAttribute' => 'lang'],
                [['charset'], 'exist', 'skipOnError' => true,
                    'targetClass' => Charset::class,
                    'targetAttribute' => ['charset' => 'php_name']],
            ],
        );
        if ($config->hasErrors()) {
            throw new BadRequestHttpException('Bad parameters');
        }
        $this->config = $config;
    }

    public function getWeapons()
    {
        $ret = [];
        foreach (Weapon::find()->all() as $a) {
            $ret[$a->key] = Yii::t('app-weapon', $a->name);
        }
        return $ret;
    }

    public function getRules()
    {
        $ret = [];
        foreach (Rule::find()->all() as $a) {
            $ret[$a->key] = Yii::t('app-rule', $a->name);
        }
        return $ret;
    }

    public function getMaps()
    {
        $ret = [];
        foreach (Map::find()->all() as $a) {
            $ret[$a->key] = Yii::t('app-map', $a->name);
        }
        return $ret;
    }
}
