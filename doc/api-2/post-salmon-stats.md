`POST /api/v2/salmon-stats`
===========================

| | |
|-|-|
|Verb|`POST`|
|URL|`https://stat.ink/api/v2/salmon-stats`|
|Return-Type|Succeed: `201 Created` (no body)<br>Failed: HTTP Error 4xx or 5xx with `application/json` or `text/html` data|
|Auth|[Needed](authorization.md)|

Post salmon run stats (card data) to stat.ink.

|パラメータ<br>Parameter|必須<br>Required|型<br>Type|値<br>Value|内容<br>Content      |
|-----------------------|----------------|----------|-----------|---------------------|
|`work_count`           |Practically     |integer   |0-         |Shifts(jobs) worked  |
|`total_golden_eggs`    |Practically     |integer   |0-         |Golden Eggs collected|
|`total_eggs`           |Practically     |integer   |0-         |Power Eggs collected |
|`total_rescued`        |Practically     |integer   |0-         |Crew members rescued |
|`total_point`          |Practically     |integer   |0-         |Total points         |
|`as_of`                |No              |integer   |UNIX time  |When this data was acquired. Current date/time will be used if omitted.|


### Example (PHP)

```php
<?php
$apiKey = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';

$data = json_encode([
    'work_count' => 388,
    'total_golden_eggs' => 4886,
    'total_eggs' => 177331,
    'total_rescued' => 780,
    'total_point' => 47034,
    'as_of' => time(),
]);

$header = [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data),
    'Authorization: Bearer ' . $apiKey,
];

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => implode("\r\n", $header),
        'content' => $data,
    ],
]);

$url = 'https://stat.ink/api/v2/salmon-stats';
$body = file_get_contents($url, false, $context);

echo implode("\n", $http_response_header) . "\n";
echo "\n";
echo $body . "\n";
```


### Example (curl)

```sh
curl \
    -H 'Content-Type: application/json' \
    -H 'Authorization: Bearer XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX' \
    -d '{"work_count":388,"total_golden_eggs":4886,"total_eggs":177331,"total_rescued":780,"total_point":47034}' \
    'https://stat.ink/api/v2/salmon-stats'
```

