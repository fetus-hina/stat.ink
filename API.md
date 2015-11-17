stat.ink API
============

Overview: POST
--------------

投稿系の API では次の形式で送信してください。

* `Content-Type: application/x-www-form-urlencoded` および妥当なリクエストボディ

    ファイルの送信はできません（厳密には送信する方法もありますが行わないでください）

* `Content-Type: multipart/form-data` および妥当なリクエストボディ

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

Overview: GET
-------------

取得系の API、または投稿系の API の戻り値は原則として JSON 形式で返ります。

* インデント

    - インデント等の整形については特に規定しません。現状は項目ごとに改行し、4スペースでインデントされていますがある日突然変更されるかもしれません。

* array

    - その並びに特に意味がないとき、項目の出現順は特に規定しません。例えばブキ一覧を取得したとき、わかばシューターが何番目に現れるかはわかりません。

* object

    - 要素の出現順は特に規定しません。ランダムに返るかもしれません。（実装上は固定されるはずですが保障しません）

* 文字列型

    - 文字列型の表現は JSON として許容されるいずれの形式にもなり得ます。
    
* 日時表現

    - 日時型の表現は `{"time": 1443175797, "iso8601": "2015-09-25T10:09:57+00:00"}` のような表現としています。
    
    - `"time"` は UNIX 時間で秒単位です。

    - `"iso8601"` は ISO 8601 の拡張形式の文字列で表した日時です。タイムゾーンは現在 UTC で表現されますが、保障しません。（「ISO 8601 をパースできるものに通せば正しく解釈される」程度を保障します）

* 国際化された名前

    - 一部の名前は `{"en_US": "Turf War", "ja_JP": "ナワバリバトル"}` のように国際化に対応した形で返されます。

    - 現在は、英語（米国）・日本語で返されますが、今後増えるかもしれません（たぶん増えません）。


POST /api/v1/battle
-------------------

`POST https://stat.ink/api/v1/battle`

バトル結果を投稿します。利用にはユーザ毎に発行されるAPIキーが必要です。
（ユーザのプロフィールページから取得できます。アプリケーションはユーザにこれを指定させてください。）

投稿に成功した場合は `GET /api/v1/battle` と同じ結果が返ります。
失敗した場合はエラー情報が HTTP ステータス and/or JSON で返ります。

### パラメータ ###

`apikey` 以外は基本的に省略可能です。
（過度な省略の場合エラーになります。現在の実装上は少なくとも `rule`, `map`, `weapon`, `result`, `rank_in_team`, `kill`, `death`
のうちどれか 1 つ以上は必須です）

省略する際は空の値を送信するか、項目自体を省略してください。

* `apikey` : (必須) ユーザを特定するための API キーを指定します。（例: `ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopq` ）

* `test` : 通常は送信しません。API テスト時に指定すると実際の反映を行わずに動作試験が行えます。
    - `validate` : 送信内容のバリデーションのみを行います。エラーもしくは簡素なレスポンス `{"validate": true}` が返ります。
    - `dry_run` : 通常の POST 成功時とほぼ同じレスポンスが返ります。ただし、`id` はダミー、画像関係は処理されず `null` になります。

* `lobby` : ゲームモードを次のうちいずれかの値で指定します。
    - `standard` : 通常モード（いわゆる「野良」またはレギュラーフレンド合流）
    - `squad_2` : タッグマッチ（2人タッグ）
    - `squad_3` : タッグマッチ（3人タッグ）
    - `squad_4` : タッグマッチ（4人タッグ）
    - `private` : プライベートマッチ
    - `fest` : フェス(similar to `standard`)

* `rule` : ルールを次のうちいずれかの値で指定します。完全なリストはルール取得 API から取得してください。
    - `nawabari` : ナワバリバトル
    - `area` : ガチバトル／ガチエリア
    - `yagura` : ガチバトル／ガチヤグラ
    - `hoko` : ガチバトル／ガチホコ

