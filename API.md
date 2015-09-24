IkaLogLog API
=============

POST /api/v1/battle
-------------------

`POST https://ikaloglog.ink/api/v1/battle`

バトル結果を投稿します。利用にはユーザに発行させるAPIキーが必要です。
（ユーザのプロフィールページから取得できます。アプリケーションはユーザにこれを指定させてください。）

投稿に画像を含む場合は `multipart/form-data` で、
画像を含まない場合は `application/x-www-form-urlencoded` または `multipart/form-data` で POST してください。

### パラメータ ###

`apikey` 以外は基本的に省略可能です。
省略する際は空の値を送信するか、項目自体を省略してください。

《共通》

* `apikey` : (必須) ユーザを特定するための API キーを指定します。（例: `ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopq` ）

* `rule` : ルールを次のうちいずれかの値で指定します。特定出来ない場合は送信の必要はありません。
    - `nawabari` : ナワバリバトル
    - `area` : ガチバトル／ガチエリア
    - `yagura` : ガチバトル／ガチヤグラ
    - `hoko` : ガチバトル／ガチホコ

* `map` : マップを次のうちいずれかの値で指定します。マップが特定出来ない場合は送信の必要はありません。
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

* `weapon` : 自分のブキを次のいずれかの値で指定します。ブキが特定出来ない場合は送信の必要はありません。
    - シューター
        - `52gal` : .52ガロン
        - `52gal-deco` : .52ガロンデコ
        - `96gal` : .96ガロン
        - `96gal-deco` : .96ガロンデコ
        - `bold` : ボールドマーカー
        - `dualsweeper` : デュアルスイーパー
        - `dualsweeper-custom` : デュアルスイーパーカスタム
        - `h3reelgun` : H3リールガン
        - `heroshooter-replica` : ヒーローシューターレプリカ
        - `hotblaster` : ホットブラスター
        - `hotblaster-custom` : ホットブラスターカスタム
        - `jetsweeper` : ジェットスイーパー
        - `jetsweeper-custom` : ジェットスイーパーカスタム
        - `l3reelgun` : L3リールガン
        - `l3reelgun-d` : L3リールガンD
        - `longblaster` : ロングブラスター
        - `momiji` : もみじシューター
        - `nova` : ノヴァブラスター
        - `nzap85` : N-ZAP 85
        - `nzap89` : N-ZAP 89
        - `octoshooter-replica` : オクタシューターレプリカ
        - `prime` : プライムシューター
        - `prime-collabo` : プライムシューターコラボ
        - `promodeler-mg` : プロモデラーMG
        - `promodeler-rg` : プロモデラーRG
        - `rapid` : ラピッドブラスター
        - `rapid-deco` : ラピッドブラスターデコ
        - `sharp` : シャープマーカー
        - `sharp-neo` : シャープマーカーネオ
        - `sshooter` : スプラシューター
        - `sshooter-collabo` : スプラシューターコラボ
        - `wakaba` : わかばシューター
    - ローラー
        - `carbon` : カーボンローラー
        - `dynamo` : ダイナモローラー
        - `dynamo-tesla` : ダイナモローラーテスラ
        - `heroroller-replica` : ヒーローローラーレプリカ
        - `hokusai` : ホクサイ
        - `pablo` : パブロ
        - `pablo-hue` : パブロ・ヒュー
        - `splatroller` : スプラローラー
        - `splatroller-collabo` : スプラローラーコラボ
    - チャージャー
        - `bamboo14mk1` : 14式竹筒銃・甲
        - `herocharger-replica` : ヒーローチャージャーレプリカ
        - `liter3k` : リッター3K
        - `liter3k-custom` : リッター3Kカスタム
        - `liter3k-scope` : 3Kスコープ
        - `splatcharger` : スプラチャージャー
        - `splatcharger-wakame` : スプラチャージャーワカメ
        - `splatscope` : スプラスコープ
        - `splatscope-wakame` : スプラスコープワカメ
        - `squiclean-a` : スクイックリンα
        - `squiclean-b` : スクイックリンβ
    - スロッシャー
        - `bucketslosher` : バケットスロッシャー
    - スピナー
        - `barrelspinner` : バレルスピナー
        - `splatspinner` : スプラスピナー

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

* `image_judge` : ジャッジ君による勝敗判定画像（PNG/JPEG、16:9）

* `image_result` : 個人成績の一覧画面の画像（PNG/JPEG、16:9）

《ナワバリバトル》

* `my_point` : 自分が塗ったスコアを整数値で指定します。300Pの勝利ボーナスはこの値に含めます（画面に表示されているままを入力します。例: `1024` ）

* `my_team_final_point` : バトル終了時の自チームのポイント数（%ではなくP）を整数値で指定します（例: `820` ）

* `his_team_final_point` : バトル終了時の相手チームのポイント数（%ではなくP）を整数値で指定します（例： `1126` ）

《ガチマッチ》

* `knock_out` : ノックアウトの有無を次のいずれかの値で指定します。
    - `yes` : 自チームまたは相手チームがノックアウト勝ちした
    - `no` : 両チームノックアウトしないままタイムアップした

* `my_team_count` : 自チームのカウント数

* `his_team_count` : 相手チームのカウント数
