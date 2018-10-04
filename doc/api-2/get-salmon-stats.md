`GET /api/v2/salmon-stats`
==========================

| | |
|-|-|
|Verb|`GET`|
|URL|`https://stat.ink/api/v2/salmon-stats`|
|Return-Type|`200 OK`, `404 Not Found`, `400 Bad Request` or `401 Unauthorized`|
|Auth|[Needed](authorization.md)|

Get salmon run stats (card data). [See POST API](post-salmon-stats.md).

### Response example

```
HTTP/1.1 200 OK
content-type: application/json; charset=UTF-8

{
    "work_count": 388,
    "total_golden_eggs": 4886,
    "total_eggs": 177331,
    "total_rescued": 780,
    "total_point": 47034,
    "as_of": {
        "time": 1538682944,
        "iso8601": "2018-10-04T19:55:44+00:00"
    },
    "registered_at": {
        "time": 1538682945,
        "iso8601": "2018-10-04T19:55:45+00:00"
    }
}
```

### Parameter

|パラメータ<br>Parameter|必須<br>Required|型<br>Type|値<br>Value|内容<br>Content      |
|-----------------------|----------------|----------|-----------|---------------------|
|`id`                   |No              |integer   |1-         |Permanent ID         |

`id` を省略した場合、最新の情報を取得します。<br>
If omitted the `id`, you will get a latest data.

`id` の値は POST API の Location ヘッダで得られます。<br>
The value of `id` is obtained in the Location header of the POST API.

他人の `id` を指定した場合は 404 エラーになります。<br>
If specified other player's `id` value, you will get the 404 error.
