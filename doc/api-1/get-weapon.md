`GET /api/v1/weapon`
====================

URL: `https://stat.ink/api/v1/weapon`
Return-Type: `application/json`

ブキの一覧を JSON 形式、 [`weapon` 構造体](struct/weapon.md) の配列で返します。
各ブキの `key` が他の API で利用するときの値です。

出現順に規定はありません。（利用者側で適切に並び替えてください）

クエリパラメータ
----------------

ブキを絞り込んで返します。複数のパラメータを指定した時は論理積(AND)になります。

それぞれの値が `key` として妥当でない場合はエラーが、論理積を求めた結果該当するものがないときは空の配列が返ります。

* `weapon` : ブキの `key` を指定します。該当するブキが一件だけ返ります。例: `/api/v1/weapon?weapon=wakaba`

* `type` : ブキ種類の `key` を指定します。該当する種類のブキがフィルタリングされて返ります。例: `/api/v1/weapon?type=charger`

* `sub` : サブウェポンの `key` を指定します。該当するサブウェポンのブキがフィルタリングされて返ります。例: `/api/v1/weapon?sub=poison`

* `special` : スペシャルの `key` を指定します。該当するスペシャルのブキがフィルタリングされて返ります。例: `/api/v1/weapon?special=daioika`

出力例
------

```js
[
    {
        "key": "wakaba",
        "type": {
            "key": "shooter",
            "name": {
                "en_US": "Shooters",
                "ja_JP": "シューター"
            }
        },
        "name": {
            "en_US": "Splattershot Jr.",
            "ja_JP": "わかばシューター"
        },
        "sub": {
            "key": "splashbomb",
            "name": {
                "en_US": "Splat Bomb",
                "ja_JP": "スプラッシュボム"
            }
        },
        "special": {
            "key": "barrier",
            "name": {
                "en_US": "Bubbler",
                "ja_JP": "バリア"
            }
        }
    },
    // ...
]
```

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
