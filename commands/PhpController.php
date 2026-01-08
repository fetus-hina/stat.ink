<?php

/**
 * @copyright Copyright (C) 2024-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use DOMElement;
use DOMNodeList;
use DOMXPath;
use Masterminds\HTML5;
use Symfony\Component\CssSelector\CssSelectorConverter;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\CurlTransport;

use function array_filter;
use function array_map;
use function array_reverse;
use function array_shift;
use function array_values;
use function file_get_contents;
use function fwrite;
use function implode;
use function intval;
use function is_array;
use function is_string;
use function iterator_to_array;
use function number_format;
use function preg_match;
use function preg_quote;
use function sprintf;
use function strlen;
use function strtolower;
use function substr;
use function trim;
use function usort;
use function version_compare;
use function vfprintf;
use function vsprintf;

use const STDERR;

final class PhpController extends Controller
{
    public function actionLatestVersion(): int
    {
        if (!$series = $this->detectRequiredPhpSeries()) {
            fwrite(STDERR, "Could not determinate requirement.\n");
            return ExitCode::DATAERR;
        }
        fwrite(STDERR, "[info] Required PHP {$series} (from composer.json)\n");

        if (!$changelogHtml = $this->fetchChangelog($series)) {
            return ExitCode::UNAVAILABLE;
        }

        if (!$version = $this->getLatestVersion($changelogHtml, $series)) {
            vfprintf(STDERR, "Could not determinate latest version of series PHP %s\n", [
                $series,
            ]);
            return ExitCode::UNAVAILABLE;
        }

        echo $version . "\n";

        return ExitCode::OK;
    }

    private function fetchChangelog(string $series): ?string
    {
        fwrite(STDERR, "[info] Retriving CHANGELOG for PHP {$series}\n");

        $client = Yii::createObject([
            'class' => HttpClient::class,
            'transport' => CurlTransport::class,
        ]);

        $response = $client
            ->createRequest()
            ->setMethod('GET')
            ->setUrl(vsprintf('https://www.php.net/ChangeLog-%d.php', [
                intval($series, 10),
            ]))
            ->send();

        if (!$response->isOk) {
            fwrite(STDERR, "Could not fetch CHANGELOG from www.php.net.\n");
            return null;
        }

        if (strtolower(substr((string)$response->content, 0, 14)) !== '<!doctype html') {
            fwrite(STDERR, "Downloaded CHANGELOG looks broken.\n");
            return null;
        }

        $content = $response->content;
        vfprintf(STDERR, "[info] Downloaded %s bytes.\n", [
            number_format(strlen($content)),
        ]);

        return $content;
    }

    private function getLatestVersion(string $html, string $series): ?string
    {
        if (!$versions = $this->detectReleasedVersionsFromChangeLog($html)) {
            return null;
        }

        $versions = array_values(
            array_filter($versions, fn (string $version): bool => (bool)preg_match(
                sprintf('/^%s/', preg_quote($series . '.', '/')),
                $version,
            )),
        );
        if (!$versions) {
            return null;
        }

        vfprintf(STDERR, "[info] Available versions: %s\n", [
            implode(', ', array_reverse($versions)),
        ]);

        return array_shift($versions);
    }

    /**
     * @return string[]
     */
    private function detectReleasedVersionsFromChangeLog(string $html): array
    {
        $document = (new HTML5(['disable_html_ns' => true]))->loadHtml($html);
        $xpath = new DOMXPath($document);
        $nodes = $xpath->query(
            (new CssSelectorConverter())->toXPath('section.version[id] > h3'),
        );
        $versions = array_filter(
            array_map(
                fn (DOMElement $element): ?string => preg_match(
                    '/^Version\s+([0-9.]+)$/',
                    trim($element->textContent),
                    $match,
                )
                    ? $match[1]
                    : null,
                self::extractDOMElements($nodes),
            ),
            fn ($v) => $v !== null,
        );
        usort($versions, fn ($a, $b) => version_compare($b, $a));
        return array_values($versions);
    }

    private function detectRequiredPhpSeries(): ?string
    {
        $json = Json::decode(
            (string)file_get_contents((string)Yii::getAlias('@app/composer.json')),
        );

        if (!is_array($json)) {
            return null;
        }

        if (!is_string($php = ArrayHelper::getValue($json, 'require.php'))) {
            return null;
        }

        if (!preg_match('/\b(\d\.\d+)\b/', $php, $match)) {
            return null;
        }

        return (string)$match[1];
    }

    /**
     * @return DOMElement[]
     */
    private static function extractDOMElements(DOMNodeList|false $nodeList): array
    {
        return array_values(
            $nodeList === false
                ? []
                : array_filter(
                    iterator_to_array($nodeList),
                    fn (mixed $node): bool => $node instanceof DOMElement,
                ),
        );
    }
}
