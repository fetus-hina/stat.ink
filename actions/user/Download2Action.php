<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use app\models\Battle2;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\ViewAction as BaseAction;

class Download2Action extends BaseAction
{
    private $user;

    public function init()
    {
        parent::init();
        $this->user = Yii::$app->user->getIdentity();
    }

    public function run()
    {
        $type = Yii::$app->request->get('type');
        if (is_scalar($type)) {
            switch ((string)$type) {
                case 'ikalog-csv':
                    return $this->runIkaLogCsv();

                case 'csv':
                    return $this->runCsv();

                case 'full-csv':
                    return $this->runFullCsv();
            }
        }
        throw new BadRequestHttpException(
            Yii::t(
                'yii',
                'Invalid data received for parameter "{param}".',
                [ 'param' => 'type' ]
            )
        );
    }

    private function runIkaLogCsv()
    {
        $charsets = [
            'de-DE' => [ 'ISO-8859-1', 'Latin-1' ],
            'es-ES' => [ 'ISO-8859-1', 'Latin-1' ],
            'es-MX' => [ 'ISO-8859-1', 'Latin-1' ],
            'fr-CA' => [ 'ISO-8859-1', 'Latin-1' ],
            'fr-FR' => [ 'ISO-8859-1', 'Latin-1' ],
            'it-IT' => [ 'ISO-8859-1', 'Latin-1' ],
            'ja-JP' => [ 'CP932', 'Shift_JIS' ],
            'ko-KR' => [ 'CP949', 'ks_c_5601' ],
            'nl-NL' => [ 'ISO-8859-1', 'Latin-1' ],
            'ru-RU' => [ 'CP1251', 'Windows-1251' ],
            'zh-CN' => [ 'CP936', 'gb2312' ],
            'zh-TW' => [ 'BIG-5', 'big5' ],
        ];
        $charset = $charsets[Yii::$app->language] ?? [ 'UTF-8', 'UTF-8' ];

        $resp = Yii::$app->response;
        $resp->setDownloadHeaders(
            'statink-ikalog-2.csv',
            'text/cvs; charset=' . $charset[1],
            false,
            null
        );
        $resp->format = 'csv';
        $battles = $this->user->getBattle2s()
            ->with(['rule', 'map'])
            ->orderBy('{{battle2}}.[[id]] ASC');
        $generator =  function () use ($battles) {
            foreach ($battles->each() as $battle) {
                yield $battle->toIkaLogCsv();
            }
        };

        return [
            'inputCharset' => 'UTF-8',
            'outputCharset' => $charset[0],
            'rows' => $generator(),
        ];
    }

    private function runCsv()
    {
        $resp = Yii::$app->response;
        $resp->setDownloadHeaders('statink-2.csv', 'text/cvs; charset=UTF-8', false, null);
        $resp->format = 'csv';
        $battles = $this->user->getBattle2s()
            ->with(['lobby', 'mode', 'rule', 'map', 'weapon', 'rank', 'rankAfter'])
            ->orderBy('{{battle2}}.[[id]] ASC');
        $generator =  function () use ($battles) {
            yield [
                Yii::t('app', 'Date Time'),
                Yii::t('app', 'Date Time'),
                Yii::t('app', 'Lobby'),
                Yii::t('app', 'Mode'),
                Yii::t('app', 'Stage'),
                Yii::t('app', 'Weapon'),
                Yii::t('app', 'Result'),
                Yii::t('app', 'Knockout'),
                Yii::t('app', 'Team ID'),
                Yii::t('app', 'Rank'),
                Yii::t('app', 'Rank (After)'),
                Yii::t('app', 'Power Level'),
                Yii::t('app', 'League Power'),
                Yii::t('app', 'Level'),
                Yii::t('app', 'Kills'),
                Yii::t('app', 'Deaths'),
                Yii::t('app', 'k+a'),
                Yii::t('app', 'Specials'),
                Yii::t('app', 'Inked'),
            ];
            foreach ($battles->each() as $battle) {
                yield $battle->toCsvArray();
            }
        };

        return [
            'inputCharset' => 'UTF-8',
            'outputCharset' => 'UTF-8',
            'appendBOM' => true,
            'rows' => $generator(),
        ];
    }

    private function runFullCsv()
    {
        $resp = Yii::$app->response;
        $resp->setDownloadHeaders('statink-2.csv', 'text/cvs; charset=UTF-8', false, null);
        $resp->format = 'csv';
        $battles = $this->user->getBattle2s()
            ->with(ArrayHelper::toFlatten([
                'battlePlayersPure',
                'gender',
                'lobby',
                'map',
                'mode',
                'rank',
                'rankAfter',
                'rule',
                'species',
                'version',
                'weapon',
                array_map(
                    function (string $gear): array {
                        return [
                            $gear,
                            "{$gear}.gear",
                            "{$gear}.primaryAbility",
                            "{$gear}.secondaries",
                            "{$gear}.secondaries.ability",
                        ];
                    },
                    ['headgear', 'clothing', 'shoes']
                ),
            ]))
            ->orderBy(['{{battle2}}.[[id]]' => SORT_ASC]);
        $generator =  function () use ($battles) {
            $schema = Battle2::fullCsvArraySchema();
            $schema[0] = '# ' . $schema[0];
            yield $schema;
            unset($schema);

            foreach ($battles->each(250) as $battle) {
                yield $battle->toFullCsvArray();
            }
        };

        return [
            'inputCharset' => 'UTF-8',
            'outputCharset' => 'UTF-8',
            'appendBOM' => true,
            'rows' => $generator(),
        ];
    }
}