* `map` : マップを次のうちいずれかの値で指定します。完全なリストはマップ取得 API から取得してください。
    - `arowana` : アロワナモール
    - `bbass` : Bバスパーク
    - `dekaline` : デカライン高架下（ver 2）
    - `hakofugu` : ハコフグ倉庫
    - `hirame` : ヒラメが丘団地
    - `hokke` : ホッケふ頭
    - `kinmedai` : キンメダイ美術館
    - `mahimahi` : マヒマヒリゾート&スパ
    - `masaba` : マサバ海峡大橋
    - `mongara` : モンガラキャンプ場
    - `mozuku` : モズク農園
    - `negitoro` : ネギトロ炭鉱
    - `shionome` : シオノメ油田
    - `tachiuo` : タチウオパーキング

* `weapon` : 自分のブキを次のいずれかの値で指定します。完全なリストはブキ取得 API から取得してください。
    - シューター
        - `52gal` : .52ガロン
        - `52gal_deco` : .52ガロンデコ
        - `96gal` : .96ガロン
        - `96gal_deco` : .96ガロンデコ
        - `bold` : ボールドマーカー
        - `bold_neo` : ボールドマーカーネオ
        - `dualsweeper` : デュアルスイーパー
        - `dualsweeper_custom` : デュアルスイーパーカスタム
        - `h3reelgun` : H3リールガン
        - `heroshooter_replica` : ヒーローシューターレプリカ
        - `hotblaster` : ホットブラスター
        - `hotblaster_custom` : ホットブラスターカスタム
        - `jetsweeper` : ジェットスイーパー
        - `jetsweeper_custom` : ジェットスイーパーカスタム
        - `l3reelgun` : L3リールガン
        - `l3reelgun_d` : L3リールガンD
        - `longblaster` : ロングブラスター
        - `longblaster_custom` : ロングブラスターカスタム
        - `momiji` : もみじシューター
        - `nova` : ノヴァブラスター
        - `nzap85` : N_ZAP 85
        - `nzap89` : N_ZAP 89
        - `octoshooter_replica` : オクタシューターレプリカ
        - `prime` : プライムシューター
        - `prime_collabo` : プライムシューターコラボ
        - `promodeler_mg` : プロモデラーMG
        - `promodeler_rg` : プロモデラーRG
        - `rapid` : ラピッドブラスター
        - `rapid_deco` : ラピッドブラスターデコ
        - `rapid_elite` : Rブラスターエリート
        - `sharp` : シャープマーカー
        - `sharp_neo` : シャープマーカーネオ
        - `sshooter` : スプラシューター
        - `sshooter_collabo` : スプラシューターコラボ
        - `wakaba` : わかばシューター
    - ローラー
        - `carbon` : カーボンローラー
        - `carbon_deco` : カーボンローラーデコ
        - `dynamo` : ダイナモローラー
        - `dynamo_tesla` : ダイナモローラーテスラ
        - `heroroller_replica` : ヒーローローラーレプリカ
        - `hokusai` : ホクサイ
        - `pablo` : パブロ
        - `pablo_hue` : パブロ・ヒュー
        - `splatroller` : スプラローラー
        - `splatroller_collabo` : スプラローラーコラボ
    - チャージャー
        - `bamboo14mk1` : 14式竹筒銃・甲
        - `bamboo14mk2` : 14式竹筒銃・乙
        - `herocharger_replica` : ヒーローチャージャーレプリカ
        - `liter3k` : リッター3K
        - `liter3k_custom` : リッター3Kカスタム
        - `liter3k_scope` : 3Kスコープ
        - `liter3k_scope_custom` : 3Kスコープカスタム
        - `splatcharger` : スプラチャージャー
        - `splatcharger_wakame` : スプラチャージャーワカメ
        - `splatscope` : スプラスコープ
        - `splatscope_wakame` : スプラスコープワカメ
        - `squiclean_a` : スクイックリンα
        - `squiclean_b` : スクイックリンβ
    - スロッシャー
        - `bucketslosher` : バケットスロッシャー
        - `hissen` : ヒッセン
    - スピナー
        - `barrelspinner` : バレルスピナー
        - `barrelspinner_deco` : バレルスピナーデコ
        - `splatspinner` : スプラスピナー

