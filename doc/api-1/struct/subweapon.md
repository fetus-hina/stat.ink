`subweapon` 構造体
==================

`subweapon` 構造体はある特定のサブウェポンを指し、次のような構造となっています。

```js
{
    "key": "splashbomb",
    "name": {
        "en_US": "Splat Bomb",
        "ja_JP": "スプラッシュボム"
    }
}
```

* `key` : 識別する時に使用するキーです。たとえば `GET /api/v1/weapon` API で絞り込む際に指定します。

* `name` : 名前を [`name` 構造体](name.md) で表します。
