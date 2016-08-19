#!/usr/bin/env php
<?php
$id = $argv[1];
$url = sprintf('https://play.google.com/store/apps/details?%s', http_build_query(['id' => $id], '', '&'));

$doc = (function ($html) {
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    if (!@$doc->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'))) {
        exit(1);
    }
    return $doc;
})(request($url));

$xpath = new DOMXpath($doc);
foreach ($xpath->query("//img[contains(concat(' ', @class, ' '), ' cover-image ')]") as $img) {
    $src = $img->getAttribute('src');
    if (strpos($src, '//') === false) {
        printf(
            "%s/%s\n",
            preg_replace('!^(https?://[^/]+)/.*$!', "$1", $url),
            ltrim($src, '/')
        );
        exit(0);
    } elseif (substr($src, 0, 2) === '//') {
        printf(
            "%s:%s",
            preg_replace('!^(https?)://.+$!', "$1", $url),
            $src
        );
        exit(0);
    } else {
        echo $src . "\n";
        exit(0);
    }
}
exit(1);

function request(string $url)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
    curl_setopt($curl, CURLOPT_USERAGENT, 'statink (+https://stat.ink/)');
    $ret = curl_exec($curl);
    if ($ret === false) {
        fprintf(STDERR, "%d: %s\n", curl_errno($curl), curl_error($curl));
        exit(1);
    }
    curl_close($curl);
    return $ret;
}
