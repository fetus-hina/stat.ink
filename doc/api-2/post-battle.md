`POST /api/v2/battle`
===================

| | |
|-|-|
|Verb|`POST`|
|URL|`https://stat.ink/api/v2/battle`|
|Return-Type|成功した場合 `201 Created` (bodyなし)<br>失敗した場合 `application/json` または `text/html`|
|認証|[必要](authorization.md)|

バトルデータをstat.inkに投稿します。

投稿時のリクエストボディの形式については[request-body.md](request-body.md)を参照してください。

投稿が成功したとき、結果はHTTPレスポンスヘッダに次のように返却されます。

|レスポンスヘッダ|値の例|意味|
|-|-|-|
|`Location`|`https://stat.ink/@username/spl2/42`|ブラウザで表示するバトル詳細ページです|
|`X-Api-Location`|`https://stat.ink/api/v2/battle/42`|APIで利用するためのURLです|
|`X-User-Screen-Name`|`username`|URL中に含まれるユーザ名です|
|`X-Battle-Id`|`42`|バトルを識別する番号です|


パラメータ
----------

基本的に全てのパラメータは省略可能です。ただし、あまりに内容がない場合はエラーになります。

POST データは全体で 12MiB 以内である必要があります。

|キー|値の型||
|-|-|-|
|`uuid`|文字列(64文字以下)<br>推奨:[UUID](https://tools.ietf.org/html/rfc4122)|バトル重複のチェックに使用します。<br>同一UUIDのPOSTが一定期間内に行われた場合、サーバは保存せずに以前の情報を返します。<br>クライアントはバトルごとに任意のUUIDまたは文字列を生成して付与することができます。<br>クライアントが生成した文字列がUUIDに見えない時はサーバ側でそれを元に適当なUUIDに変換します。|
|`splatnet_number`|整数(1～)|イカリング2(SplatNet 2)のバトル番号(`battle_number`)を指定します。|
|`lobby`|指定文字列|どのロビーからバトルを行ったかを指定します。（後述）|
|`mode`|指定文字列|どのプレーモードでバトルを行ったかを指定します。（後述）|
|`rule`|指定文字列|どのルールでバトルを行ったかを指定します。（後述）|
|`stage`|指定文字列|どのステージ（マップ）でバトルを行ったかを指定します。（後述）|
|`weapon`|指定文字列|このバトルを投稿するプレーヤーがどのブキを使用したかを指定します。（後述）|
|`result`|指定文字列|バトルの勝敗に応じた値を設定します。<br>`win` : 勝利<br>`lose` : 敗北|
|`knock_out`|指定文字列|ガチマッチルールで終了方法に応じた値を設定します。<br>`yes` : ノックアウト勝ち/負け<br>`no` : 時間切れ勝ち/負け|
|`rank_in_team`|整数(1～4)|プレーヤーがリザルト画面のチーム内何番目に表示されたかを指定します。|
|`kill`|整数(0～99)|プレーヤーのキルの数を指定します。|
|`death`|整数(0～99)|プレーヤーのデスの数を指定します。|
|`max_kill_combo`|整数(0～)|「たおした」表示中にさらにキルしたことを「コンボ」と定義したときのバトル中の最大のコンボ数を指定します。|
|`max_kill_streak`|整数(0～)|バトル開始またはリスポーンからデスするまたは試合終了までのキル(=行動不能までに何キル連続したか)を「ストリーク」と定義したときのバトル中の最大のストリーク数を指定します。|
|`kill_or_assist`|整数(0～)|プレーヤーのキル数+アシスト数を指定します。(Splatoon 2仕様のスコアボード用)|
|`special`|整数(0～)|プレーヤーのスペシャル使用数を指定します。(Splatoon 2仕様のスコアボード用)|
|`level`|整数(1～50?)|プレーヤーのバトル開始時のランクを指定します。（Splatoon 2の仕様次第で変更の可能性あり）|
|`level_after`|整数(1～50?)|同様にバトル終了後のランクを指定します。|
|`rank`|指定文字列|プレーヤーのバトル開始時のウデマエを指定します。|
|`rank_exp`|整数(0～50)|S+, バトル開始時のウデマエの数字を指定します。|
|`rank_after`|指定文字列|プレーヤーのバトル終了時のウデマエを指定します。|
|`rank_exp_after`|整数(0～50)|S+, バトル終了時のウデマエの数字を指定します。|
|`my_point`|整数(0～)|自分が塗ったポイントと勝利ボーナスを合計した数値を指定します。（画面に表示されるままのポイント）|
|`estimate_gachi_power`|整数|推定ガチパワーを指定します。|
|`league_point`|数値(e.g.1234.5)|リーグパワーを指定します。|
|`my_team_estimate_league_point`|整数|自分のチームの推定リーグパワーを指定します。|
|`his_team_estimate_league_point`|整数|相手のチームの推定リーグパワーを指定します。|
|`my_team_point`|整数(0～)|リザルト画面の自分のチームの最終塗りポイントを指定します。|
|`his_team_point`|整数(0～)|同様に敵チームのポイントを指定します。|
|`my_team_percent`|数値(0.0～100.0)|同様に自分のチームの塗りポイントのパーセンテージを指定します。|
|`his_team_percent`|数値(0.0～100.0)|同様に敵チームのパーセンテージを指定します。|
|`my_team_count`|整数(0～100)|リザルト画面の自分のチームの最終的な取得カウントを指定します。（ガチマッチ用、初期0、ノックアウト100）|
|`his_team_count`|整数(0～100)|同様に敵チームのカウントを指定します。|
|`my_team_id`|文字列|イカリング2(SplatNet 2)JSON中の自チームを特定するためのIDを指定します。(`tag_id` ?)|
|`his_team_id`|文字列|同様に敵チームを特定するためのIDを指定しますが、おそらく取得できないと思います。|
|`gender`|指定文字列|自分のイカの性別を指定します。この項目はフェスで使用します。<br>`boy` : ボーイ<br>`girl` : ガール|
|`fest_title`|指定文字列|フェスの称号を指定します。|
|`fest_exp`|整数(0～99)|フェスの経験値を指定します。「カリスマ 42/99」の 42|
|`fest_title_after`|指定文字列|フェスの称号を指定します。（バトル後の値）|
|`fest_exp_after`|整数(0～99)|フェスの経験値を指定します。（バトル後の値）|
|`fest_power`|数値(e.g. 1234.5)|フェスパワーを指定します。|
|`my_team_estimate_fest_power`|整数|自チームの概算フェスパワーを指定します。|
|`his_team_estimate_fest_power`|整数|敵チームの概算フェスパワーを指定します。|
|`players`|構造体|自分を含めた両チーム8人分のデータを指定します。（後述）|
|`death_reasons`|マップ|自分が死んだ死因を指定します。（後述）|
|`events`|配列|ゲーム中の進行状況に対応する時系列データを指定します。（後述）|
|`splatnet_json`|オブジェクト or 文字列|イカリング2(SplatNet 2)のバトルを示すJSONをそのまま指定します。JSONをデコードしたオブジェクトをそのまま指定しても、JSONを文字列としてシリアライズした結果を指定しても構いません。|
|`automated`|指定文字列|自動化された投稿か手動による投稿かを指定します。<br>この情報は、全体統計に使用できるかを判定するのに使用します。<br>（意図的に勝利データのみを送信するのが容易かを示すものと考えてください）<br>`yes` : 自動化されている（機械的に処理されている）<br>`no` : 自動化されていない（手動入力等）|
|`link_url`|文字列(URL)|このバトルに関連したURLを指定します。<br>一般的には、バトルの録画をアップロードしたYouTubeへのURLを指定します。|
|`note`|文字列|このバトルに関連するメモを指定します。|
|`private_note`|文字列|同様ですが、本人以外には表示不可能な秘密のメモを指定します。|
|`agent`|文字列(64文字以下)|投稿に利用するクライアントの名称を指定します。<br>例えば `IkaLog` などのソフトウェアの名称を指定します。<br>他のクライアントと重複しないような名前をつけてください。<br>これを指定するとき、`agent_version`も指定必須となります。|
|`agent_version`|文字列(255文字以下)|`agent`で示されるクライアントのバージョン情報を指定します。<br>アーキテクチャやOSに関する情報を含めることもできます。|
|`agent_custom`|文字列|クライアントがクライアント自身のために利用するデータを指定できます。stat.inkは内容には関知しません。|
|`agent_variables`|マップ|クライアント定義のキー・バリューを指定出来ます。（後述）|
|`image_judge`|画像バイナリ(PNG/JPEG)|ジャッジ画面のスクリーンショットを指定します。|
|`image_result`|画像バイナリ(PNG/JPEG)|リザルト画面（8人分のデータが並んだ画面）のスクリーンショットを指定します。|
|`image_gear`|画像バイナリ(PNG/JPEG)|ギア構成画面のスクリーンショットを指定します。|
|`start_at`|整数(UNIX時間)|バトル開始日時をunix時間（単位は秒）で指定します。|
|`end_at`|整数(UNIX時間)|バトル終了日時をunix時間（単位は秒）で指定します。|


`lobby`, `mode`, `rule`
-----------------------

`lobby` は次のいずれかの値を取ります。

|指定文字列|内容|
|-|-|
|`standard`|ひとりプレー（野良、ソロ）<br>Solo Queue|
|`squad_2`|リーグ（2人）<br>League (Twin)|
|`squad_4`|リーグ（4人）、フェス（チーム）<br>League (Quad), Splatfest (Team)|
|`private`|プライベートマッチ<br>Private battle|

`mode` は次のいずれかの値を取ります。

|指定文字列|内容|
|-|-|
|`regular`|レギュラーマッチ<br>Regular Battle|
|`gachi`|ガチバトル<br>Ranked Battle|
|`fest`|フェス<br>Splatfest|
|`private`|プライベートマッチ<br>Private Battle|

`rule` は次のいずれかの値を取ります。

|指定文字列|内容|
|-|-|
|`nawabari`|ナワバリバトル<br>Turf War|
|`area`|ガチエリア<br>Splat Zones|
|`yagura`|ガチヤグラ<br>Tower Control|
|`hoko`|ガチホコ<br>Rainmaker|

`lobby`, `mode`, `rule` は現実的には次のような組み合わせになります。

|プレー|プレー人数|`lobby`|`mode`|`rule`||
|------|----------|-------|------|------|-|
|レギュラー<br>Regular|1人<br>Solo|`standard`|`regular`|`nawabari`||
|レギュラー<br>Regular|合流<br>Join to friend|`standard`|`regular`|`nawabari`|区別しない<br>Same as Solo|
|ガチマッチ<br>Ranked|1人<br>Solo|`standard`|`gachi`|`area`, `yagura`, `hoko`|
|ガチマッチ<br>Ranked|リーグ（2人）<br>League (Twin)|`squad_2`|`gachi`|`area`, `yagura`, `hoko`|
|ガチマッチ<br>Ranked|リーグ（4人）<br>League (Quad)|`squad_4`|`gachi`|`area`, `yagura`, `hoko`|
|フェス<br>Splatfest|ソロ<br>Solo|`standard`|`fest`|`nawabari`||
|フェス<br>Splatfest|チーム<br>Team|`squad_4`|`fest`|`nawabari`||
|プラベ<br>Private|-|`private`|`private`|`nawabari`, `area`, `yagura`, `hoko`|


`stage`
-------

[GET /api/v2/stage](get-stage.md)で詳細情報が取得可能です。

|指定文字列|ステージ||
|-|-|-|
|`ama`|海女美術大学<br>Inkblot Art Acodemy||
|`battera`|バッテラストリート<br>The Reef||
|`chozame`|チョウザメ造船<br>Sturgeon Shipyard||
|`engawa`|エンガワ河川敷<br>Snapper Canal||
|`fujitsubo`|フジツボスポーツクラブ<br>Musselforge Fitness||
|`gangaze`|ガンガゼ野外音楽堂<br>Starfish Mainstage||
|`hokke`|ホッケふ頭<br>Port Mackerel||
|`kombu`|コンブトラック<br>Humpback Pump Track|互換性のため`combu`も受け付けます<br>Also accepts `combu` for compatibility|
|`manta`|マンタマリア号<br>Manta Maria||
|`mozuku`|モズク農園<br>Kelp Dome||
|`tachiuo`|タチウオパーキング<br>Moray Towers||
|`mystery`|ミステリーゾーン<br>Shifty Station|フェス専用ステージ<br>For Splatfest|


`weapon`
--------

[GET /api/v2/weapon](get-weapon.md)で詳細情報が取得可能です。

また、実際のデータベースを参照した一覧ページが[ここにあります](https://stat.ink/api-info/weapon2)。<br>
There is a listing page [here](https://stat.ink/api-info/weapon2).

|Value|SplatNet|Weapon Name|Remarks|
|-|-|-|-|
|`52gal`|`50`|.52ガロン<br>.52 Gal||
|`96gal`|`80`|.96ガロン<br>.96 Gal||
|`bold`|`0`|ボールドマーカー<br>Sploosh-o-matic||
|`heroshooter_replica`|`45`|ヒーローシューター レプリカ<br>Hero Shot Replica||
|`jetsweeper`|`90`|ジェットスイーパー<br>Jet Squelcher||
|`momiji`|`11`|もみじシューター<br>Custom Splattershot Jr.||
|`nzap85`|`60`|N-ZAP85<br>N-ZAP '85||
|`prime`|`70`|プライムシューター<br>Splattershot Pro||
|`prime_collabo`|`71`|プライムシューターコラボ<br>Forge Splattershot Pro||
|`promodeler_mg`|`30`|プロモデラーMG<br>Aerospray MG||
|`promodeler_rg`|`31`|プロモデラーRG<br>Aerospray RG||
|`sharp`|`20`|シャープマーカー<br>Splash-o-matic||
|`sshooter`|`40`|スプラシューター<br>Splattershot||
|`sshooter_collabo`|`41`|スプラシューターコラボ<br>Tentatek Splattershot||
|`wakaba`|`10`|わかばシューター<br>Splattershot Jr.||
|`clashblaster`|`230`|クラッシュブラスター<br>Clash Blaster||
|`heroblaster_replica`|`215`|ヒーローブラスター レプリカ<br>Hero Blaster Replica||
|`hotblaster`|`210`|ホットブラスター<br>Blaster||
|`hotblaster_custom`|`211`|ホットブラスターカスタム<br>Custom Blaster||
|`longblaster`||ロングブラスター<br>Range Blaster||
|`nova`|`200`|ノヴァブラスター<br>Luna Blaster||
|`rapid`|`240`|ラピッドブラスター<br>Rapid Blaster||
|`rapid_elite`|`250`|Rブラスターエリート<br>Rapid Blaster Pro||
|`h3reelgun`|`310`|H3リールガン<br>H-3 Nozzlenose||
|`l3reelgun`|`300`|L3リールガン<br>L-3 Nozzlenose||
|`dualsweeper`|`5030`|デュアルスイーパー<br>Dualie Squelchers||
|`heromaneuver_replica`|`5015`|ヒーローマニューバー レプリカ<br>Hero Dualie Replicas||
|`maneuver`|`5010`|スプラマニューバー<br>Splat Dualies|互換性のため `manueuver` も受け付けます<br>Also accepts `manueuver` for compatibility|
|`maneuver_collabo`|`5011`|スプラマニューバーコラボ<br>Enperry Splat Dualies|互換性のため `manueuver_collabo` も受け付けます<br>Also accepts `manueuver_collabo` for compatibility|
|`sputtery`|`5000`|スパッタリー<br>Dapple Dualies||
|`carbon`|`1000`|カーボンローラー<br>Carbon Roller||
|`dynamo`|`1020`|ダイナモローラー<br>Dynamo Roller||
|`heroroller_replica`|`1015`|ヒーローローラー レプリカ<br>Hero Roller Replica||
|`splatroller`|`1010`|スプラローラー<br>Splat Roller||
|`splatroller_collabo`|`1011`|スプラローラーコラボ<br>Krak-On Splat Roller||
|`variableroller`|`1030`|ヴァリアブルローラー<br>Flingza Roller||
|`herobrush_replica`|`1115`|ヒーローブラシ レプリカ<br>Herobrush Replica||
|`hokusai`|`1110`|ホクサイ<br>Octobrush||
|`pablo`|`1100`|パブロ<br>Inkbrush||
|`bamboo14mk1`|`2050`|14式竹筒銃・甲<br>Bamboozler 14 Mk I||
|`herocharger_replica`|`2015`|ヒーローチャージャー レプリカ<br>Hero Charger Replica||
|`liter4k`|`2030`|リッター4K<br>E-liter 4K||
|`liter4k_custom`|`2031`|リッター4Kカスタム<br>Custom E-liter 4K||
|`liter4k_scope`|`2040`|4Kスコープ<br>E-liter 4K Scope||
|`liter4k_scope_custom`|`2041`|4Kスコープカスタム<br>Custom E-liter 4K Scope||
|`soytuber`|`2060`|ソイチューバー<br>Goo Tuber||
|`splatcharger`|`2010`|スプラチャージャー<br>Splat Charger||
|`splatcharger_collabo`|`2011`|スプラチャージャーコラボ<br>Firefin Splat Charger||
|`splatscope`|`2020`|スプラスコープ<br>Splatterscope||
|`splatscope_collabo`|`2021`|スプラスコープコラボ<br>Firefin Splatterscope||
|`squiclean_a`|`2000`|スクイックリンα<br>Classic Squiffer||
|`bucketslosher`|`3000`|バケットスロッシャー<br>Slosher||
|`heroslosher_replica`|`3005`|ヒーロースロッシャー レプリカ<br>Hero Slosher Replica||
|`hissen`|`3010`|ヒッセン<br>Tri-Slosher||
|`screwslosher`|`3020`|スクリュースロッシャー<br>Sloshing Machine||
|`barrelspinner`|`4010`|バレルスピナー<br>Heavy Splatling||
|`barrelspinner_deco`|`4011`|バレルスピナーデコ<br>Heavy Splatling Deco||
|`herospinner_replica`|`4015`|ヒーロースピナー レプリカ<br>Hero Splatling Replica||
|`splatspinner`|`4000`|スプラスピナー<br>Mini Splatling||
|`campingshelter`|`6010`|キャンピングシェルター<br>Tenta Brella||
|`heroshelter_replica`|`6005`|ヒーローシェルター レプリカ<br>Hero Brella Replica||
|`parashelter`|`6000`|パラシェルター<br>Splat Brella||


`rank`, `rank_after`
--------------------

`rank` および `rank_after` にはプレー開始前後のウデマエを指定します。

|指定文字列|ウデマエ|
|-|-|
|c-|C-|
|c |C |
|c+|C+|
|b-|B-|
|b |B |
|b+|B+|
|a-|A-|
|a |A |
|a+|A+|
|s |S |
|s+|S+|


`fest_title`, `fest_title_after`
--------------------------------

`fest_title`, `fest_title_after` にはバトル前、バトル後のフェスの称号を指定します。

指定する値には性別を示唆するものも含まれますが、 **性別によらず同じものを指定** します。（性別は `gender` で指定します）<br>
Regardless of gender, specify the same key-string. (Use `gender` to specify gender)

|キー|称号（ボーイ）<br>Title (Boy)|称号（ガール）<br>Title (Girl)|
|-|-|-|
|`fanboy`|ふつうの《お題》ボーイ<br>*Something* Fanboy|ふつうの《お題》ガール<br>*Something* Fangirl|
|`fiend`|まことの《お題》ボーイ<br>*Something* Fiend|まことの《お題》ガール<br>*Something* Fiend|
|`defender`|スーパー《お題》ボーイ<br>*Something* Defender|スーパー《お題》ガール<br>*Something* Defender|
|`champion`|カリスマ《お題》ボーイ<br>*Something* Champion|カリスマ《お題》ガール<br>*Something* Champion|
|`king`|えいえんの《お題》ボーイ<br>*Something* King|えいえんの《お題》ガール<br>*Something* Queen|

なお、キーの値は英語（北米）版のボーイ用の称号を小文字にしたものです。


`players`
---------

`players`には、敵味方2～8人の情報を配列で設定します。配列の中身は後述の構造体で、全体としてこのようになっています。(JSON)

```js
{
  // ...
  "players": [
    {
      // 次に指定する構造体
    },
    // 2～8要素
  ],
  // ...
}
```

|キー|値の型||
|-|-|-|
|`team`|指定文字列|`my` : 自分のチーム<br>`his` : 相手のチーム|
|`is_me`|指定文字列|`yes` : 投稿者<br>`no` : 投稿者以外|
|`weapon`|指定文字列|そのプレーヤーの持っていた武器(指定する値は前述の通り)|
|`level`|整数(1～50?)|そのプレーヤーのランクを指定します|
|`rank`|指定文字列|そのプレーヤーのウデマエを指定します(指定する値は前述の通り)|
|`rank_in_team`|整数(1～4)|そのプレーヤーが各チーム内で何番目に表示されているかを指定します|
|`kill`|整数(0～99)|そのプレーヤーのキル数を指定します|
|`death`|整数(0～99)|そのプレーヤーのデス数を指定します|
|`kill_or_assist`|整数(0～)|そのプレーヤーのキル数+アシスト数を指定します|
|`special`|整数(0～)|そのプレーヤーのスペシャル使用数を指定します|
|`point`|整数(0～)|そのプレーヤーのポイント(塗った面積+ボーナス)を指定します|
|`my_kill`|整数(0～)|そのプレーヤーのデス数のうち、投稿プレーヤーが倒した数を指定します|
|`name`|文字列(1～10文字)|そのプレーヤーの名前を指定します|
|`gender`|指定文字列|そのプレーヤーの性別<br>`boy`: ボーイ<br>`girl`: ガール|
|`fest_title`|指定文字列|そのプレーヤーのバトル中のフェス称号（指定する値は前述の通り）|
|`splatnet_id`|文字列|イカリング2(SplatNet 2)上で示される、プレーヤーを特定するID(`principal_id`)を指定します|


`death_reasons`
---------------

プレーヤーの死因とその回数を集計の上、設定します。

死因をキー、回数を値とするマップを設定します。

場外転落で1回、スプラマニューバーで2回死んだとき、つぎのような電文になります。

```js
{
  // ...
  "death_reasons": {
    "oob": 1,
    "manueuver": 2
  },
  // ...
}
```

キーとなる死因は、`weapon`のための指定文字列と、次の各値になります。

|指定文字列|死因|
|-|-|
|`unknown`|死因不明<br>Unknown|
|`fall`|場外転落<br>Out of bounds (fall)|
|`drown`|水死<br>Out of bounds (water)|
|`oob`|場外（詳細不明）<br>Out of bounds (details unknown)|

|指定文字列|死因|
|-|-|
|`curlingbomb`|カーリングボム<br>Curling Bomb|
|`kyubanbomb`|キューバンボム<br>Suction Bomb|
|`quickbomb`|クイックボム<br>Burst Bomb|
|`robotbomb`|ロボットボム<br>Autobomb|
|`splashbomb`|スプラッシュボム<br>Splat Bomb|
|`splashshield`|スプラッシュシールド<br>Splash Wall|
|`sprinkler`|スプリンクラー<br>Sprinkler|
|`trap`|トラップ<br>Ink Mine|

|指定文字列|死因|
|-|-|
|`amefurashi`|アメフラシ<br>Ink Storm|
|`bubble`|バブルランチャー<br>Bubble Blower|
|`chakuchi`|スーパーチャクチ<br>Splashdown|
|`jetpack`|ジェットパック<br>Inkjet|
|`missile`|マルチミサイル<br>Tenta Missiles|
|`presser`|ハイパープレッサー<br>Sting Ray|
|`sphere`|イカスフィア<br>Baller|

`events`
--------

バトル中に発生した各種時系列イベントデータを配列で指定します。

```js
{
  // ...
  "events": [
    {
      "at": 0.1,
      "type": "point",
      "point": 2
    },
    // ...
  ],
  // ...
}
```

|キー|値の型|必須?||
|-|-|-|-|
|`at`|数値|必須|イベント発生タイミング（バトル開始からの秒）|
|`type`|文字列|必須|イベント種別|
|type依存のキー|||type依存の値


### `type: point`

塗りポイントの変化に関するイベントです。

|キー|値の型|必須?||
|-|-|-|-|
|`point`|数値(0～)|必須|塗りポイントを指定します|


### `type: killed`

プレーヤーが他のプレーヤーをキルしたことを示すイベントです。

追加の情報はありません。


### `type: dead`

プレーヤーが他のプレーヤーにキルされたことを示すイベントです。

|キー|値の型|必須?||
|-|-|-|-|
|`reason`|指定文字列||死因を指定します。`death_reasons`の指定値が利用できます。|


### `type: special%`

プレーヤーのスペシャルゲージのたまり状況を示すイベントです。

|キー|値の型|必須?||
|-|-|-|-|
|`point`|数値(0～100)|必須|スペシャルゲージのたまりを指定します|


### `type: alive_inklings`

各プレーヤーの生死状況を示すイベントです。変化したタイミングでの発行を期待しています。

|キー|値の型|必須?||
|-|-|-|-|
|`my_team`|配列|必須|味方チームの生死を示します。|
|`his_team`|配列|必須|敵チームの生死を示します。|

`my_team`, `his_team` ともに、最大4要素の配列で、生きているイカを`true`、死んでいるイカを`false`で示します。

```js
{
  "at": 42.0,
  "type": "alive_inklings",
  "my_team": [ true, true, true, false ],
  "his_team": [ true, true, false, true ]
}
```

書きかけ


`agent_variables`
-----------------

送信クライアント定義のシンプルなkey-valueペアを指定することができます。
key, value ともに valid な UTF-8 の文字列である必要があります。
この項目は「追加情報」としてバトル詳細に表示されます。
長さはこのパラメータ以外も含めて POST データ全体が 12MiB 以内に収まる必要があります。

`key` は `snake_case` の英数字のみを推奨します。

`key` が数字のみを含むデータの取り扱いは未定義です。

`value` に文字列以外のデータを渡したときの取り扱いは未定義です。


----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。

