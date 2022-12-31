<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\openapi\doc\V1 as V1Generator;
use app\components\openapi\doc\V2 as V2Generator;
use app\models\Country;
use app\models\Timezone;
use app\models\TimezoneGroup;
use yii\console\Controller;
use yii\helpers\FileHelper;
use yii\helpers\Html;

class ApidocController extends Controller
{
    private const FLAGICON_CDN = 'https://cdnjs.cloudflare.com/ajax/libs';
    private const FLAGICON_URL = '{cdn}/flag-icons/{version}/flags/4x3/{cc}.svg';

    public $defaultAction = 'create';
    public $layout = false;

    public $languages = [
        'en' => 'en-US',
        'ja' => 'ja-JP',
        'zh-hans' => 'zh-CN',
        'zh-hant' => 'zh-TW',
    ];

    public function actionCreate(): int
    {
        $successful = true;
        foreach ($this->languages as $langCodeShort => $langCodeLong) {
            if (!$this->create($langCodeShort, $langCodeLong)) {
                $successful = false;
            }
        }
        return $successful ? 0 : 1;
    }

    private function create(string $langCodeShort, string $langCodeLong): bool
    {
        Yii::$app->language = $langCodeLong;

        $successful = true;
        $successful = $this->createV2($langCodeShort) && $successful;
        $successful = $this->createV1($langCodeShort) && $successful;
        return $successful;
    }

    private function createV2(string $langCode): bool
    {
        $generator = Yii::createObject([
            'class' => V2Generator::class,
        ]);

        $this->stderr(__METHOD__ . "(): {$langCode}: Creating JSON...\n");
        $jsonPath = vsprintf('%s/runtime/apidoc/%s.json', [
            Yii::getAlias('@app'),
            vsprintf('%d-%04x%04x', [
                time(),
                random_int(0, 0xffff),
                random_int(0, 0xffff),
            ]),
        ]);
        FileHelper::createDirectory(dirname($jsonPath));
        $json = $generator->render();
        if (@file_put_contents($jsonPath, $json) === false) {
            $this->stderr(__METHOD__ . "(): {$langCode}: Failed to create a json file!\n");
            return false;
        }

        $this->stderr(__METHOD__ . "(): {$langCode}: Checking syntax...\n");
        $cmdline = vsprintf('/usr/bin/env %s %s lint %s', [
            escapeshellarg('npx'),
            escapeshellarg('speccy'),
            escapeshellarg($jsonPath),
        ]);
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            $this->stderr(__METHOD__ . "(): {$langCode}: Lint failed (status={$status}).\n");
            $this->stderr("json: {$jsonPath}\n");
            $this->stderr(implode("\n", $lines) . "\n");
            return false;
        }

