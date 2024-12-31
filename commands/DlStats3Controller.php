<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Yii;
use app\commands\dlStats3\BattleTrait;
use app\commands\dlStats3\SalmonTrait;
use yii\console\Controller;
use yii\console\ExitCode;

final class DlStats3Controller extends Controller
{
    use BattleTrait;
    use SalmonTrait;

    private const TIMEZONE = 'Etc/UTC';
    public const BASE_BATTLE_RESULTS_CSV = '@app/runtime/dl-stats/splatoon-3/battle-results-csv';
    public const BASE_SALMON_RESULTS_CSV = '@app/runtime/dl-stats/splatoon-3/salmon-results-csv';

    public $defaultAction = 'create';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Yii::$app->timeZone = self::TIMEZONE;
    }

    public function actionCreate(): int
    {
        $result = $this->actionCreateBattleResultsCsv();
        if ($result !== ExitCode::OK) {
            return $result;
        }

        return $this->actionCreateSalmonResultsCsv();
    }
}
