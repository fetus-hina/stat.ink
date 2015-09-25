IkaLogLog API
=============

POST /api/v1/battle
-------------------

`POST https://stat.ink/api/v1/battle`

バトル結果を投稿します。利用にはユーザ毎に発行されるAPIキーが必要です。
（ユーザのプロフィールページから取得できます。アプリケーションはユーザにこれを指定させてください。）

次のいずれかの形式で送信してください。

* `Content-Type: multipart/form-data` および妥当なリクエストボディ

    ファイルの送信はできません（厳密には送信する方法もありますが行わないでください）

* `Content-Type: application/x-www-form-urlencoded` および妥当なリクエストボディ

    ファイルの送信が行えます。
    ウェブブラウザが行うのと同じ形式で行ってください。

* `Content-Type: application/json` および妥当なリクエストボディ

    ファイルの送信はできません。

    パラメータに「整数」と書いてある部分を文字列として送信しても何ら問題はありません。

    booleanに見える部分も文字列として送信してください。

    ```
    POST /api/v1/battle HTTP/1.1
    Host: stat.ink
    Content-Type: application/json
    Content-Length: ***

    {"apikey":"...", "rule":"nawabari", ...}
    ```

* `Content-Type: application/x-msgpack` および妥当なリクエストボディ

    ファイルの送信が行えます。
    ファイルは該当するパラメータの値としてファイル本体をそのまま入れてください。

    電文はJSONの場合と同様、全体をMapで包んでください。

    パラメータに「整数」と書いてある部分を文字列として送信しても何ら問題はありません。

    booleanに見える部分も文字列として送信してください。

    ```
    POST /api/v1/battle HTTP/1.1
    Host: stat.ink
    Content-Type: application/x-msgpack
    Content-Length: ***

    8e a6 61 70 69 6b 65 79 a6 41 50 49 4b 45 59 a4 72 75 6c 65 a8 ...
    ```

    （このサンプルは HEX で記載していますが実際にはただのバイナリです）

    ※forkして作成したサイトの場合で MessagePack 取り扱いのための拡張が入っていない場合、
    この形式は利用できません。


### パラメータ ###

`apikey` 以外は基本的に省略可能です。
（過度な省略の場合エラーになります。現在の実装上は少なくとも `rule`, `map`, `weapon`, `result`, `rank_in_team`, `kill`, `death`
のうちどれか 1 つ以上は必須です）

省略する際は空の値を送信するか、項目自体を省略してください。

《共通》

* `apikey` : (必須) ユーザを特定するための API キーを指定します。（例: `ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopq` ）

* `rule` : ルールを次のうちいずれかの値で指定します。
    - `nawabari` : ナワバリバトル
    - `area` : ガチバトル／ガチエリア
    - `yagura` : ガチバトル／ガチヤグラ
    - `hoko` : ガチバトル／ガチホコ

* `map` : マップを次のうちいずれかの値で指定します。
    - `arowana` : アロワナモール
    - `bbass` : Bバスパーク
    - `dekaline` : デカライン高架下（ver 2）
    - `hakofugu` : ハコフグ倉庫
    - `hirame` : ヒラメが丘団地
    - `hokke` : ホッケふ頭
    - `masaba` : マサバ海峡大橋
    - `mongara` : モンガラキャンプ場
    - `mozuku` : モズク農園
    - `negitoro` : ネギトロ炭鉱
    - `shionome` : シオノメ油田
    - `tachiuo` : タチウオパーキング

* `weapon` : 自分のブキを次のいずれかの値で指定します。
    - シューター
        _ `52gal` : .52ガロン
        _ `52gal_deco` : .52ガロンデコ
        _ `96gal` : .96ガロン
        _ `96gal_deco` : .96ガロンデコ
        _ `bold` : ボールドマーカー
        _ `dualsweeper` : デュアルスイーパー
        _ `dualsweeper_custom` : デュアルスイーパーカスタム
        _ `h3reelgun` : H3リールガン
        _ `heroshooter_replica` : ヒーローシューターレプリカ
        _ `hotblaster` : ホットブラスター
        _ `hotblaster_custom` : ホットブラスターカスタム
        _ `jetsweeper` : ジェットスイーパー
        _ `jetsweeper_custom` : ジェットスイーパーカスタム
        _ `l3reelgun` : L3リールガン
        _ `l3reelgun_d` : L3リールガンD
        _ `longblaster` : ロングブラスター
        _ `momiji` : もみじシューター
        _ `nova` : ノヴァブラスター
        _ `nzap85` : N_ZAP 85
        _ `nzap89` : N_ZAP 89
        _ `octoshooter_replica` : オクタシューターレプリカ
        _ `prime` : プライムシューター
        _ `prime_collabo` : プライムシューターコラボ
        _ `promodeler_mg` : プロモデラーMG
        _ `promodeler_rg` : プロモデラーRG
        _ `rapid` : ラピッドブラスター
        _ `rapid_deco` : ラピッドブラスターデコ
        _ `sharp` : シャープマーカー
        _ `sharp_neo` : シャープマーカーネオ
        _ `sshooter` : スプラシューター
        _ `sshooter_collabo` : スプラシューターコラボ
        _ `wakaba` : わかばシューター
    _ ローラー
        _ `carbon` : カーボンローラー
        _ `dynamo` : ダイナモローラー
        _ `dynamo_tesla` : ダイナモローラーテスラ
        _ `heroroller_replica` : ヒーローローラーレプリカ
        _ `hokusai` : ホクサイ
        _ `pablo` : パブロ
        _ `pablo_hue` : パブロ・ヒュー
        _ `splatroller` : スプラローラー
        _ `splatroller_collabo` : スプラローラーコラボ
    _ チャージャー
        _ `bamboo14mk1` : 14式竹筒銃・甲
        _ `herocharger_replica` : ヒーローチャージャーレプリカ
        _ `liter3k` : リッター3K
        _ `liter3k_custom` : リッター3Kカスタム
        _ `liter3k_scope` : 3Kスコープ
        _ `splatcharger` : スプラチャージャー
        _ `splatcharger_wakame` : スプラチャージャーワカメ
        _ `splatscope` : スプラスコープ
        _ `splatscope_wakame` : スプラスコープワカメ
        _ `squiclean_a` : スクイックリンα
        _ `squiclean_b` : スクイックリンβ
    _ スロッシャー
        _ `bucketslosher` : バケットスロッシャー
        _ `hissen` : ヒッセン
    _ スピナー
        _ `barrelspinner` : バレルスピナー
        _ `splatspinner` : スプラスピナー

