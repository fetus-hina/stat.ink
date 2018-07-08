Battle CSV
==========

https://stat.ink/downloads からダウンロード可能な、リザルト情報の CSV のスキーマ情報です。<br>
This is a schema information for results data that can download from https://stat.ink/downloads .

Schema
------

- `#` から始まる行はコメントです。<br>If line starts with `#`, the line is a comment.
- `A`, `alpha` は「自分のチーム」、`B`, `bravo` は「敵のチーム」です。<br>`A`, `alpha` are "Good Guys" and `B`, `bravo` are "Bad Guys".
  - `A1` は情報の投稿者です。この情報を利用して統計を作成すると、大きく偏る可能性があります。<br>The `A1` is a stat.ink user (posted the battle). If you use this player's result, statistics may not be correct.

| column # | column name | example | meaning |
|----------|-------------|---------|---------|
|  0 | period | `2018-07-01T00:00:00+00:00` | どのピリオド（スケジュール）のバトルかを示します。<br>Indicates which period is. |
|  1 | game-ver | `3.1.0` | ゲームバージョンを示します。<br>Indicates official game version. |
|  2 | lobby-mode | `regular` | `regular`: レギュラーマッチ Regular battle<br>`gachi`: ガチマッチ Ranked battle<br>`fest`: フェスマッチ Splatfest |
|  3 | lobby | `standard` | `standard`: ひとりプレー（野良） Solo Queue <br>`squad_2` `squad_4`: リーグマッチ/チームフェス League(Ranked)/Team(Splatfest)<br>`private`: プライベートマッチ Private battle |
|  4 | mode | `nawabari` | `nawabari`: ナワバリバトル Turf War<br>`area`: ガチエリア Splat Zones<br>`yagura`: ガチヤグラ Tower Control<br>`hoko`: ガチホコバトル Raimaker<br>`asari`: ガチアサリ Clam Blitz |
|  5 | stage | `battera` | [ステージ Stage](https://stat.ink/api-info/stage2) |
|  6 | time | `180` | 試合の長さ（秒）<br>Time length of the battle (seconds) |
|  7 | win | `alpha` | `alpha`: Aチームが勝利した A team won<br>`bravo`: Bチームが勝利した B team won |
|  8 | knockout | `TRUE` | `TRUE`: ノックアウトで決着した Knocked out<br>`FALSE`: 時間切れで決着した Time was up<br>空白/empty: 不明かナワバリ unknown or Turf War |
|  9 | A1-weapon | `wakaba` | [ブキ Weapon](https://stat.ink/api-info/weapon2) |
| 10 | A1-kill-assist | `10` | キル+アシスト Kills + Assists |
| 11 | A1-kill | `6` | キル Kills |
| 12 | A1-assist | `4` | アシスト Assists |
| 13 | A1-death | `2` | デス Deaths |
| 14 | A1-special | `1` | スペシャル Special uses |
| 15 | A1-inked | `1000` | 塗った面積 Turf inked |
| 16 | A1-rank | `s+` | ウデマエ Rank |
| 17 | A1-level | `99` | ランク Level |
| 18 | A2-weapon | | |
| 19 | A2-kill-assist | | |
| 20 | A2-kill | | |
| 21 | A2-assist | | |
| 22 | A2-death | | |
| 23 | A2-special | | |
| 24 | A2-inked | | |
| 25 | A2-rank | | |
| 26 | A2-level | | |
| 27 | A3-weapon | | |
| 28 | A3-kill-assist | | |
| 29 | A3-kill | | |
| 30 | A3-assist | | |
| 31 | A3-death | | |
| 32 | A3-special | | |
| 33 | A3-inked | | |
| 34 | A3-rank | | |
| 35 | A3-level | | |
| 36 | A4-weapon | | |
| 37 | A4-kill-assist | | |
| 38 | A4-kill | | |
| 39 | A4-assist | | |
| 40 | A4-death | | |
| 41 | A4-special | | |
| 42 | A4-inked | | |
| 43 | A4-rank | | |
| 44 | A4-level | | |
| 45 | B1-kill-assist | | |
| 46 | B1-kill | | |
| 47 | B1-assist | | |
| 48 | B1-death | | |
| 49 | B1-special | | |
| 50 | B1-inked | | |
| 51 | B1-rank | | |
| 52 | B1-level | | |
| 53 | B2-weapon | | |
| 54 | B2-kill-assist | | |
| 55 | B2-kill | | |
| 56 | B2-assist | | |
| 57 | B2-death | | |
| 58 | B2-special | | |
| 59 | B2-inked | | |
| 60 | B2-rank | | |
| 61 | B2-level | | |
| 62 | B3-weapon | | |
| 63 | B3-kill-assist | | |
| 64 | B3-kill | | |
| 65 | B3-assist | | |
| 66 | B3-death | | |
| 67 | B3-special | | |
| 68 | B3-inked | | |
| 69 | B3-rank | | |
| 70 | B3-level | | |
| 71 | B4-weapon | | |
| 72 | B4-kill-assist | | |
| 73 | B4-kill | | |
| 74 | B4-assist | | |
| 75 | B4-death | | |
| 76 | B4-special | | |
| 77 | B4-inked | | |
| 78 | B4-rank | | |
| 79 | B4-level | | |

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
