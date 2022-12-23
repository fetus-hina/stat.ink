<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Yii;
use app\models\Season3;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\web\Request;

use const FILTER_VALIDATE_INT;
use const SORT_DESC;

final class Season3Helper
{
    public const DEFAULT_SEASON_PARAM_NAME = 'season';

    public static function getUrlTargetSeason(
        string $paramName = self::DEFAULT_SEASON_PARAM_NAME,
    ): ?Season3 {
        $request = Yii::$app->get('request');
        if (!$request instanceof Request) {
            throw new InvalidCallException();
        }

        $id = \filter_var($request->get($paramName), FILTER_VALIDATE_INT);
        return \is_int($id)
            ? Season3::find()->andWhere(['id' => $id])->limit(1)->one()
            : null;
    }

    public static function getCurrentSeason(string $offset = 'P1D'): ?Season3
    {
        $db = Yii::$app->db;
        if (!$db instanceof Connection) {
            throw new InvalidConfigException();
        }

        $timestamp = self::timestamp($offset);
        return Season3::find()
            ->andWhere(
                \vsprintf('%s @> %s::timestamptz', [
                    $db->quoteColumnName('term'),
                    $db->quoteValue($timestamp->format(DateTimeInterface::ATOM)),
                ]),
            )
            ->limit(1)
            ->one($db);
    }

    /**
     * @return Season3[]
     */
    public static function getSeasons(bool $xSupported = false): array
    {
        $db = Yii::$app->db;
        if (!$db instanceof Connection) {
            throw new InvalidConfigException();
        }

        return Season3::find()
            ->andWhere(['<=', '[[start_at]]', self::timestamp()->format(DateTimeInterface::ATOM)])
            ->andWhere(
                $xSupported
                    ? ['>=', 'start_at', '2022-10-01T00:00:00+00:00']
                    : '1 = 1',
            )
            ->orderBy(['start_at' => SORT_DESC])
            ->all($db);
    }

    private static function timestamp(?string $offset = null): DateTimeInterface
    {
        $timestamp = (new DateTimeImmutable())->setTimestamp($_SERVER['REQUEST_TIME']);
        return $offset !== null
            ? $timestamp->sub(new DateInterval($offset))
            : $timestamp;
    }
}
