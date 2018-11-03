`POST /api/v2/salmon`
===================

| | |
|-|-|
|Verb|`POST`|
|URL|`https://stat.ink/api/v2/salmon`|
|Return-Type|Succeed: `201 Created` (no body)<br>Failed: HTTP Error 4xx or 5xx with `application/json` or `text/html` data<br>Already exists: `302 Found` (no body)|
|Auth|[Needed](authorization.md)|

Post a salmon run shift to stat.ink.


Request Structure
-----------------

All parameters are optional.

|パラメータ<br>Parameter|型<br>Type|値<br>Value| |
|-----------------------|----------|-----------|-|
|`uuid`|UUID or string|See below|UUID is recommended|
|`splatnet_number`|integer|1-|SplatNet `job_id`|
|`stage`|key string|e.g. `dam`|[§Stage](#stage)|
|`clear_waves`|integer|0-3|`3` if cleared. `0` if failed in wave 1|
|`fail_reason`|key string| |`null` if cleared (or unknown reason)<br>`wipe_out`: Dead all players<br>`time_limit`: Time was up|
|`title`|key string|e.g. `profreshional`|[§Title](#title), befor the work|
|`title_exp`|integer|0-999|Profreshional "40"/999, before the work|
|`title_after`|key string| |After the work|
|`title_exp_after`|integer|0-999|After the work|
|`danger_rate`|number|0.0-200.0|Hazard level|
|`boss_appearances`|key-value|See below|Number of bosses|
|`waves`|array of `wave` structure|See below|Wave data, 1-3 elements|
|`my_data`|`player` structure|See below|Player's data|
|`teammates`|array of `player` structure|See below|Players (except "my_data") data, typically 3 elements|
|`shift_start_at`|integer|UNIX time|Play window started at|
|`start_at`|integer|UNIX time|This work started at|
|`end_at`|integer|UNIX time|(May not be presented in SplatNet JSON)|
|`note`|string| |Note (public)|
|`private_note`|string| |Note (private)|
|`link_url`|URL| | |
|`automated`|boolean|`yes` or `no`|`yes` if by automated program.<br>`no` if manual input.|
|`agent`|string (≤64 chars)|e.g. `my awesome client`|User-agent name.|
|`agent_version`|string (≤ 255 chars)|e.g. `1.0.0 (Windows 10)`|User-agent version, can include user environment such as OS|


Full example:

```js
{
  "uuid": "6ad938a0-64a0-5edb-a224-2a63f7237fef",
  "splatnet_number": 3550,
  "stage": "dam",
  "clear_waves": 3, // cleared
  "fail_reason": null, // cleared
  "title_after": "profreshional",
  "title_exp_after": 400,
  "danger_rate": 165.6,
  "boss_appearances": {
    "goldie": 14,
    "steelhead": 3,
    "stinger": 2,
    "griller": 0,
    "flyfish": 3,
    "scrapper": 3,
    "steel_eel": 2,
    "maws": 5,
    "drizzler": 2
  },
  "waves": [
    { // wave 1
      "known_occurrence": "mothership",
      "water_level": "normal",
      "golden_egg_quota": 18,
      "golden_egg_appearances": 27,
      "golden_egg_delivered": 23,
      "power_egg_collected": 695
    },
    { // wave 2
      "known_occurrence": "rush",
      "water_level": "normal",
      "golden_egg_quota": 19,
      "golden_egg_appearances": 36,
      "golden_egg_delivered": 20,
      "power_egg_collected": 1772
    },
    { // wave 3
      "known_occurrence": null, // wave-level only
      "water_level": "high",
      "golden_egg_quota": 21,
      "golden_egg_appearances": 36,
      "golden_egg_delivered": 24,
      "power_egg_collected": 887
    }
  ],
  "my_data": {
    "splatnet_id": "3f6fb10a91b0c551",
    "name": "あいざわひな(18)",
    "special": "jetpack",
    "rescue": 3,
    "death": 3,
    "golden_egg_delivered": 14,
    "power_egg_collected": 826,
    "species": "inkling",
    "gender": "girl",
    "special_uses": [
      0, // wave 1
      1, // wave 2
      1  // wave 3
    ],
    "weapons": [
      "nzap85", // wave 1
      "sputtery", // wave 2
      "campingshelter" // wave 3
    ],
    "boss_kills": {
      "goldie": 5,
      "maws": 2
    }
  },
  "teammates": [
    { // player 1
      "splatnet_id": "532ce9609b00ebd9",
      "name": "Player 7e",
      "special": "pitcher",
      "rescue": 7,
      "death": 5,
      "golden_egg_delivered": 19,
      "power_egg_collected": 823,
      "species": null,
      "gender": null,
      "special_uses": [
        0,
        1,
        1
      ],
      "weapons": [
        "sputtery",
        "campingshelter",
        "jetsweeper"
      ],
      "boss_kills": {
        "drizzler": 1,
        "steel_eel": 1
      }
    },
    { // player 2
      "splatnet_id": "52c31db71413b8f1",
      "name": "Player 66",
      "special": "presser",
      "rescue": 5,
      "death": 3,
      "golden_egg_delivered": 17,
      "power_egg_collected": 849,
      "species": null,
      "gender": null,
      "special_uses": [
        1,
        0,
        1
      ],
      "weapons": [
        "campingshelter",
        "jetsweeper",
        "nzap85"
      ],
      "boss_kills": {
        "goldie": 5,
        "scrapper": 2,
        "steelhead": 1,
        "stinger": 1
      }
    },
    { // player 3
      "splatnet_id": "d7afd6496355f18a",
      "name": "Player 84",
      "special": "chakuchi",
      "rescue": 2,
      "death": 6,
      "golden_egg_delivered": 18,
      "power_egg_collected": 856,
      "species": null,
      "gender": null,
      "special_uses": [
        0,
        0,
        1
      ],
      "weapons": [
        "jetsweeper",
        "nzap85",
        "sputtery"
      ],
      "boss_kills": {
        "goldie": 2,
        "maws": 1,
        "scrapper": 1,
        "steel_eel": 1,
        "stinger": 1
      }
    }
  ],
  "shift_start_at": 1540339200,
  "start_at": 1540402219,
  "automated": "no",
  "agent": "stat.ink development",
  "agent_version": "9a0560cf"
}
```

`uuid`
------

Client application should specify a UUID to detect duplicated "work".

- SplatNet 2-based Application

  - Generate a UUID version 5 with namespace `418fe150-cb33-11e8-8816-d050998473ba`.

    Use `splatnet_number` @ `principal_id`. (Example: `42@abc123`)<br>
    `uuid_v5("418fe150-cb33-11e8-8816-d050998473ba", sprintf("%d@%s", number, principal_id))`

- Standalone Application

  - Nothing send
  - Generate a UUID version 4
  - Generate a UUID version 3 or 5 with your own namespace


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

If not appearances the boss, you can send `0` or omit the boss.

See also: [§Boss](#boss)

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
|`known_occurrence`|key string|e.g. `fog`|[§Known Occurrence](#known-occurrence). `null` or empty-string if "standard" work|
|`water_level`|key string|e.g. `high`|[§Water Level](#water-level)|
|`golden_egg_quota`|integer|1-25|Players should deliver golden eggs|
|`golden_egg_appearances`|integer|0-|Golden Egg appearances, "pops"|
|`golden_egg_delivered`|integer|0-|Golden Egg delivered, "collected"|
|`power_egg_collected`|integer|0-|Power Egg collected|


`player` structure
------------------

|パラメータ<br>Parameter|型<br>Type|値<br>Value| |
|-|-|-|-|
|`splatnet_id`|string|≤ 16 chars|Principal-ID of SplatNet 2 (`pid`)|
|`name`|string|≤ 10 chars|Player's name|
|`special`|key string|e.g. `jetpack`|[§Special Weapon](#special-weapon)|
|`rescue`|integer|0-|How many rescued other players|
|`death`|integer|0-|How many dead|
|`golden_egg_delivered`|integer|0-|How many Golden Eggs delivered|
|`power_egg_collected`|integer|0-|How many Power Eggs collected|
|`species`|key string|`inkling` or `octoling`|`inkling`: Inklings<br>`octoling`: Octolings<br>Note: no "s" in key-strings|
|`gender`|key string|`boy` or `girl`|`boy`: Boy, Male<br>`girl`: Girl, Female<br>This value used for switching gender of title, like "Jefe/Jefa".|
|`special_uses`|array of integer|0-3 each|How many used special weapon for each wave<br>Example: `"special_uses": [0, 1, 2]`|
|`weapons`|array of key-string| |What weapon loaned for each wave<br>[§Main Weapon](#main-weapon)<br>Example: `"weapons": ["wakaba", "sshooter", "splatcharger"]`|
|`boss_kills`|key-value| |Number of bosses killed<br>[§Boss](#boss)|

See "full example" above.


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


Water Level
-----------

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


Main Weapon
-----------

<!--replace:weapon-->
|指定文字列<br>Key String|イカリング<br>SplatNet|ブキ<br>Weapon Name                                      |備考<br>Remarks|
|------------------------|----------------------|---------------------------------------------------------|---------------|
|`kuma_blaster`          |`20000`               |バイト専用 クマサン印のブラスター<br>Grizzco Blaster     |               |
|`kuma_brella`           |`20010`               |バイト専用 クマサン印のシェルター<br>Grizzco Brella      |               |
|`kuma_charger`          |`20020`               |バイト専用 クマサン印のチャージャー<br>Grizzco Charger   |               |
|`kuma_slosher`          |`20030`               |バイト専用 クマサン印のスロッシャー<br>Grizzco Slosher   |               |
|`52gal`                 |`50`                  |バイト専用 .52ガロン<br>.52 Gal                          |               |
|`96gal`                 |`80`                  |バイト専用 .96ガロン<br>.96 Gal                          |               |
|`promodeler_mg`         |`30`                  |バイト専用 プロモデラーMG<br>Aerospray MG                |               |
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
|`liter4k_scope`         |`2040`                |バイト専用 4Kスコープ<br>E-liter 4K Scope                |               |
|`soytuber`              |`2060`                |バイト専用 ソイチューバー<br>Goo Tuber                   |               |
|`splatcharger`          |`2010`                |バイト専用 スプラチャージャー<br>Splat Charger           |               |
|`splatscope`            |`2020`                |バイト専用 スプラスコープ<br>Splatterscope               |               |
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


Special Weapon
--------------

<!--replace:special-->
|指定文字列<br>Key String|イカリング<br>SplatNet|名前<br>Name                                     |備考<br>Remarks|
|------------------------|----------------------|-------------------------------------------------|---------------|
|`jetpack`               |`8`                   |ジェットパック<br>Inkjet                         |               |
|`chakuchi`              |`9`                   |スーパーチャクチ<br>Splashdown                   |               |
|`pitcher`               |`2`                   |スプラッシュボムピッチャー<br>Splat-Bomb Launcher|               |
|`presser`               |`7`                   |ハイパープレッサー<br>Sting Ray                  |               |
<!--endreplace-->