* `rank` : バトル開始時のウデマエを次のいずれかの値で指定します。
    - `c-`
    - `c`
    - `c+`
    - (中略)
    - `a+`
    - `s`
    - `s+`

* `rank_exp` : バトル開始時のウデマエの数値を指定します。 "S+ 99" なら `99` です。これを指定するときは `rank` も指定する必要があります。

* `rank_after` : バトル終了時のウデマエを `rank` 同様に指定します。

* `rank_exp_after` : バトル終了時のウデマエの数値を指定します。これを指定するときは `rank_after` も指定する必要があります。

* `level` : バトル開始時のランクを整数値で指定します。（例: `42` ）

* `level_after` : バトル終了時のランクを整数値で指定します。（例: `43`）

* `cash` : バトル開始時のおカネを整数値で指定します。（例: `12345`）

* `cash_after` : バトル終了時のおカネを整数値で指定します。（例: `12345`）

* `result` : バトル勝敗を次のいずれかの値で指定します。
    - `win` : バトルに勝利
    - `lose` : バトルに敗北

* `rank_in_team` : バトル結果のチーム内での順位を整数値で指定します (`1`..`4`)

* `kill` : バトルのキル数（敵イカを倒した数）を整数値で指定します（例: `5` ）

* `death` : バトルのデス数（敵イカにやられた数と水死・転落死の合計）を整数値で指定します（例: `3` ）

* `death_reasons` : 死因と死亡回数を設定します。詳細は後述します。

* `image_judge` : ジャッジ君による勝敗判定画像（PNG/JPEG、3MiB以下）

* `image_result` : 個人成績の一覧画面の画像（PNG/JPEG、3MiB以下）

* `my_point` : 自分が塗ったスコアを整数値で指定します。300Pの勝利ボーナスはこの値に含めます（画面に表示されているままを入力します。例: `1024` ）

* `my_team_final_point` : バトル終了時の自チームのポイント数（%ではなくP）を整数値で指定します（例: `820` ）

* `his_team_final_point` : バトル終了時の相手チームのポイント数（%ではなくP）を整数値で指定します

* `my_team_final_percent` : バトル終了時の自チームの塗りポイント率を0.1%単位で指定します。（例: `35.6` )

* `his_team_final_percent` : バトル終了時の相手チームの塗りポイント率を0.1%単位で指定します。

* `knock_out` : ノックアウトの有無を次のいずれかの値で指定します。
    - `yes` : 自チームまたは相手チームがノックアウト勝ちした
    - `no` : 両チームノックアウトしないままタイムアップした

* `my_team_count` : 自チームのカウント数（リザルトに表示される、ノックアウト時に100になるカウント）

* `his_team_count` : 相手チームのカウント数（リザルトに表示される、ノックアウト時に100になるカウント）

* `gender` : プレイヤーキャラの性別を次のどちらかで指定します。現状はフェスでしか使用しません（表示もされません）が、記録は行われます。
    - `boy` : ボーイ
    - `girl` : ガール

* `fest_title` : フェスの称号を指定します。このパラメータを送信する時は `gender` が必要です。（送信キー中に性別を内包する表記のものもありますがガールでもその値を送信します）
    - `fanboy` : ふつうの（お題）ボーイ/ガール
    - `friend` : まことの（お題）ボーイ/ガール
    - `defender` : スーパー（お題）ボーイ/ガール
    - `champion` : カリスマ（お題）ボーイ/ガール
    - `king` : えいえんの（お題）ボーイ/ガール

* `players` : 自分や他人の成績を指定します。詳細は後述します。

