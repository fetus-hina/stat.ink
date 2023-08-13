<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Curl\Curl;
use Exception;
use Yii;
use app\commands\splatoon3Ink\UpdateEventSchedule;
use app\commands\splatoon3Ink\UpdateSalmonSchedule;
use app\commands\splatoon3Ink\UpdateSchedule;
use app\commands\splatoon3Ink\UpdateSplatfestSchedule;
use app\components\helpers\TypeHelper;
use app\components\helpers\splatoon3ink\ScheduleParser;
use app\models\TranslateMessage;
use app\models\TranslateSourceMessage;
use yii\caching\Cache;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use function array_keys;
use function fwrite;
use function implode;
use function trim;
use function vsprintf;

use const STDERR;

final class Splatoon3InkController extends Controller
{
    use UpdateEventSchedule;
    use UpdateSalmonSchedule;
    use UpdateSchedule;
    use UpdateSplatfestSchedule;

    public $defaultAction = 'update';

    public function actionUpdate(): int
    {
        $schedules = ScheduleParser::parseAll(
            $this->queryJson('https://splatoon3.ink/data/schedules.json'),
        );

        $status = 0;
        $status |= $this->updateSchedule($schedules);
        $status |= $this->updateEventSchedule($schedules);
        $status |= $this->updateSalmonSchedule($schedules);
        $status |= $this->updateEventMessages();
        $status |= $this->actionUpdateSplatfestSchedule();
        return $status === 0 ? ExitCode::OK : ExitCode::UNSPECIFIED_ERROR;
    }

    public function actionUpdateSchedule(): int
    {
        return $this->updateSchedule(
            ScheduleParser::parseAll(
                $this->queryJson('https://splatoon3.ink/data/schedules.json'),
            ),
        );
    }

    public function actionEventSchedule(): int
    {
        return $this->updateEventSchedule(
            ScheduleParser::parseAll(
                $this->queryJson('https://splatoon3.ink/data/schedules.json'),
            ),
        );
    }

    public function actionUpdateSalmonSchedule(): int
    {
        return $this->updateSalmonSchedule(
            ScheduleParser::parseAll(
                $this->queryJson('https://splatoon3.ink/data/schedules.json'),
            ),
        );
    }

    public function actionUpdateEventMessages(): int
    {
        return $this->updateEventMessages();
    }

    public function actionUpdateSplatfestSchedule(): int
    {
        return $this->updateSplafestSchedule(
            ScheduleParser::parseFestivals(
                $this->queryJson('https://splatoon3.ink/data/festivals.json'),
            ),
        );
    }

    private function queryJson(string $url, array $data = []): array
    {
        return Yii::$app->cache->getOrSet(
            [__METHOD__, $url, $data],
            function () use ($url, $data): array {
                echo "Querying {$url} ...\n";
                $curl = new Curl();
                $curl->setUserAgent(
                    vsprintf('%s/%s (+%s)', [
                        'stat.ink',
                        Yii::$app->version,
                        'https://github.com/fetus-hina/stat.ink',
                    ]),
                );
                $curl->get($url, $data);
                if ($curl->error) {
                    throw new Exception(
                        "Request failed: url={$url}, code={$curl->errorCode}, msg={$curl->errorMessage}",
                    );
                }
                return Json::decode($curl->rawResponse, true);
            },
            duration: 1800, // 30 min
        );
    }

