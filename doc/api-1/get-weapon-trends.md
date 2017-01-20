`GET /api/v1/weapon-trends`
===========================

URL: `https://stat.ink/api/v1/weapon-trends` (※必須パラメータあり(後述))<br>
Return-Type: `application/json`

ルール・マップ別のブキの使用状況について JSON 形式で返します。

出現順は、stat.ink の把握している利用数順です。JSON 中の `rank` の順と一致します。

クエリパラメータ
----------------

* [必須] `rule` : ルールの `key` を指定します。
    - `nawabari` : ナワバリバトル
    - `area` : ガチエリア
    - `yagura` : ガチヤグラ
    - `hoko` : ガチホコ

* [必須] `map` : ステージの `key` を指定します。
    - 有効な値は `GET /api/v1/map` から取得してください。

リクエスト例: `https://stat.ink/api/v1/weapon-trends?rule=nawabari&map=arowana` （ナワバリバトル・アロワナモール）


出力構造
--------

各ブキを表す構造は次の通りです。レスポンスはこの構造の配列になります。

- `rank` : [number(integer)] 利用数の多い順に 1 から順に設定されます。
- `use_pct` : [number] 利用率をパーセント単位で出力します。全ブキの合計がちょうど 100 になるとは限りません。
- `weapon` : [[weapon](struct/weapon.md)] ブキの詳細を表します。
    - `key`, `type`, ...

検出している最近の利用数が 0 の場合、当該ブキは出力に現れません。
利用者は、目的のブキを探すときに存在しなければ 0.00% として補完する必要があります。

出力例
------

```js
[
    {
        "rank": 1,
        "use_pct": 4.48,
        "weapon": {
            "key": "liter3k_scope",
            "type": {
                "key": "charger",
                "name": {
                    "ja_JP": "チャージャー",
                    "en_US": "Chargers",
                    "en_GB": "Chargers",
                    "es_ES": "Cargatintas",
                    "es_MX": "Cargatintas"
                }
            },
            "name": {
                "ja_JP": "3Kスコープ",
                "en_US": "E-liter 3K Scope",
                "en_GB": "E-Litre 3K Scope",
                "es_ES": "Telentintador 3K",
                "es_MX": "Telentintador 3K"
            },
            "sub": {
                "key": "quickbomb",
                "name": {
                    "ja_JP": "クイックボム",
                    "en_US": "Burst Bomb",
                    "en_GB": "Burst Bomb",
                    "es_ES": "Bomba rápida",
                    "es_MX": "Globo entintado"
                }
            },
            "special": {
                "key": "supersensor",
                "name": {
                    "ja_JP": "スーパーセンサー",
                    "en_US": "Echolocator",
                    "en_GB": "Echolocator",
                    "es_ES": "Superdetector",
                    "es_MX": "Ecolocalizador"
                }
            }
        }
    },
    // ...
]
```

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