* `start_at` : 試合の開始時間をUNIX時間、秒単位で指定します。

* `end_at` : 試合の終了時間をUNIX時間、秒単位で指定します。指定しない場合は現在時刻として処理されます。

* `agent` : 送信クライアントの名称を64文字以内で指定します。（例: `IkaLog` ） `agent` を指定するときは `agent_version` も指定する必要があります。

* `agent_version` : 送信クライアントのバージョンを255文字以内で指定します。（例: `1.2.3` ） `agent_version` を指定するときは `agent` も指定する必要があります。

* `agent_custom` : 送信クライアント定義の文字列を指定することができます。valid な UTF-8 の文字列である必要がありますが、中身については関知しません。長さはこのパラメータ以外も含めて POST データ全体が 10MiB 以内に収まる必要があります。

----

### `death_reasons` ###

JSON または MessagePack で送信する場合、 object(JSON), map(MessagePack) を使用し、次のような形式になります(JSONで表記):

```js
{
    "death_reasons": {
        "wakaba": 1,
        "daioika": 2
    }
}
```

フォーム送信形式で送信する場合、次のような形式になります。

```
...&death_reasons[wakaba]=1&death_reasons[daioika]=2&...
```

それぞれの形式において、この例の場合は「わかばシューターで1回殺された」「ダイオウイカで2回殺された」ことを示します。

ブキ等のキーは次の値になります。

* メインウェポン

    - `weapon` と同じです

* サブウェポン

    - `chasebomb` : チェイスボム
    - `kyubanbomb` : キューバンボム
    - `quickbomb` : クイックボム
    - `splashbomb` : スプラッシュボム
    - `splashshield` : スプラッシュシールド
    - `sprinkler` : スプリンクラー
    - `trap` : トラップ

* スペシャル

    - `daioika` : ダイオウイカ
    - `megaphone` : メガホンレーザー
    - `supershot` : スーパーショット
    - `tornado` : トルネード

* ガチホコバトル（定義値は仮）

    - `hoko_shot` : ガチホコショット
    - `hoko_barrier` : ガチホコバリアの爆発
    - `hoko_inksplode` : ガチホコ時間切れ爆発

* 場外

    - `oob` : 場外（水死または転落死）
    - `drown` : 水死
    - `fall` : 転落死

* その他

    - `unknown` : 死因不明

----

### `players` ###

JSON または MessagePack で送信する場合、 object/map の配列を指定します。配列の要素数は 2～8 です。

```js
{
    "players": [
        {
            "team": "my",
            "is_me": "no",
            "level": 42,
            "rank": "a+",
            "weapon": "wakaba",
            // ...
        },
        { /* ... */ },
        { /* ... */ },
        { /* ... */ },
        { /* ... */ },
        { /* ... */ },
        { /* ... */ },
        { /* ... */ }
    ]
}
```

フォーム送信形式で送信する場合、次のような形式になります。最初の添え字は 0～7 です。

```
...&players[0][level]=42&players[0][rank]=a+&players[0][weapon]=wakaba&...
```

各プレーヤーについて、次のパラメータを取ります。許容される値は原則としてメインのパラメータと同じになります。

* `team` : どちら側のチームに所属しているかを指定します。このパラメータは必須です。
    - `my` : 自分が所属する側のチーム、自分もしくはチームメンバー
    - `his` : 敵チーム

* `is_me` : 自分であれば `yes` を、自分でなければ `no` を指定します。このパラメータは必須です。

* `weapon` : ブキを指定します。

* `rank` : バトル開始時のウデマエを指定します。

* `level` : バトル開始時のランクを整数値で指定します。

* `rank_in_team` : バトル結果のチーム内での順位を整数値で指定します。

* `kill` : バトルのキル数を整数値で指定します。

* `death` : バトルのデス数を整数値で指定します。

