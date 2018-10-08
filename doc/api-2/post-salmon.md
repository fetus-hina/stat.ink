`POST /api/v2/***`
===================

UNDER DEVELOPMENT

| | |
|-|-|
|Verb|`POST`|
|URL|`https://stat.ink/api/v2/TBD`|
|Return-Type|Succeed: `201 Created` (no body)<br>Failed: HTTP Error 4xx or 5xx with `application/json` or `text/html` data<br>Already exists: `302 Found` (no body)|
|Auth|[Needed](authorization.md)|

Post a salmon run shift to stat.ink.


Request Structure
-----------------

All parameters are optional.

|パラメータ<br>Parameter|型<br>Type|値<br>Value| |
|-----------------------|----------|-----------|-|
|`splatnet_number`|integer|1-|SplatNet `job_id`|
|`stage`|key string|e.g. `dam`|[§Stage](#Stage)|
|`clear_waves`|integer|0-3|`3` if cleared. `0` if failed in wave 1|
|`title`|key string|e.g. `profreshional`|[§Title](#Title), befor the work|
|`title_exp`|integer|0-999|Profreshional "40"/999, before the work|
|`title_after`|key string|After the work|
|`title_exp_after`|integer|0-999|After the work|
|`danger_rate`|number|0.0-999.9| |
|`boss_appearances`|key-value|See below|Number of bosses|
|`waves`|array of `wave` structure|See below|Wave data, 1-3 elements|
|`start_at`|integer|UNIX time| |
|`end_at`|integer|UNIX time| |

Full example:

```js
{
  "splatnet_number": 3527,
  "stage": "donburako",
  "clear_waves": 2, // failed on Wave 3
  "title": "profreshional",
  "title_exp": 370,
  "title_after": "profreshional",
  "title_exp_after": 370,
  "danger_rate": 170.4,
  "boss_appearances": {
    "drizzler": 6,
    "flyfish": 7,
    "maws": 10,
    "scrapper": 6,
    "steel_eel": 8,
    "steelhead": 5,
    "stinger": 15
  },
  "waves": [
    { // wave 1
      "known_occurrence": null,
      "water_level": "normal",
      "golden_egg_quota": 18,
      "golden_egg_appearances": 45,
      "golden_egg_delivered": 29,
      "power_egg_collected": 706
    },
    { // wave 2
      "known_occurrence": null,
      "water_level": "low",
      "golden_egg_quota": 20,
      "golden_egg_appearances": 54,
      "golden_egg_delivered": 40,
      "power_egg_collected": 1457
    },
    { // wave 3 (failed)
      "known_occurrence": null,
      "water_level": "normal",
      "golden_egg_quota": 22,
      "golden_egg_appearances": 51,
      "golden_egg_delivered": 17,
      "power_egg_collected": 820
    }
  ],
  "start_at": 1538836518, // 2018-10-06T23:35:18+09:00
  "end_at": null // may not be presented in SplatNet JSON
}
```

`boss_appearances`
------------------

When 2 Stingers and 4 Steelheads appearances, the key-value map should be:

```js
{
  // ...
  "boss_appearances": {
    "stinger": 2,
    "steelhead": 4
  },
  // ...
}
```

See also: [§Boss](#Boss)

`wave` structure
----------------

```js
{
  // ...
  "waves": [
    { /* wave 1 */ },
    { /* wave 2 */ },
    { /* wave 3 */ }
  ],
  // ...
}
```

|パラメータ<br>Parameter|型<br>Type|値<br>Value| |
|-|-|-|-|
|`known_occurrence`|key string|e.g. `fog`|[§Known Occurrence](#Known%20Occurrence). `null` or empty-string if "standard" work|
|`water_level`|key string|e.g. `high`|[§Water Level](#Water%20Level)|
|`golden_egg_quota`|integer|1-25|Players should deliver golden eggs|
|`golden_egg_appearances`|integer|0-|Golden Egg appearances, "pops"|
|`golden_egg_delivered`|integer|0-|Golden Egg delivered, "collected"|
|`power_egg_collected`|integer|0-|Power Egg collected|


Stage
-----

<!--replace:stage-->
|指定文字列<br>Key String|名前<br>Name                              |イカリングヒント<br>SplatNet Hint                                |
|------------------------|------------------------------------------|-----------------------------------------------------------------|
|`shaketoba`             |海上集落シャケト場<br>Lost Outpost        |`/images/coop_stage/6d68f5baa75f3a94e5e9bfb89b82e7377e3ecd2c.png`|
|`donburako`             |難破船ドン・ブラコ<br>Marooner's Bay      |`/images/coop_stage/e07d73b7d9f0c64e552b34a2e6c29b8564c63388.png`|
|`tokishirazu`           |トキシラズいぶし工房<br>Salmonid Smokeyard|`/images/coop_stage/e9f7c7b35e6d46778cd3cbc0d89bd7e1bc3be493.png`|
|`dam`                   |シェケナダム<br>Spawning Grounds          |`/images/coop_stage/65c68c6f0641cc5654434b78a6f10b0ad32ccdee.png`|
<!--endreplace-->


Title
-----

a.k.a "Rank" or "Grade"

<!--replace:title-->
|指定文字列<br>Key String|イカリング<br>SplatNet|名前<br>Name              |備考<br>Remarks|
|------------------------|----------------------|--------------------------|---------------|
|`intern`                |`0`                   |けんしゅう<br>Intern      |               |
|`apprentice`            |`1`                   |かけだし<br>Apprentice    |               |
|`part_timer`            |`2`                   |はんにんまえ<br>Part-Timer|               |
|`go_getter`             |`3`                   |いちにんまえ<br>Go-Getter |               |
|`overachiever`          |`4`                   |じゅくれん<br>Overachiever|               |
|`profreshional`         |`5`                   |たつじん<br>Profreshional |               |
<!--endreplace-->


Boss
----

<!--replace:boss-->
|指定文字列<br>Key String|イカリング<br>SplatNet     |名前<br>Name         |備考<br>Remarks|
|------------------------|---------------------------|---------------------|---------------|
|`drizzler`              |`21`<br>`sakerocket`       |コウモリ<br>Drizzler |               |
|`flyfish`               |`9`<br>`sakelien-cup-twins`|カタパッド<br>Flyfish|               |
|`goldie`                |`3`<br>`sakelien-golden`   |キンシャケ<br>Goldie |               |
|`griller`               |`16`<br>`sakedozer`        |グリル<br>Griller    |               |
|`maws`                  |`15`<br>`sakediver`        |モグラ<br>Maws       |               |
|`scrapper`              |`12`<br>`sakelien-shield`  |テッパン<br>Scrapper |               |
|`steel_eel`             |`13`<br>`sakelien-snake`   |ヘビ<br>Steel Eel    |               |
|`steelhead`             |`6`<br>`sakelien-bomber`   |バクダン<br>Steelhead|               |
|`stinger`               |`14`<br>`sakelien-tower`   |タワー<br>Stinger    |               |
<!--endreplace-->


Water Level 海面の高さ
----------------------

<!--replace:water-level-->
|指定文字列<br>Key String|イカリング<br>SplatNet|名前<br>Name     |備考<br>Remarks|
|------------------------|----------------------|-----------------|---------------|
|`low`                   |`low`                 |干潮<br>Low Tide |               |
|`normal`                |`normal`              |普通<br>Mid Tide |               |
|`high`                  |`high`                |満潮<br>High Tide|               |
<!--endreplace-->


Known Occurrence
----------------

<!--replace:event-->
|指定文字列<br>Key String|イカリング<br>SplatNet|名前<br>Name                     |備考<br>Remarks|
|------------------------|----------------------|---------------------------------|---------------|
|`cohock_charge`         |`cohock-charge`       |ドスコイ大量発生<br>Cohock Charge|               |
|`fog`                   |`fog`                 |霧<br>Fog                        |               |
|`goldie_seeking`        |`goldie-seeking`      |キンシャケ探し<br>Goldie Seeking |               |
|`griller`               |`griller`             |グリル発進<br>The Griller        |               |
|`mothership`            |`the-mothership`      |ハコビヤ襲来<br>The Mothership   |               |
|`rush`                  |`rush`                |ラッシュ<br>Rush                 |               |
<!--endreplace-->

Note: Water-levels is (and will) not defined.<br>
メモ: 海面の高さはここでは定義されません。


Main Weapon 支給ブキ
--------------------

<!--replace:weapon-->
|指定文字列<br>Key String|イカリング<br>SplatNet|ブキ<br>Weapon Name                                      |備考<br>Remarks|
|------------------------|----------------------|---------------------------------------------------------|---------------|
|`kuma_blaster`          |`20000`               |バイト専用 クマサン印のブラスター<br>Grizzco Blaster     |               |
|`kuma_brella`           |`20010`               |バイト専用 クマサン印のシェルター<br>Grizzco Brella      |               |
|`kuma_charger`          |`20020`               |バイト専用 クマサン印のチャージャー<br>Grizzco Charger   |               |
|`kuma_slosher`          |`20030`               |バイト専用 クマサン印のスロッシャー<br>Grizzco Slosher   |               |
|`52gal`                 |`50`                  |バイト専用 .52ガロン<br>.52 Gal                          |               |
|`96gal`                 |`80`                  |バイト専用 .96ガロン<br>.96 Gal                          |               |
|`promodeler_rg`         |`31`                  |バイト専用 プロモデラーRG<br>Aerospray RG                |               |
|`hotblaster`            |`210`                 |バイト専用 ホットブラスター<br>Blaster                   |               |
|`clashblaster`          |`230`                 |バイト専用 クラッシュブラスター<br>Clash Blaster         |               |
|`h3reelgun`             |`310`                 |バイト専用 H3リールガン<br>H-3 Nozzlenose                |               |
|`jetsweeper`            |`90`                  |バイト専用 ジェットスイーパー<br>Jet Squelcher           |               |
|`l3reelgun`             |`300`                 |バイト専用 L3リールガン<br>L-3 Nozzlenose                |               |
|`nova`                  |`200`                 |バイト専用 ノヴァブラスター<br>Luna Blaster              |               |
|`nzap85`                |`60`                  |バイト専用 N-ZAP85<br>N-ZAP '85                          |               |
|`longblaster`           |`220`                 |バイト専用 ロングブラスター<br>Range Blaster             |               |
|`rapid`                 |`240`                 |バイト専用 ラピッドブラスター<br>Rapid Blaster           |               |
|`rapid_elite`           |`250`                 |バイト専用 Rブラスターエリート<br>Rapid Blaster Pro      |               |
|`sharp`                 |`20`                  |バイト専用 シャープマーカー<br>Splash-o-matic            |               |
|`sshooter`              |`40`                  |バイト専用 スプラシューター<br>Splattershot              |               |
|`wakaba`                |`10`                  |バイト専用 わかばシューター<br>Splattershot Jr.          |               |
|`prime`                 |`70`                  |バイト専用 プライムシューター<br>Splattershot Pro        |               |
|`bold`                  |`0`                   |バイト専用 ボールドマーカー<br>Sploosh-o-matic           |               |
|`bottlegeyser`          |`400`                 |バイト専用 ボトルガイザー<br>Squeezer                    |               |
|`carbon`                |`1000`                |バイト専用 カーボンローラー<br>Carbon Roller             |               |
|`dynamo`                |`1020`                |バイト専用 ダイナモローラー<br>Dynamo Roller             |               |
|`variableroller`        |`1030`                |バイト専用 ヴァリアブルローラー<br>Flingza Roller        |               |
|`pablo`                 |`1100`                |バイト専用 パブロ<br>Inkbrush                            |               |
|`hokusai`               |`1110`                |バイト専用 ホクサイ<br>Octobrush                         |               |
|`splatroller`           |`1010`                |バイト専用 スプラローラー<br>Splat Roller                |               |
|`bamboo14mk1`           |`2050`                |バイト専用 14式竹筒銃・甲<br>Bamboozler 14 Mk I          |               |
|`squiclean_a`           |`2000`                |バイト専用 スクイックリンα<br>Classic Squiffer          |               |
|`liter4k`               |`2030`                |バイト専用 リッター4K<br>E-liter 4K                      |               |
|`soytuber`              |`2060`                |バイト専用 ソイチューバー<br>Goo Tuber                   |               |
|`splatcharger`          |`2010`                |バイト専用 スプラチャージャー<br>Splat Charger           |               |
|`furo`                  |`3030`                |バイト専用 オーバーフロッシャー<br>Bloblobber            |               |
|`explosher`             |`3040`                |バイト専用 エクスプロッシャー<br>Explosher               |               |
|`bucketslosher`         |`3000`                |バイト専用 バケットスロッシャー<br>Slosher               |               |
|`screwslosher`          |`3020`                |バイト専用 スクリュースロッシャー<br>Sloshing Machine    |               |
|`hissen`                |`3010`                |バイト専用 ヒッセン<br>Tri-Slosher                       |               |
|`kugelschreiber`        |`4030`                |バイト専用 クーゲルシュライバー<br>Ballpoint Splatling   |               |
|`barrelspinner`         |`4010`                |バイト専用 バレルスピナー<br>Heavy Splatling             |               |
|`hydra`                 |`4020`                |バイト専用 ハイドラント<br>Hydra Splatling               |               |
|`splatspinner`          |`4000`                |バイト専用 スプラスピナー<br>Mini Splatling              |               |
|`nautilus47`            |`4040`                |バイト専用 ノーチラス47<br>Nautilus 47                   |               |
|`sputtery`              |`5000`                |バイト専用 スパッタリー<br>Dapple Dualies                |               |
|`quadhopper_black`      |`5040`                |バイト専用 クアッドホッパーブラック<br>Dark Tetra Dualies|               |
|`dualsweeper`           |`5030`                |バイト専用 デュアルスイーパー<br>Dualie Squelchers       |               |
|`kelvin525`             |`5020`                |バイト専用 ケルビン525<br>Glooga Dualies                 |               |
|`maneuver`              |`5010`                |バイト専用 スプラマニューバー<br>Splat Dualies           |               |
|`parashelter`           |`6000`                |バイト専用 パラシェルター<br>Splat Brella                |               |
|`campingshelter`        |`6010`                |バイト専用 キャンピングシェルター<br>Tenta Brella        |               |
|`spygadget`             |`6020`                |バイト専用 スパイガジェット<br>Undercover Brella         |               |
<!--endreplace-->


Special Weapon スペシャル
-------------------------

<!--replace:special-->
|指定文字列<br>Key String|イカリング<br>SplatNet|名前<br>Name                                     |備考<br>Remarks|
|------------------------|----------------------|-------------------------------------------------|---------------|
|`jetpack`               |`8`                   |ジェットパック<br>Inkjet                         |               |
|`chakuchi`              |`9`                   |スーパーチャクチ<br>Splashdown                   |               |
|`pitcher`               |`2`                   |スプラッシュボムピッチャー<br>Splat-Bomb Launcher|               |
|`presser`               |`7`                   |ハイパープレッサー<br>Sting Ray                  |               |
<!--endreplace-->
