<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\api\internal;

use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\Map2;
use app\models\Mode2;
use app\models\UserWeapon2;
use app\models\Weapon2;
use app\models\WeaponCategory2;
use app\models\WeaponType2;
use statink\yii2\stages\spl2\Spl2Stage;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

use function max;
use function microtime;
use function sprintf;
use function strcasecmp;
use function uasort;
use function vsprintf;

use const SORT_ASC;
use const SORT_DESC;

final class CurrentData2Action extends Action
{
    public function init()
    {
        parent::init();
        Yii::$app->response->format = YII_ENV_DEV ? 'json' : 'compact-json';
        if (Yii::$app->user->isGuest) {
            throw new BadRequestHttpException();
        }
    }

    public function run()
    {
        return [
            'current' => $this->getCurrentInfo(),
            'rules' => $this->getRules(),
            'maps' => $this->getMaps(),
            'weapons' => $this->getWeapons(),
            'favWeapons' => $this->getFavoriteWeapons(),
        ];
    }

    public function getCurrentInfo()
    {
        $now = microtime(true);
        $period = BattleHelper::calcPeriod2((int)$now);
        $range = BattleHelper::periodToRange2($period);
        $fest = false;
        return [
            'period' => [
                'id' => $period,
                'next' => max($range[1] - $now, 0), // in sec
            ],
            'fest' => $fest,
            'regular' => false,
            'gachi' => false,
        ];
    }

    public function getRules()
    {
        $ret = [];
        foreach (Mode2::find()->with('rules')->asArray()->all() as $mode) {
            $ret[$mode['key']] = (function (array $rules): array {
                $tmp = [];
                foreach ($rules as $rule) {
                    $tmp[$rule['key']] = [
                        'name' => Yii::t('app-rule2', $rule['name']),
                    ];
                }
                uasort($tmp, fn ($a, $b) => strcasecmp($a['name'], $b['name']));
                return $tmp;
            })($mode['rules']);
        }
        return $ret;
    }

    public function getMaps()
    {
        return ArrayHelper::asort(
            ArrayHelper::map(
                Map2::find()->all(),
                'key',
                fn (Map2 $map): array => [
                    'name' => Yii::t('app-map2', $map->name),
                    'shortName' => Yii::t('app-map2', $map->short_name),
                    'image' => Url::to(
                        Spl2Stage::url('daytime', $map->key),
                        true,
                    ),
                ],
            ),
            fn (array $a, array $b): int => strcasecmp(
                (string)ArrayHelper::getValue($a, 'name'),
                (string)ArrayHelper::getValue($b, 'name'),
            ),
        );
    }

    public function getWeapons()
    {
        $categories = WeaponCategory2::find()
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $ret = [];
        foreach ($categories as $category) {
            $types = WeaponType2::find()
                ->andWhere(['category_id' => $category->id])
                ->orderBy(['id' => SORT_ASC])
                ->all();
            foreach ($types as $type) {
                $weapons = Weapon2::find()
                    ->andWhere(['type_id' => $type->id])
                    ->all();
                if ($weapons) {
                    $ret[] = [
                        'name' => $category->name === $type->name
                            ? Yii::t('app-weapon2', $type->name)
                            : sprintf(
                                '%s » %s',
                                Yii::t('app-weapon2', $category->name),
                                Yii::t('app-weapon2', $type->name),
                            ),
                        'list' => ArrayHelper::asort(
                            ArrayHelper::map(
                                $weapons,
                                'key',
                                fn (Weapon2 $weapon): array => [
                                    'name' => Yii::t('app-weapon2', $weapon->name),
                                ],
                            ),
                            fn (array $a, array $b): int => strcasecmp(
                                (string)ArrayHelper::getValue($a, 'name'),
                                (string)ArrayHelper::getValue($b, 'name'),
                            ),
                        ),
                    ];
                }
            }
        }
        return $ret;
    }

    public function getFavoriteWeapons()
    {
        if (!$user = Yii::$app->user->identity) {
            return [];
        }

        $fmt = Yii::$app->formatter;
        return ArrayHelper::getColumn(
            UserWeapon2::find()
                ->with(['weapon'])
                ->andWhere(['and',
                    ['user_id' => $user->id],
                    ['>', 'battles', 0],
                ])
                ->orderBy(['battles' => SORT_DESC])
                ->limit(10)
                ->all(),
            fn (UserWeapon2 $model): array => [
                'key' => (string)ArrayHelper::getValue($model, 'weapon.key'),
                'name' => vsprintf('%s (%s)', [
                    Yii::t('app-weapon2', (string)ArrayHelper::getValue($model, 'weapon.name')),
                    $fmt->asInteger((int)$model->battles),
                ]),
            ],
        );
    }
}
