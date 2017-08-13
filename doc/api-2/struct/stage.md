`stage`構造体
=============

`stage`構造体はある特定のステージ（マップ）を指し、次のような構造となっています。

```js
{
    "key": "battera",
    "splatnet": 0,
    "name": {
        "ja_JP": "バッテラストリート",
        "en_US": "The Reef",
        "en_GB": "The Reef",
        "es_ES": "Barrio Congrio",
        "es_MX": "Barrio Congrio"
    },
    "short_name": {
        "ja_JP": "バッテラ",
        "en_US": "Reef",
        "en_GB": "Reef",
        "es_ES": "Barrio",
        "es_MX": "Barrio"
    },
    "area": 2450,
    "release_at": {
        "time": 1490382000,
        "iso8601": "2017-03-24T19:00:00+00:00"
    }
}
```

- `key` : 識別する時に使用するキーです。たとえば[`POST /api/v2/battle`](../post-battle.md) APIでステージを指定する際に使用します。
- `splatnet` : イカリング2で使用されていると思われるキーです。
- `name` : マップの名前を[`name`構造体](name.md)で表します。
- `name_short` : マップの名前（短縮名）を[`name`構造体](name.md)で表します。
- `area` : マップの広さの目安です。まだ計算していないときは`null`になります。
- `release_at` : マップの公開日時を[`time`構造体](time.md)で表します。公開日時が確定していないときは`null`になります。

v1との差異
----------

- この構造体は`map`という名前でした。
- `name_short`が追加されています。

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
