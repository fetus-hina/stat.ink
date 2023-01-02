<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Curl\Curl;
use Throwable;
use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;

use function addslashes;
use function array_merge;
use function array_unique;
use function array_values;
use function dirname;
use function explode;
use function file_put_contents;
use function filter_var;
use function fwrite;
use function implode;
use function inet_pton;
use function preg_match;
use function preg_replace;
use function strcmp;
use function strpos;
use function strtolower;
use function substr;
use function trim;
use function usort;
use function vfprintf;

use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_VALIDATE_IP;
use const STDERR;

class CloudflareController extends Controller
{
    public function actionUpdateIpRanges(): int
    {
        if (!$addrList = $this->downloadIpRanges()) {
            return 1;
        }

        $lines = [
            '<?php',
            '',
            'declare(strict_types=1);',
            '',
            'return [',
        ];
        foreach ($addrList as $addr) {
            $lines[] = '    \'' . addslashes($addr) . '\',';
        }
        $lines[] = '];';
        $source = implode("\n", $lines) . "\n";

        $path = Yii::getAlias('@app/config/cloudflare/ip_ranges.php');
        FileHelper::createDirectory(dirname($path));
        file_put_contents($path, $source);

        return 0;
    }

    private function downloadIpRanges(): ?array
    {
        $list = array_merge(
            $this->downloadIpRangesFile('https://www.cloudflare.com/ips-v4', FILTER_FLAG_IPV4) ?: [],
            $this->downloadIpRangesFile('https://www.cloudflare.com/ips-v6', FILTER_FLAG_IPV6) ?: [],
        );

        $removeNetmask = fn (string $cidr): string => (($pos = strpos($cidr, '/')) === false)
                ? $cidr
                : substr($cidr, 0, $pos);

        $isIPv4 = function (string $cidr) use ($removeNetmask): bool {
            $filtered = filter_var($removeNetmask($cidr), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
            return $filtered !== false;
        };

        usort($list, function (string $a, string $b) use ($removeNetmask, $isIPv4): int {
            $aAddr = $removeNetmask($a);
            $bAddr = $removeNetmask($b);

            $aIsIPv4 = $isIPv4($aAddr);
            $bIsIPv4 = $isIPv4($bAddr);

            // IPv4 優先
            if ($aIsIPv4 !== $bIsIPv4) {
                return $aIsIPv4 ? -1 : 1;
            }

            // アドレス順に並ぶはず
            return strcmp(inet_pton($aAddr), inet_pton($bAddr))
                ?: strcmp($a, $b);
        });

        return $list ? array_values(array_unique($list)) : null;
    }

    private function downloadIpRangesFile(string $url, int $filterFlag): ?array
    {
        try {
            fwrite(STDERR, "Downloading ip ranges from {$url}\n");

            $curl = new Curl();
            $text = $curl->get($url, []);
            if ($curl->error || !$curl->responseHeaders) {
                fwrite(STDERR, "Failed to download from {$url}\n");
                return null;
            }

            $contentType = (string)($curl->responseHeaders['Content-Type'] ?? null);
            $contentType = strtolower($contentType);
            if (substr($contentType, 0, 10) !== 'text/plain') {
                fwrite(STDERR, "Downloaded data looks not a plain text\n");
                return null;
            }

            $results = [];
            $text = preg_replace('/\x0d\x0a|\x0d|\x0a/s', "\n", $text);
            foreach (explode("\n", $text) as $line) {
                $line = trim($line);
                if (!$line) {
                    continue;
                }

                if (!preg_match('#^([0-9a-f.:]+)/#i', $line, $match)) {
                    continue;
                }

                $filtered = filter_var($match[1], FILTER_VALIDATE_IP, [
                    'flags' => $filterFlag,
                ]);
                if ($filtered === false) {
                    continue;
                }

                $results[] = $line;
            }

            return $results;
        } catch (Throwable $e) {
            vfprintf(STDERR, "Catch an exception (%s): %s\n", [
                __METHOD__,
                $e->getMessage(),
            ]);
            return null;
        }
    }
}