* `point` : 塗ったスコアを整数値で指定します。300Pの勝利ボーナスはこの値に含めます（画面に表示されているままを入力します。 `my_point` に対応します）


----

GET /api/v1/battle
------------------

指定したバトルについて記録されている情報が返ります。

おおむね `POST /api/v1/battle` の送信パラメータと対応したキーになりますが、

* `apikey` が含まれず `user` が含まれる

* 日時やキーを指定した箇所が展開されて含まれる

などの違いがあります。

`id` パラメータが指定されている場合は 1 件のみが object で、指定されていない場合は object のリストが返ります。

```js
{
    "id": 79,
    "url": "https:\/\/stat.ink\/u\/fetus_hina\/79",
    "user": {
        "id": 1,
        "name": "ひな",
        "screen_name": "fetus_hina",
        "join_at": {
            "time": 1443175797,
            "iso8601": "2015-09-25T10:09:57+00:00"
        },
        "profile": {
            "nnid": "fetus_hina",
            "twitter": "fetus_hina",
            "ikanakama": null,
            "eneironment": "ほげ\nふが\n\nぴよ\nfoo\nbar\nbaz"
        },
        "stat": {
            "entire": {
                "battle_count": 16,
                "wp": 100,
                "wp_24h": 100,
                "kill": 48,
                "death": 32,
                "kd_available_battle": 16
            },
            "nawabari": {
                "battle_count": 16,
                "wp": 100,
                "kill": 48,
                "death": 32
            },
            "gachi": {
                "battle_count": 0,
                "wp": null,
                "kill": 0,
                "death": 0
            }
        }
    },
    "rule": {
        "key": "nawabari",
        "mode": {
            "key": "regular",
            "name": {
                "en_US": "Regular Battle",
                "ja_JP": "レギュラーマッチ"
            }
        },
        "name": {
            "en_US": "Turf War",
            "ja_JP": "ナワバリバトル"
        }
    },
    "map": {
        "key": "hakofugu",
        "name": {
            "en_US": "Walleye Warehouse",
            "ja_JP": "ハコフグ倉庫"
        }
    },
    "weapon": null,
    "rank": null,
    "level": 22,
    "result": "win",
    "rank_in_team": 1,
    "kill": 8,
    "death": 2,
    "image_judge": null,
    "image_result": "https:\/\/stat.ink\/images\/vk\/vk45tcekjzca3lyc3zfurxmwoq.jpg",
    "agent": {
        "name": "IkaLog",
        "version": "0.01",
        "custom": null
    },
    "environment": "ほげ\nふが\n\nぴよ",
    "start_at": {
        "time": 1443381832,
        "iso8601": "2015-09-27T19:23:52+00:00"
    },
    "end_at": {
        "time": 1443382015,
        "iso8601": "2015-09-27T19:26:55+00:00"
    },
    "register_at": {
        "time": 1443382039,
        "iso8601": "2015-09-27T19:27:19+00:00"
    },
    "my_point": 1302,
    "my_team_final_point": null,
    "his_team_final_point": null,
    "my_team_final_percent": null,
    "his_team_final_percent": null
}
```

### パラメータ ###

* `id` : 指定した ID のバトル情報が返ります。

* `screen_name` : 指定したユーザのバトルが新しい順に返ります。

* `newer_than` : 指定した ID よりも新しいバトルが新しい順に返ります。（指定した ID を含みません）

* `older_than` : 指定した ID よりも古いバトルが新しい順に返ります。（指定した ID を含みません）

* `count` : 1～100 で指定した件数（かそれより少ない件数）のデータが返ります。デフォルトは 10 です。

----

GET /api/v1/rule
----------------

ルールの一覧をJSON形式で返します。 `key` が他のAPIで利用するときの値です。

ルールの出現順や構造の中の順番は特に決まっていません。（必要であれば利用者側で並び替えてください。ナワバリが真ん中に出現する可能性もあります）

