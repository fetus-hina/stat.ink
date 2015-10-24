`map` 構造体
============

`map` 構造体はある特定のマップ（ステージ）を指し、次のような構造となっています。

```js
{
    "key": "arowana",
    "name": {
        "en_US": "Arowana Mall",
        "ja_JP": "アロワナモール"
    }
}
```

* `key` : 識別する時に使用するキーです。たとえば `POST /api/v1/battle` API でマップを指定する際に使用します。

* `name` : マップの名前を [`name` 構造体](name.md) で表します。