        $this->stderr(__METHOD__ . "(): {$langCode}: Creating HTML...\n");
        $outPath = vsprintf('%s/web/apidoc/v2.%s.html', [
            Yii::getAlias('@app'),
            $langCode,
        ]);
        $cmdline = vsprintf('/usr/bin/env npx %s bundle -o %s --title %s %s', [
            escapeshellarg('redoc-cli'),
            escapeshellarg($outPath),
            escapeshellarg(Yii::t('app-apidoc2', 'stat.ink API for Splatoon 2')),
            escapeshellarg($jsonPath),
        ]);
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            $this->stderr(__METHOD__ . "(): {$langCode}: Create failed (status={$status}).\n");
            $this->stderr("json: {$jsonPath}\n");
            $this->stderr(implode("\n", $lines) . "\n");
            return false;
        }
        $this->stderr(__METHOD__ . "(): OK\n");

        return true;
    }

    private function createV1(string $langCode): bool
    {
        $generator = Yii::createObject([
            'class' => V1Generator::class,
        ]);

        $this->stderr(__METHOD__ . "(): {$langCode}: Creating JSON...\n");
        $jsonPath = vsprintf('%s/runtime/apidoc/%s.json', [
            Yii::getAlias('@app'),
            vsprintf('%d-%04x%04x', [
                time(),
                random_int(0, 0xffff),
                random_int(0, 0xffff),
            ]),
        ]);
        FileHelper::createDirectory(dirname($jsonPath));
        $json = $generator->render();
        if (@file_put_contents($jsonPath, $json) === false) {
            $this->stderr(__METHOD__ . "(): {$langCode}: Failed to create a json file!\n");
            return false;
        }

        $this->stderr(__METHOD__ . "(): {$langCode}: Checking syntax...\n");
        $cmdline = vsprintf('/usr/bin/env %s %s lint %s', [
            escapeshellarg('npx'),
            escapeshellarg('speccy'),
            escapeshellarg($jsonPath),
        ]);
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            $this->stderr(__METHOD__ . "(): {$langCode}: Lint failed (status={$status}).\n");
            $this->stderr("json: {$jsonPath}\n");
            $this->stderr(implode("\n", $lines) . "\n");
            return false;
        }

        $this->stderr(__METHOD__ . "(): {$langCode}: Creating HTML...\n");
        $outPath = vsprintf('%s/web/apidoc/v1.%s.html', [
            Yii::getAlias('@app'),
            $langCode,
        ]);
        $cmdline = vsprintf('/usr/bin/env %s %s bundle -o %s --title %s %s', [
            escapeshellarg('npx'),
            escapeshellarg('redoc-cli'),
            escapeshellarg($outPath),
            escapeshellarg(Yii::t('app-apidoc1', 'stat.ink API for Splatoon 1')),
            escapeshellarg($jsonPath),
        ]);
        @exec($cmdline, $lines, $status);
        if ($status !== 0) {
            $this->stderr(__METHOD__ . "(): {$langCode}: Create failed (status={$status}).\n");
            $this->stderr("json: {$jsonPath}\n");
            $this->stderr(implode("\n", $lines) . "\n");
            return false;
        }
        $this->stderr(__METHOD__ . "(): OK\n");

        return true;
    }

    public function actionPutTimezoneTable(): int
    {
        $groups = TimezoneGroup::find()
            ->with(['timezones', 'timezones.countries'])
            ->andWhere(['<>', 'name', 'Others'])
            ->all();
        echo "<!-- ./yii apidoc/put-timezone-table -->\n";
        echo Html::tag(
            'table',
            implode('', [
                Html::tag(
                    'thead',
                    implode('', [
                        Html::tag('tr', implode('', [
                            Html::tag('th', '', ['rowspan' => 2]),
                            Html::tag('th', Html::encode('stat.ink\'s display name'), [
                                'rowspan' => 2,
                            ]),
                            Html::tag('th', Html::encode('IANA Identifier'), ['rowspan' => 2]),
                            Html::tag('th', Html::encode('UTC Offset'), ['colspan' => 2]),
                        ])),
                        Html::tag('tr', implode('', [
                            Html::tag('th', Html::encode('January')),
                            Html::tag('th', Html::encode('July')),
                        ])),
                    ]),
                ),
                Html::tag(
                    'tbody',
                    implode('', array_map(
                        [$this, 'renderTimezoneTableGroup'],
                        $groups,
                    )),
                ),
            ]),
            ['role' => 'table'],
        ) . "\n";

        return 0;
    }

    private function renderTimezoneTableGroup(TimezoneGroup $group): string
    {
        if (count($group->timezones) < 1) {
            return '';
        }

        return implode('', [
            Html::tag('tr', Html::tag(
                'td',
                Html::tag('b', implode(' / ', [
                    Html::encode($group->name),
                    Html::encode(Yii::t('app-tz', $group->name, [], 'ja-JP')),
                ])),
                ['colspan' => 5],
            )),
            implode('', array_map(
                function (Timezone $tz): string {
                    $tz_ = new DateTimeZone($tz->identifier);
                    $offsetJan = (new DateTimeImmutable('2020-01-08T00:00:00', $tz_))
                        ->format('P');
                    $offsetJul = (new DateTimeImmutable('2020-07-08T00:00:00', $tz_))
                        ->format('P');

                    return Html::tag('tr', implode('', [
                        Html::tag(
                            'td',
                            implode('', array_map(
                                fn (Country $country): string => vsprintf('<img src="%s" height="%d" width="%d" alt="%s">', [
                                        $this->getFlagIconUrl($country->key),
                                        16,
                                        round(16 * 4 / 3),
                                        implode('', array_map(
                                            fn (int $codepoint): string => sprintf('&#x%x;', $codepoint),
                                            $country->regionalIndicatorSymbols,
                                        )),
                                    ]),
                                $tz->countries,
                            )),
                            ['align' => 'center'],
                        ),
                        Html::tag('td', implode('<br>', [
                            Html::encode($tz->name),
                            Html::encode(Yii::t('app-tz', $tz->name, [], 'ja-JP')),
                        ])),
                        Html::tag('td', Html::tag('code', Html::encode($tz->identifier))),
                        $offsetJan === $offsetJul
                            ? Html::tag('td', Html::encode($offsetJan), [
                                'colspan' => 2,
                                'align' => 'center',
                            ])
                            : implode('', [
                                Html::tag('td', Html::encode($offsetJan), [
                                    'align' => 'center',
                                ]),
                                Html::tag('td', HTml::encode($offsetJul), [
                                    'align' => 'center',
                                ]),
                            ]),
                    ]));
                },
                $group->timezones,
            )),
        ]);
    }

    private function getFlagIconUrl(string $cc): string
    {
        static $flagIconCssVersion = false;
        if ($flagIconCssVersion === false) {
            if (!$flagIconCssVersion = $this->getFlagIconCssVersion()) {
                throw new \Exception('Could not detect the version of flag-icons');
            }
        }

        return preg_replace_callback(
            '/\{\w+\}/',
            function (array $match) use ($flagIconCssVersion, $cc): string {
                switch ($match[0]) {
                    case '{cdn}':
                        return static::FLAGICON_CDN;

                    case '{version}':
                        return rawurlencode($flagIconCssVersion);

                    case '{cc}':
                        return rawurlencode($cc);

                    default:
                        return $match[0];
                }
            },
            static::FLAGICON_URL,
        );
    }

    private function getFlagIconCssVersion(): ?string
    {
        $cwd = getcwd();
        try {
            chdir(dirname(__DIR__));
            @exec('npm view flag-icons version', $lines, $status);
            if ($status !== 0) {
                return null;
            }

            if (!preg_match('/^v?(\d+\.\d+\.\d+)/is', (string)($lines[0] ?? null), $match)) {
                return null;
            }

            return $match[1];
        } finally {
            @chdir($cwd);
        }
    }
}
