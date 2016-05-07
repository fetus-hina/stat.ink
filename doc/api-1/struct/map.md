`map` 構造体
============

`map` 構造体はある特定のマップ（ステージ）を指し、次のような構造となっています。

```js
{
    "key": "arowana",
    "name": {
        "en_US": "Arowana Mall",
        "ja_JP": "アロワナモール"
    },
    "area": 2021,
    "release_at": {
        "time": 1432738800,
        "iso8601": "2015-05-27T15:00:00+00:00"
    }
}
```

* `key` : 識別する時に使用するキーです。たとえば `POST /api/v1/battle` API でマップを指定する際に使用します。

* `name` : マップの名前を [`name` 構造体](name.md) で表します。

* `area` : マップの広さの目安です。まだ計算していないときは null になります。

* `release_at` : マップの公開日時を [`time` 構造体](time.md) で表します。公開日時が確定していないときは null になります。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
