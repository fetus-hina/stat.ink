#!/usr/bin/env php
<?php
$doc = (function ($html) {
    $doc = new DOMDocument();
    $doc->preserveWhiteSpace = false;
    if (!@$doc->loadHtml(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'))) {
        exit(1);
    }
    return $doc;
})(request());

$xpath = new DOMXpath($doc);
foreach ($xpath->query('//head/link') as $link) {
    $rel = strtolower($link->getAttribute('rel'));
    if ($rel === 'shortcut icon' || $rel === 'icon') {
        $href = $link->getAttribute('href');
        if (strpos($href, '//') === false) {
            echo 'http://ikazok.net/' . ltrim($href, '/') . "\n";
            exit(0);
        } elseif (substr($href, 0, 2) === '//') {
            echo 'http' . $href . "\n";
            exit(0);
        } else {
            echo $href . "\n";
            exit(0);
        }
    }
}
exit(1);

function request()
{
    $curl = curl_init('http://ikazok.net/');
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
