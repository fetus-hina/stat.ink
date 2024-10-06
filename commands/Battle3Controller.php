<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use app\components\helpers\Battle3Helper;
use app\models\Battle3;
use yii\console\Controller;
use yii\helpers\Json;

use function filter_var;
use function fwrite;
use function is_int;
use function usort;
use function vsprintf;

use const FILTER_VALIDATE_INT;
use const JSON_NUMERIC_CHECK;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const STDERR;

final class Battle3Controller extends Controller
{
    public function actionCalcGearPowers(string $id): int
    {
        $isUuid = !is_int(filter_var($id, FILTER_VALIDATE_INT));
        $battle = Battle3::find()
            ->with(Battle3Helper::getRelationsForApiResponse(false))
            ->andWhere(['and',
                ['is_deleted' => false],
                $isUuid
                    ? ['uuid' => $id]
                    : ['id' => filter_var($id, FILTER_VALIDATE_INT)],
            ])
            ->limit(1)
            ->one();
        if (!$battle) {
            fwrite(STDERR, "Battle not found\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $players = $battle->battlePlayer3s ?: $battle->battleTricolorPlayer3s;
        if (!$players) {
            fwrite(STDERR, "No players\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }

        usort($players, fn ($a, $b) => $a->id <=> $b->id);

        $results = [];
        foreach ($players as $player) {
            $results[vsprintf('%s #%s (%d)', [
                $player->name,
                $player->number,
                $player->id,
            ])] = Battle3Helper::calcGPs($player);
        }

        echo Json::encode(
            $results,
            JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        ) . "\n";

        return 0;
    }
}
