<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Throwable;
use Yii;
use app\components\helpers\DataUri;
use app\components\helpers\db\Now;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\helpers\Json;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\CurlTransport;

use function assert;
use function bin2hex;
use function count;
use function fprintf;
use function fwrite;
use function is_array;
use function is_string;
use function preg_match;
use function sprintf;
use function vfprintf;

use const STDERR;

final class PasskeyAaguidController extends Controller
{
    private const SOURCE_URL =
        'https://raw.githubusercontent.com/passkeydeveloper/passkey-authenticator-aaguids/refs/heads/main/combined_aaguid.json';

    private const AAGUID_REGEX = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';

    public $defaultAction = 'update';

    public function actionUpdate(): int
    {
        $entries = $this->fetchEntries();
        if ($entries === null) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (count($entries) === 0) {
            fwrite(STDERR, "Source JSON has no entries; skipping update.\n");
            return ExitCode::OK;
        }

        $db = Yii::$app->db;
        assert($db instanceof Connection);

        $now = new Now();

        $db->transaction(function (Connection $db) use ($entries, $now): void {
            foreach ($entries as $aaguid => $info) {
                $this->processEntry($db, (string)$aaguid, $info, $now);
            }
        });

        return ExitCode::OK;
    }

    private function fetchEntries(): ?array
    {
        try {
            vfprintf(STDERR, "Downloading %s\n", [self::SOURCE_URL]);

            $client = Yii::createObject([
                'class' => HttpClient::class,
                'transport' => CurlTransport::class,
            ]);
            $response = $client->createRequest()
                ->setOptions([
                    'timeout' => 30,
                    'userAgent' => sprintf(
                        'stat.ink/%s (+https://github.com/fetus-hina/stat.ink)',
                        Yii::$app->version,
                    ),
                    'maxRedirects' => 5,
                ])
                ->setMethod('get')
                ->setUrl(self::SOURCE_URL)
                ->send();
            if (!$response->isOk) {
                fprintf(STDERR, "Fetch failed, HTTP %d\n", $response->statusCode);
                return null;
            }

            $decoded = Json::decode($response->content, true);
            return is_array($decoded) ? $decoded : null;
        } catch (Throwable $e) {
            fprintf(STDERR, "Error: %s\n", $e->getMessage());
            return null;
        }
    }

    private function processEntry(Connection $db, string $aaguid, mixed $info, Now $now): void
    {
        if (preg_match(self::AAGUID_REGEX, $aaguid) !== 1) {
            fwrite(STDERR, "Skipping invalid AAGUID: {$aaguid}\n");
            return;
        }

        if (!is_array($info)) {
            return;
        }

        $name = isset($info['name']) && is_string($info['name']) && $info['name'] !== ''
            ? $info['name']
            : null;
        if ($name === null) {
            return;
        }

        $this->upsertName($db, $aaguid, $name, $now);

        foreach (['light', 'dark'] as $theme) {
            $value = $info["icon_{$theme}"] ?? null;
            if (!is_string($value) || $value === '') {
                continue;
            }

            $parsed = DataUri::parse($value);
            if ($parsed === null) {
                fwrite(STDERR, "Skipping invalid icon_{$theme} for {$aaguid}\n");
                continue;
            }

            [$mimeType, $binary] = $parsed;
            $this->upsertIcon($db, $aaguid, $theme, $mimeType, $binary, $now);
        }
    }

    private function upsertName(Connection $db, string $aaguid, string $name, Now $now): void
    {
        $sql =
            'INSERT INTO {{%passkey_aaguid}} ' .
            '([[aaguid]], [[name]], [[created_at]], [[updated_at]]) ' .
            "VALUES (:aaguid, :name, {$now}, {$now}) " .
            'ON CONFLICT ([[aaguid]]) DO UPDATE SET ' .
            '[[name]] = {{excluded}}.[[name]], ' .
            '[[updated_at]] = {{excluded}}.[[updated_at]] ' .
            'WHERE {{%passkey_aaguid}}.[[name]] IS DISTINCT FROM {{excluded}}.[[name]]';

        $db->createCommand($sql, [
            ':aaguid' => $aaguid,
            ':name' => $name,
        ])->execute();
    }

    private function upsertIcon(
        Connection $db,
        string $aaguid,
        string $theme,
        string $mimeType,
        string $binary,
        Now $now,
    ): void {
        $sql =
            'INSERT INTO {{%passkey_aaguid_icon}} ' .
            '([[aaguid]], [[theme]], [[mime_type]], [[data]], [[created_at]], [[updated_at]]) ' .
            "VALUES (:aaguid, :theme, :mime, decode(:data_hex, 'hex'), {$now}, {$now}) " .
            'ON CONFLICT ([[aaguid]], [[theme]]) DO UPDATE SET ' .
            '[[mime_type]] = {{excluded}}.[[mime_type]], ' .
            '[[data]] = {{excluded}}.[[data]], ' .
            '[[updated_at]] = {{excluded}}.[[updated_at]] ' .
            'WHERE {{%passkey_aaguid_icon}}.[[mime_type]] IS DISTINCT FROM {{excluded}}.[[mime_type]] ' .
            'OR {{%passkey_aaguid_icon}}.[[data]] IS DISTINCT FROM {{excluded}}.[[data]]';

        $db->createCommand($sql, [
            ':aaguid' => $aaguid,
            ':theme' => $theme,
            ':mime' => $mimeType,
            ':data_hex' => bin2hex($binary),
        ])->execute();
    }
}
