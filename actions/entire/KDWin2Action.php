<?php

/**
 * @copyright Copyright (C) 2017-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire;

use Yii;
use app\models\KDWin2FilterForm;
use app\models\SplatoonVersion2;
use app\models\SplatoonVersionGroup2;
use app\models\StatWeapon2KdWinRate;
use yii\web\ServerErrorHttpException;
use yii\web\ViewAction;

use function array_filter;
use function min;
use function trim;

class KDWin2Action extends ViewAction
{
    public const KD_LIMIT = 16;

    public function run()
    {
        $filter = Yii::createObject(KDWin2FilterForm::class);
        $filter->load($_GET);
        if ($filter->validate()) {
            if ($filter->version == '') {
                $latest = $this->getLatestVersionGroup();
                $filter->version = $latest->tag;
                $this->controller->redirect(
                    ['entire/kd-win2',
                        'filter' => array_filter(
                            $filter->attributes,
                            fn (?string $value): bool => trim((string)$value) !== '',
                        ),
                    ],
                );
                return;
            }
        }

        return $this->controller->render('kd-win2', [
            'data' => $this->makeData($filter),
            'filter' => $filter,
        ]);
    }

    private function makeData(KDWin2FilterForm $filter): array
    {
        $table = StatWeapon2KdWinRate::tableName();
        $query = StatWeapon2KdWinRate::find()
            ->asArray()
            ->innerJoinWith('rule', false)
            ->applyFilter($filter)
            ->select([
                'rule' => 'MAX({{rule2}}.[[key]])',
                'kill' => "{{{$table}}}.[[kill]]",
                'death' => "{{{$table}}}.[[death]]",
                'battle' => "SUM({{{$table}}}.[[battles]])",
                'win' => "SUM({{{$table}}}.[[wins]])",
            ])
            ->groupBy([
                "{{{$table}}}.[[rule_id]]",
                "{{{$table}}}.[[kill]]",
                "{{{$table}}}.[[death]]",
            ]);

        $result = [];
        foreach ($query->all() as $row) {
            $rule = $row['rule'];
            $k = min(static::KD_LIMIT, (int)$row['kill']);
            $d = min(static::KD_LIMIT, (int)$row['death']);

            if (!isset($result[$rule])) {
                $result[$rule] = [];
            }

            if (!isset($result[$rule][$k])) {
                $result[$rule][$k] = [];
            }

            if (!isset($result[$rule][$k][$d])) {
                $result[$rule][$k][$d] = [
                    'battle' => 0,
                    'win' => 0,
                ];
            }
            $result[$rule][$k][$d]['battle'] += (int)$row['battle'];
            $result[$rule][$k][$d]['win'] += (int)$row['win'];
        }

        return $result;
    }

    public function getLatestVersionGroup(): SplatoonVersionGroup2
    {
        $version = SplatoonVersion2::findCurrentVersion();
        if (!$version) {
            throw new ServerErrorHttpException(
                'Could not determinate current game version',
            );
        }

        return $version->group;
    }
}
