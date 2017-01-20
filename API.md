stat.ink API
============

Overview: POST
--------------

投稿系の API では次の形式で送信してください。

* `Content-Type`
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

* `Content-Encoding`:
    * `Content-Encoding` 省略または `Content-Encoding: identity`

        通常はこれになります。「普通のリクエスト」で特に何もありません。

    * `Content-Encoding: gzip`
        
        リクエストボディ全体を gzip で圧縮して送信できます。

        JSON 形式で POST する際に圧縮効果が期待できますが、MessagePack で画像付きの場合などは圧縮できない画像が大部分を占めるため期待したほどの圧縮効果は得られない可能性があります。 

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

    - 現在は、次の言語で返されます。
        - 英語（北米） [en-US]
        - 英語（欧州・豪州） [en-GB]
        - 日本語 [ja-JP]


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

* `apikey` : (必須) ユーザを特定するための API キーを指定します。[構成文字・文字数等の情報](doc/api-1/struct/apikey.md)

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
    - `anchovy` : アンチョビットゲームズ
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
    - `shottsuru` : ショッツル鉱山
    - `tachiuo` : タチウオパーキング

* `weapon` : 自分のブキを `wakaba` `momiji` 等のあらかじめ定義された値で指定します。指定する値は[一覧ページ](https://stat.ink/api-info/weapon)かブキ取得APIから取得してください。

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

* `max_kill_combo` : バトル中、短時間（画面下部のたおした！表示が消えるまでの間くらいが目安）に連続でたおした数の最大値を整数値で指定します。

* `max_kill_streak` : バトル中、「リスポーンからデスまでの間（要するに、死ぬまで）」を１区間として、たおした数の最大値を整数値で指定します。

* `death_reasons` : 死因と死亡回数を設定します。詳細は後述します。

* `image_judge` : ジャッジ君による勝敗判定画像（PNG/JPEG、3MiB以下）

* `image_result` : 個人成績の一覧画面の画像（PNG/JPEG、3MiB以下）

* `image_gear` : ギア・ギアパワー画面の画像（PNG/JPEG、3MiB以下）

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
    - `fiend` : まことの（お題）ボーイ/ガール
        - [互換性維持のために `friend` も受け付けます](https://github.com/fetus-hina/stat.ink/issues/44)
    - `defender` : スーパー（お題）ボーイ/ガール
    - `champion` : カリスマ（お題）ボーイ/ガール
    - `king` : えいえんの（お題）ボーイ/ガール

* `fest_exp` : フェスの称号に対応する経験値を0～99の整数値で指定します。

* `fest_title_after` : バトル後のフェスの称号を指定します。このパラメータを送信する時は `gender` が必要です。

* `fest_exp_after` : バトル後のフェスの称号に対応する経験値を0～99の整数値で指定します。

* `fest_power` : バトル前の自分のフェスパワーを整数値で指定します。(β)

* `my_team_power` : バトル前の自分のチームの合計フェスパワーを整数値で指定します。(β)

* `his_team_power` : バトル前の相手のチームの合計フェスパワーを整数値で指定します。(β)

* `players` : 自分や他人の成績を指定します。詳細は後述します。

* `events` : バトル中に発生したイベントを指定します。（現在詳細な解説はありません）

* `link_url` : バトルに関連したURLを指定します。YouTube等の動画URLを指定することを想定しています。

* `note` : メモ（公開用）を指定します。長さはこのパラメータ以外も含めて POST データ全体が 12MiB 以内に収まる必要があります。

* `private_note` : メモ（非公開）を指定します。長さはこのパラメータ以外も含めて POST データ全体が 12MiB 以内に収まる必要があります。

* `start_at` : 試合の開始時間をUNIX時間、秒単位で指定します。

* `end_at` : 試合の終了時間をUNIX時間、秒単位で指定します。指定しない場合は現在時刻として処理されます。

* `agent` : 送信クライアントの名称を64文字以内で指定します。（例: `IkaLog` ） `agent` を指定するときは `agent_version` も指定する必要があります。

* `agent_version` : 送信クライアントのバージョンを255文字以内で指定します。（例: `1.2.3` ） `agent_version` を指定するときは `agent` も指定する必要があります。

* `agent_custom` : 送信クライアント定義の文字列を指定することができます。valid な UTF-8 の文字列である必要がありますが、中身については関知しません。長さはこのパラメータ以外も含めて POST データ全体が 12MiB 以内に収まる必要があります。

* `agent_variables` : 送信クライアント定義のシンプルなkey-valueペアを指定することができます。key, value ともに valid な UTF-8 の文字列である必要があります。この項目は「追加情報」としてバトル詳細に表示されます。長さはこのパラメータ以外も含めて POST データ全体が 12MiB 以内に収まる必要があります。
    - key は snake_case の英数字のみを推奨します。
    - key が数字のみを含むデータの取り扱いは未定義です。
    - value に文字列以外のデータを渡したときの取り扱いは未定義です。

* `uuid` : 送信クライアント定義の送信データIDを指定することができます。再送処理に利用することを想定し、同じIDが指定されていれば登録を行いません。重複判定期間はサーバ側定義で、現在は1日です。文字列の最大長は64文字で、名前に反してUUID(RFC 4122 他)である必要はありません（もちろんUUIDでも構いません）。また、ユーザが異なる(=APIKEYが異なる)場合には重複判定されません。
    - ID が指定されていないか空文字列の場合、無条件に登録します。（以前ままの動作）
    - ID が指定されていて、それが重複していなければ、登録します。（通常の動作）
    - ID が指定されていて、それが重複していれば、登録せずに以前のデータを返します。

* `automated` : 自動化された戦績の場合に `yes`、そうでない場合（例えば手動入力）に `no` を指定します。
    - 指定しなかった場合、原則として `no` として取り扱います。
    - ただし、`agent` が `IkaLog` の場合に限り `yes` として取り扱います。
    - 互換性のためであり、この挙動に依存するのは推奨しません。

* `gear` : ギアやギアパワーをして居します。詳細は後述します。

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

    - `weapon` と同じです。[指定する値の一覧ページ](https://stat.ink/api-info/weapon)

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

    - `propeller` : プロペラから飛び散ったインク（アンチョビットゲームズ）
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

* `my_kill` : `kill` のうち、自分がしたキルの数を整数値で指定します。（`kill` との整合性は現在のところ気にしません）

* `point` : 塗ったスコアを整数値で指定します。300Pの勝利ボーナスはこの値に含めます（画面に表示されているままを入力します。 `my_point` に対応します）


----

### `gears` ###

指定する場合は必ず次の3要素を取るオブジェクトで指定します。

* `headgear` : アタマのギアに関する情報

* `clothing` : フクのギアに関する情報

* `shoes` : クツのギアに関する情報

それぞれの情報は次の要素を取るオブジェクトで指定します。

* `gear` : ギアを特定して指定します。このパラメータの取り得る値はkey一覧を参照してください。このパラメータを指定した場合、 `primary_ability` は無視されます。
    - [key一覧 - アタマ](https://stat.ink/api-info/gear-headgear)
    - [key一覧 - フク](https://stat.ink/api-info/gear-clothing)
    - [key一覧 - クツ](https://stat.ink/api-info/gear-shoes)

* `primary_ability` : メインギアパワーを指定します。このパラメータの取り得る値は[key一覧](doc/api-1/constant/ability.md)を参照してください。

* `secondary_abilities` : サブギアパワーを配列で指定します。配列の要素数は1～3になります。取り得る値は `primary_ability` と同様です。まだ経験値が足りていないスロットは `null` を指定します。枠自体がない場合は要素数を減らして表します。

例えば次のようになります。

```js
{
    // ...
    "gears": {
        "headgear": {
            "gear": "hero_headset_replica", // ヒーローヘッズレプリカ（この指定でヒト速度アップのメインギアパワーが暗黙的に指定されます）
            "secondary_abilities": [
                "run_speed_up",
                "ink_saver_main",
                null // 3つめのスロットはまだ経験値が足りていないため解放されていない
            ]
        },
        "clothing": {
            "primary_ability": "haunt", // ギアは特定できなかったが、うらみであることはわかった
            "secondary_abilities": [ // スロットが2つあるがどちらも解放されていない（3つめはダウニーに開けてもらっていない）
                null,
                null
            ]
        },
        "shoes": // ...
    },
    // ...
}
```

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

        // マップの広さ(nullの可能性あり)
        "area": 2021,

        // このマップの公開日時(nullの可能性あり)
        "release_at": {
            "time": 1432738800,
            "iso8601": "2015-05-27T15:00:00+00:00"
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

GET /api/v1/gear
----------------

ギアの一覧をJSON形式で返します。 `key` が他のAPIで利用するときの値です。

ギアの出現順や構造の中の順番は特に決まっていません。（必要であれば利用者側で並び替えてください）

```js
[
    {
        // この値がバトル登録時に指定する値です
        "key": "corocoro_cap",

        // ギアの種類（装備箇所）を表します。"headgear" "clothing" "shoes" のいずれかになります。
        "type": {
            "key": "headgear",
            "name": {
                "ja_JP": "アタマ",
                "en_US": "Headgear",
                "en_GB": "Headgear",
                "es_ES": "Accesorios",
                "es_MX": "Accesorios"
            }
        },

        // ギアのブランドを示します。
        "brand": {
            "key": "zekko",
            "name": {
                "ja_JP": "エゾッコ",
                "en_US": "Zekko",
                "en_GB": "Zekko",
                "es_ES": "Ezko",
                "es_MX": "Ezko"
            },

            // ガチャでつきやすいギアパワー (null-able)
            "strength": {
                "key": "special_saver",
                "name": {
                    "ja_JP": "スペシャル減少量ダウン",
                    "en_US": "Special Saver",
                    "en_GB": "Special Saver",
                    "es_ES": "Reducción especial",
                    "es_MX": "Ahorro en especial"
                }
            },

            // ガチャでつきにくいギアパワー (null-able)
            "weakness": {
                "key": "special_charge_up",
                "name": {
                    "ja_JP": "スペシャル増加量アップ",
                    "en_US": "Special Charge Up",
                    "en_GB": "Special Charge Up",
                    "es_ES": "Recarga especial",
                    "es_MX": "Recarga especial"
                }
            }
        },

        // ギアの名前を示します。
        "name": {
            "ja_JP": "コロコロキャップ",
            "en_US": "CoroCoro Cap",
            "en_GB": "CoroCoro Cap",
        },

        // メインのギアパワー（ガチャで変えられない方）を示します。
        "primary_ability": {
            "key": "damage_up",
            "name": {
                "ja_JP": "攻撃力アップ",
                "en_US": "Damage Up",
                "en_GB": "Damage Up",
                "es_ES": "Superataque",
                "es_MX": "Ataque mejorado"
            }
        }
    },
    // ...
]
```

### パラメータ ###

複数のパラメータを指定したときは論理積(AND)になります。

それぞれの値が `key` として妥当でない場合はエラーが、論理積を求めた結果該当するものがないときは空の配列が返ります。

* `type` : ギアの種類を指定します。
    - `headgear` : アタマ
    - `clothing` : フク
    - `shoes` : クツ

* `brand` : ブランドの `key` を指定します。例: `/api/v1/gear?brand=krak_on`

* `ability` : メインのギアパワーの `key` を指定します。例: `/api/v1/gear?ability=damage_up`


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

----

GET /api/v1/weapon-trends
-------------------------

[ルール・マップ別のブキトレンドデータ取得についてはこちらを参照してください](doc/api-1/get-weapon-trends.md)

----

DELETE /api/v1/battle
---------------------

[バトルの削除についてはこちらを参照してください](doc/api-1/delete-battle.md)

----

PATCH /api/v1/battle
---------------------

[バトルの修正についてはこちらを参照してください](doc/api-1/delete-patch.md)

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
