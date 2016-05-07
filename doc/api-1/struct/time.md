`time` 構造体
=============

`time` 構造体は次の構造になっていて、特定の日時を示します。内部に含む各値は同一の日時を指すことが保証されます。

```js
{
    "time": 1443175797,
    "iso8601": "2015-09-25T10:09:57+00:00"
}
```

* `time` : UNIX 時間、秒単位で表した日時です。

* `iso8601` : ISO 8601 拡張形式で表した日時です。タイムゾーンは現在 UTC で表現されますが、保証しません。（「ISO 8601 をパースできるものに通せば正しく解釈される」程度を保証します）

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