    private function updateEventMessages(): int
    {
        $langs = [
            'de' => 'https://splatoon3.ink/data/locale/de-DE.json',
            'en-GB' => 'https://splatoon3.ink/data/locale/en-GB.json',
            'es' => 'https://splatoon3.ink/data/locale/es-ES.json',
            'es-MX' => 'https://splatoon3.ink/data/locale/es-MX.json',
            'fr-CA' => 'https://splatoon3.ink/data/locale/fr-CA.json',
            'fr' => 'https://splatoon3.ink/data/locale/fr-FR.json',
            'it' => 'https://splatoon3.ink/data/locale/it-IT.json',
            'ja' => 'https://splatoon3.ink/data/locale/ja-JP.json',
            'ko' => 'https://splatoon3.ink/data/locale/ko-KR.json',
            'nl' => 'https://splatoon3.ink/data/locale/nl-NL.json',
            'ru' => 'https://splatoon3.ink/data/locale/ru-RU.json',
            'zh-CN' => 'https://splatoon3.ink/data/locale/zh-CN.json',
            'zh-TW' => 'https://splatoon3.ink/data/locale/zh-TW.json',
        ];

        $jsonEnUs = $this->queryJson('https://splatoon3.ink/data/locale/en-US.json');
        $updated = Yii::$app->db->transaction(
            function (Connection $db) use ($langs, $jsonEnUs): bool {
                $updated = false;
                foreach ($langs as $langCode => $jsonUrl) {
                    if (
                        $this->updateEventLangMessages(
                            $langCode,
                            $this->queryJson($jsonUrl),
                            $jsonEnUs,
                        )
                    ) {
                        $updated = true;
                    }
                }
                return $updated;
            },
            Transaction::READ_COMMITTED,
        );

        if ($updated) {
            fwrite(STDERR, "Updated message(s), flushing cache\n");
            $cache = Yii::$app->get('messageCache');
            if ($cache && $cache instanceof Cache) {
                $cache->flush();
                fwrite(STDERR, "Flushed\n");
            } else {
                fwrite(STDERR, "Skip. Not configured\n");
            }

            fwrite(STDERR, "VACUUMing message tables\n");
            $tables = [
                'translate_source_message',
                'translate_message',
            ];
            foreach ($tables as $table) {
                fwrite(STDERR, "  {$table} ...\n");
                Yii::$app->db
                    ->createCommand(
                        vsprintf('VACUUM ( ANALYZE ) %s', [
                            Yii::$app->db->quoteTableName($table),
                        ]),
                    )
                    ->execute();
            }
            fwrite(STDERR, "Done.\n");
        }

        return ExitCode::OK;
    }

    private function updateEventLangMessages(string $langCode, array $dstJson, array $srcJson): bool
    {
        $categoryMap = [
            'name' => 'db/event3',
            'desc' => 'db/event3/description',
            'regulation' => 'db/event3/regulation',
        ];

        $updated = false;
        $eventIds = array_keys(ArrayHelper::getValue($srcJson, 'events'));
        foreach ($eventIds as $eventId) {
            foreach ($categoryMap as $key => $category) {
                if (
                    $this->updateEventLangMessage(
                        $langCode,
                        $category,
                        trim(
                            TypeHelper::string(
                                ArrayHelper::getValue($dstJson, ['events', $eventId, $key]),
                            ),
                        ),
                        trim(
                            TypeHelper::string(
                                ArrayHelper::getValue($srcJson, ['events', $eventId, $key]),
                            ),
                        ),
                    )
                ) {
                    $updated = true;
                }
            }
        }

        return $updated;
    }

    private function updateEventLangMessage(
        string $langCode,
        string $category,
        string $dstText,
        string $srcText,
    ): bool {
        if ($srcText === '' || $dstText === '') {
            return false;
        }

        $srcMessage = $this->getOrCreateSourceMessage($category, $srcText);
        $model = TranslateMessage::find()
            ->andWhere([
                'id' => $srcMessage->id,
                'language' => $langCode,
            ])
            ->limit(1)
            ->one();
        if ($model && $model->translation === $dstText) {
            return false;
        }

        $model ??= Yii::createObject([
            'class' => TranslateMessage::class,
            'id' => $srcMessage->id,
            'language' => $langCode,
        ]);
        $model->translation = $dstText;
        if ($model->save()) {
            return true;
        }

        throw new Exception('Failed to update ' . implode(' / ', [
            $langCode,
            $category,
            $srcText,
            $dstText,
        ]));
    }

    private function getOrCreateSourceMessage(string $category, string $message): TranslateSourceMessage
    {
        $model = TranslateSourceMessage::find()
            ->andWhere([
                'category' => $category,
                'message' => $message,
            ])
            ->limit(1)
            ->one();
        if ($model) {
            return $model;
        }

        $model = Yii::createObject([
            'class' => TranslateSourceMessage::class,
            'category' => $category,
            'message' => $message,
        ]);
        if (!$model->save()) {
            throw new Exception('Failed to save TranslateSourceMessage for "' . $message . '"');
        }

        return $model;
    }
}