```js
[
    {
        // この値が battle API に送信する値です
        "key": "nawabari",

        // ゲームモードの情報を示す構造です
        "mode": {
            // レギュラーマッチの場合はこの key が "regular" と一致します
            "key": "regular",
            "name": {
                "en_US": "Regular Battle",
                "ja_JP": "レギュラーマッチ"
            }
        },

        // ルールの名前 
        "name": {
            "en_US": "Turf War",
            "ja_JP": "ナワバリバトル"
        }
    },
    {
        "key": "area",
        "mode": {
            // ガチバトルの場合はこの key が "gachi" と一致します
            "key": "gachi",
            "name": {
                "en_US": "Ranked Battle",
                "ja_JP": "ガチマッチ"
            }
        },
        "name": {
            "en_US": "Splat Zones",
            "ja_JP": "ガチエリア"
        }
    },
    // ...
]
```

----

GET /api/v1/map
----------------

マップの一覧をJSON形式で返します。 `key` が他のAPIで利用するときの値です。

マップの出現順や構造の中の順番は特に決まっていません。（必要であれば利用者側で並び替えてください）

```js
[
    {
        // この値が battle API に送信する値です
        "key": "arowana",

        // マップの名前
        "name": {
            "en_US": "Arowana Mall",
            "ja_JP": "アロワナモール"
        }
    },
    // ...
]
```

----

GET /api/v1/weapon
----------------

ブキの一覧をJSON形式で返します。 `key` が他のAPIで利用するときの値です。

ブキの出現順やブキ構造の中の順番は特に決まっていません。（必要であれば利用者側で並び替えてください）

```js
[
    {
        // この値が battle API に送信する値です
        "key": "wakaba",

        // ブキの種別とその名前
        "type": {
            "key": "shooter",
            "name": {
                "en_US": "Shooters",
                "ja_JP": "シューター"
            }
        },

        // ブキの名前
        "name": {
            "en_US": "Splattershot Jr.",
            "ja_JP": "わかばシューター"
        },

        // サブウェポンの種類と名前
        "sub": {
            "key": "splashbomb",
            "name": {
                "en_US": "Splat Bomb",
                "ja_JP": "スプラッシュボム"
            }
        },

        // スペシャルの種類と名前
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

### パラメータ ###

複数のパラメータを指定したときは論理積(AND)になります。

それぞれの値が `key` として妥当でない場合はエラーが、論理積を求めた結果該当するものがないときは空の配列が返ります。

* `weapon` : ブキの `key` を指定します。該当するブキが一件だけ返ります。例: `/api/v1/weapon?weapon=wakaba`

* `type` : ブキ種類の `key` を指定します。該当する種類のブキがフィルタリングされて返ります。例: `/api/v1/weapon?type=charger`

* `sub` : サブウェポンの `key` を指定します。該当するサブウェポンのブキがフィルタリングされて返ります。例: `/api/v1/weapon?sub=poison`

* `special` : スペシャルの `key` を指定します。該当するスペシャルのブキがフィルタリングされて返ります。例: `/api/v1/weapon?special=daioika`


----

GET /api/v1/death-reason
----------------

死因の一覧をJSON形式で返します。 `key` が他のAPIで利用するときの値です。

死因の出現順や死因構造の中の順番は特に決まっていません。（必要であれば利用者側で並び替えてください）

```js
[
    {
        // この値が battle API に送信する値です
        "key": "hoko_shot",

        "name": {
            "en_US": "Rainmaker Shot",
            "ja_JP": "ガチホコショット"
        },

        "type": {
            "key": "hoko",
            "name": {
                "en_US": "Rainmaker",
                "ja_JP": "ガチホコ"
            }
        }
    },
    // ...
]
```

### パラメータ ###

値が `key` として妥当でない場合はエラーが返ります。

* `type` : 死因の type の `key` を指定します。該当する種類の死因がフィルタリングされて返ります。例: `/api/v1/death-reason?type=hoko`