* `rank` : バトル開始時のウデマエを次のいずれかの値で指定します。
    - `c-`
    - `c`
    - `c+`
    - (中略)
    - `a+`
    - `s`
    - `s+`

* `level` : バトル開始時のランクを整数値で指定します。（例: `42` ）

* `result` : バトル勝敗を次のいずれかの値で指定します。
    - `win` : バトルに勝利
    - `lose` : バトルに敗北

* `rank_in_team` : バトル結果のチーム内での順位を整数値で指定します (`1`..`4`)

* `kill` : バトルのキル数（敵イカを倒した数）を整数値で指定します（例: `5` ）

* `death` : バトルのデス数（敵イカにやられた数と水死・転落死の合計）を整数値で指定します（例: `3` ）

* `image_judge` : ジャッジ君による勝敗判定画像（PNG/JPEG、3MiB以下）

* `image_result` : 個人成績の一覧画面の画像（PNG/JPEG、3MiB以下）

* `start_at` : 試合の開始時間をUNIX時間、秒単位で指定します。

* `end_at` : 試合の終了時間をUNIX時間、秒単位で指定します。

* `agent` : 送信クライアントの名称を16文字以内で指定します。（例: `IkaLog` ）

* `agent_version` : 送信クライアントのバージョンを16文字以内で指定します。（例: `1.2.3` ）

《ナワバリバトル》 : `rule` が `nawabari` のときのみ有効

* `my_point` : 自分が塗ったスコアを整数値で指定します。300Pの勝利ボーナスはこの値に含めます（画面に表示されているままを入力します。例: `1024` ）

* `my_team_final_point` : バトル終了時の自チームのポイント数（%ではなくP）を整数値で指定します（例: `820` ）

* `his_team_final_point` : バトル終了時の相手チームのポイント数（%ではなくP）を整数値で指定します

* `my_team_final_percent` : バトル終了時の自チームの塗りポイント率を0.1%単位で指定します。（例: `35.6` )

* `his_team_final_percent` : バトル終了時の相手チームの塗りポイント率を0.1%単位で指定します。

《ガチマッチ》 : `rule` がガチマッチに相当するもののときのみ有効

* `knock_out` : ノックアウトの有無を次のいずれかの値で指定します。
    - `yes` : 自チームまたは相手チームがノックアウト勝ちした
    - `no` : 両チームノックアウトしないままタイムアップした

* `my_team_count` : 自チームのカウント数

* `his_team_count` : 相手チームのカウント数

----

GET /api/v1/rule
----------------

ルールの一覧をJSON形式で返します。 `key` が他のAPIで利用するときの値です。

```js
[
    {
        "key": "nawabari",
        "mode": {
            "key": "regular",
            "name": {
                "ja_JP": "レギュラーマッチ"
            }
        },
        "name": {
            "ja_JP": "ナワバリバトル"
        }
    },
    {
        "key": "area",
        "mode": {
            "key": "gachi",
            "name": {
                "ja_JP": "ガチマッチ"
            }
        },
        "name": {
            "ja_JP": "ガチエリア"
        }
    },
    {
        "key": "yagura",
        "mode": {
            "key": "gachi",
            "name": {
                "ja_JP": "ガチマッチ"
            }
        },
        "name": {
            "ja_JP": "ガチヤグラ"
        }
    },
    {
        "key": "hoko",
        "mode": {
            "key": "gachi",
            "name": {
                "ja_JP": "ガチマッチ"
            }
        },
        "name": {
            "ja_JP": "ガチホコ"
        }
    }
]
```

----

GET /api/v1/map
----------------

マップの一覧をJSON形式で返します。 `key` が他のAPIで利用するときの値です。

```js
[
    {
        "key": "arowana",
        "name": {
            "ja_JP": "アロワナモール"
        }
    },
    {
        "key": "bbass",
        "name": {
            "ja_JP": "Bバスパーク"
        }
    },
    {
        "key": "shionome",
        "name": {
            "ja_JP": "シオノメ油田"
        }
    }
]
```

----

GET /api/v1/weapon
----------------

ブキの一覧をJSON形式で返します。 `key` が他のAPIで利用するときの値です。

```js
[
    {
        "key": "52gal",
        "name": {
            "ja_JP": ".52ガロン"
        },
        "type": {
            "key": "shooter",
            "name": {
                "ja_JP": "シューター"
            }
        }
    },
    {
        "key": "52gal_deco",
        "name": {
            "ja_JP": ".52ガロンデコ"
        },
        "type": {
            "key": "shooter",
            "name": {
                "ja_JP": "シューター"
            }
        }
    },
    {
        "key": "96gal",
        "name": {
            "ja_JP": ".96ガロン"
        },
        "type": {
            "key": "shooter",
            "name": {
                "ja_JP": "シューター"
            }
        }
    }
]
```
