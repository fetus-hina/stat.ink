<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\splatoon3ink;

use Exception;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\TranslateMessage;
use yii\caching\Cache;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function array_keys;
use function fwrite;
use function implode;
use function range;
use function trim;
use function vsprintf;

use const STDERR;

trait SplatfestMessages
{
    private function updateSplatfestMessages(): int
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
                        $this->updateSplatfestLangMessages(
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

    private function updateSplatfestLangMessages(string $langCode, array $dstJson, array $srcJson): bool
    {
        $categoryMap = [
            'title' => 'db/splatfest3',
        ];

        $teamCategoryMap = [
            'teamName' => 'db/splatfest3/team',
        ];

        $updated = false;
        $splatfestIds = array_keys(ArrayHelper::getValue($srcJson, 'festivals'));
        foreach ($splatfestIds as $splatfestId) {
            foreach ($categoryMap as $key => $category) {
                if (
                    $this->updateSplatfestLangMessage(
                        $langCode,
                        $category,
                        trim(
                            TypeHelper::string(
                                ArrayHelper::getValue($dstJson, ['festivals', $splatfestId, $key]),
                            ),
                        ),
                        trim(
                            TypeHelper::string(
                                ArrayHelper::getValue($srcJson, ['festivals', $splatfestId, $key]),
                            ),
                        ),
                    )
                ) {
                    $updated = true;
                }
            }

            foreach (range(0, 2) as $teamIndex) {
                foreach ($teamCategoryMap as $key => $category) {
                    if (
                        $this->updateSplatfestLangMessage(
                            $langCode,
                            $category,
                            trim(
                                TypeHelper::string(
                                    ArrayHelper::getValue(
                                        $dstJson,
                                        ['festivals', $splatfestId, 'teams', $teamIndex, $key],
                                    ),
                                ),
                            ),
                            trim(
                                TypeHelper::string(
                                    ArrayHelper::getValue(
                                        $srcJson,
                                        ['festivals', $splatfestId, 'teams', $teamIndex, $key],
                                    ),
                                ),
                            ),
                        )
                    ) {
                        $updated = true;
                    }
                }
            }
        }

        return $updated;
    }

    private function updateSplatfestLangMessage(
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
}
