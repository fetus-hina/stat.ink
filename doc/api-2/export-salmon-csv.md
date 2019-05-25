Exported Salmon Run CSV
=======================

- "Rotation": Play window. Have some shifts.
- "Shift": The play. 1-3 waves.

Schema
------

- `#` から始まる行はコメントです。<br>If line starts with `#`, the line is a comment.
- ファイルは UTF-8 でエンコードされています。<br>The file encoded by UTF-8.
- ファイルの先頭にはいわゆる BOM が挿入されています。<br>There is a "BOM" character at beginning of the file.

| # | column| column name | type | example | meaning |
|---|-------|-------------|------|---------|---------|
|   0 |  A | `statink_id` | integer | `73172` | Our internal ID<br>stat.inkの内部番号 |
|   1 |  B | `rotation_period` | integer | `216441` | Same value if same rotation.<br>同じスケジュールなら同じ値<br>Internal: `rotation_start` = unixtime(`value` * 7200) |
|   2 |  C | `shift_start` | integer | `1558452561` | UNIX time. This shift (job) started at this time. |
|   3 |  D | `shift_start` | datetime | `2019-05-22T00:29:21+09:00` | ISO 8601 formatted. |
|   4 |  E | `splatnet_number` | integer | `4589` | SplatNet Internal ID<br>イカリングの内部番号 |
|   5 |  F | `stage` | key | `tokishirazu` | `dam`: Grounds / シェケナダム<br>`donburako`: Bay / ドン・ブラコ<br>`shaketoba`: Outpost / シャケト場<br>`tokishirazu`: Smokeyard / トキシラズ<br>`polaris`: Polaris / ポラリス |
|   6 |  G | `stage` | localized | `トキシラズいぶし工房` | Localized |
|   7 |  H | `clear_wave` | integer | `3` | `0`: Failed in Wave 1<br>`1`: Failed in Wave 2<br>`2`: Failed in Wave 3<br>`3`: Cleared the shift |
|   8 |  I | `fail_reason` | key | | `wipe_out`: Wiped / ゼンメツ<br>`time_limit`: Time is up / 時間切れ |
|   9 |  J | `fail_reason` | localized | | Localized |
|  10 |  K | `hazard_level` | decimal (3.1) | `200.0` | `0.0`..`200.0`, `200.0`=Hazard Level Max |
|  11 |  L | `title_before` | key | `profreshional` | `apprentice`: Apprentice / かけだし<br>`part_timer`: Part-Timer / はんにんまえ<br>`go_getter`: Go-Getter / いちにんまえ<br>`overachiever`: Overachiever / じゅくれん<br>`profreshional`: Profreshional / たつじん |
|  12 |  M | `title_before` | localized | `たつじん` | Localized |
|  13 |  N | `title_before` | integer | `440` | `0`..`999` |
|  14 |  O | `title_after` | key | `profreshional` | |
|  15 |  P | `title_after` | localized | `たつじん` | |    
|  16 |  Q | `title_after` | integer | `460` | |
|  17 |  R | `w1_event` | key | | `cohock_charge`: Cohock Charge / ドスコイ大量発生<br>`fog`: Fog / 霧<br>`goldie_seeking`: Goldie Seeking / キンシャケ探し<br>`griller`: The Griller / グリル発進<br>`mothership`: The Mothership / ハコビヤ襲来<br>`rush`: Rush / ラッシュ（ヒカリバエ） |
|  18 |  S | `w1_event` | localized | | Localized |
|  19 |  T | `w1_water` | key | `normal` | `low`, `normal`, `high` |
|  20 |  U | `w1_water` | localized | `普通` | Localized |
|  21 |  V | `w1_quota` | integer | `21` | Golden Eggs quota for Wave 1<br>Wave 1のノルマ |
|  22 |  W | `w1_delivers` | integer | `57` | How many delivered Golden Eggs<br>金イクラの納品数 |
|  23 |  X | `w1_appearances` | integer | `36` | How many appeared Golden Eggs<br>金イクラの出現数 |
|  24 |  Y | `w1_pwr_eggs` | integer | `996` | How many collected Power Eggs<br>イクラの収集数 |
|  25 |  Z | `w2_event` | key | | |
|  26 | AA | `w2_event` | localized | | |
|  27 | AB | `w2_water` | key | `normal` | |
|  28 | AC | `w2_water` | localized | `普通` | |    
|  29 | AD | `w2_quota` | integer | `23` | |  
|  30 | AE | `w2_delivers` | integer | `54` | |  
|  31 | AF | `w2_appearances` | integer | `28` | |  
|  32 | AG | `w2_pwr_eggs` | integer | `813` | | 
|  33 | AH | `w3_event` | key | | |
|  34 | AI | `w3_event` | localized | | |      
|  35 | AJ | `w3_water` | key | `low` | |
|  36 | AK | `w3_water` | localized | `干潮` | |    
|  37 | AL | `w3_quota` | integer | `25` | |  
|  38 | AM | `w3_delivers` | integer | `66` | |  
|  39 | AN | `w3_appearances` | integer | `34` | |  
|  40 | AO | `w3_pwr_eggs` | integer | `1269` | |    
|  41 | AP | `player_id` | string | `3f6fb10a91b0c551` | SplatNet Internal ID<br>イカリングの内部ID |
|  42 | AQ | `player_name` | string | `ひな` | In-game name |
|  43 | AR | `player_w1_weapon` | key | `bottlegeyser` | [See this](https://github.com/fetus-hina/stat.ink/blob/master/doc/api-2/post-salmon.md#main-weapon) |
|  44 | AS | `player_w1_weapon` | localized | `ボトルガイザー` | Localized |
|  45 | AT | `player_w2_weapon` | key | `maneuver` | |
|  46 | AU | `player_w2_weapon` | localized | `スプラマニューバー` | |
|  47 | AV | `player_w3_weapon` | key | `wakaba` | |
|  48 | AW | `player_w3_weapon` | localized | `わかばシューター` | |
|  49 | AX | `player_special` | key | `presser` | [See this](https://github.com/fetus-hina/stat.ink/blob/master/doc/api-2/post-salmon.md#special-weapon) |
|  50 | AY | `player_special` | localized | `ハイパープレッサー` | |
|  51 | AZ | `player_w1_sp_use` | integer | `0` | How many times used special weapon in Wave 1 |
|  52 | BA | `player_w2_sp_use` | integer | `0` | |
|  53 | BB | `player_w3_sp_use` | integer | `2` | |
|  54 | BC | `player_rescues` | integer | `1` | "Helps"; Number of times the player helped other players |
|  55 | BD | `player_rescued` | integer | `1` | "Deaths"; Number of times the player helped *by* other players |
|  56 | BE | `player_golden_eggs` | integer | `26` | How many delivered Golden Eggs |
|  57 | BF | `player_power_eggs` | integer | `806` | How many collected Power Eggs |
|  58 | BG | `mate1_id` | string | | |
|  59 | BH | `mate1_name` | string | | |
|  60 | BI | `mate1_w1_weapon` | key | | |
|  61 | BJ | `mate1_w1_weapon` | localized | | |
|  62 | BK | `mate1_w2_weapon` | key | | |
|  63 | BL | `mate1_w2_weapon` | localized | | |
|  64 | BM | `mate1_w3_weapon` | key | | |
|  65 | BN | `mate1_w3_weapon` | localized | | |
|  66 | BO | `mate1_special` | key | | |
|  67 | BP | `mate1_special` | localized | | |
|  68 | BQ | `mate1_w1_sp_use` | integer | | |
|  69 | BR | `mate1_w2_sp_use` | integer | | |
|  70 | BS | `mate1_w3_sp_use` | integer | | |
|  71 | BT | `mate1_rescues` | integer | | |
|  72 | BU | `mate1_rescued` | integer | | |
|  73 | BV | `mate1_golden_eggs` | integer | | |
|  74 | BW | `mate1_power_eggs` | integer | | |
|  75 | BX | `mate2_id` | string | | |
|  76 | BY | `mate2_name` | string | | |
|  77 | BZ | `mate2_w1_weapon` | key | | |
|  78 | CA | `mate2_w1_weapon` | localized | | |
|  79 | CB | `mate2_w2_weapon` | key | | |
|  80 | CC | `mate2_w2_weapon` | localized | | |
|  81 | CD | `mate2_w3_weapon` | key | | |
|  82 | CE | `mate2_w3_weapon` | localized | | |
|  83 | CF | `mate2_special` | key | | |
|  84 | CG | `mate2_special` | localized | | |
|  85 | CH | `mate2_w1_sp_use` | integer | | |
|  86 | CI | `mate2_w2_sp_use` | integer | | |
|  87 | CJ | `mate2_w3_sp_use` | integer | | |
|  88 | CK | `mate2_rescues` | integer | | |
|  89 | CL | `mate2_rescued` | integer | | |
|  90 | CM | `mate2_golden_eggs` | integer | | |
|  91 | CN | `mate2_power_eggs` | integer | | |
|  92 | CO | `mate3_id` | string | | |
|  93 | CP | `mate3_name` | string | | |
|  94 | CQ | `mate3_w1_weapon` | key | | |
|  95 | CR | `mate3_w1_weapon` | localized | | |
|  96 | CS | `mate3_w2_weapon` | key | | |
|  97 | CT | `mate3_w2_weapon` | localized | | |
|  98 | CU | `mate3_w3_weapon` | key | | |
|  99 | CV | `mate3_w3_weapon` | localized | | |
| 100 | CW | `mate3_special` | key | | |
| 101 | CX | `mate3_special` | localized | | |
| 102 | CY | `mate3_w1_sp_use` | integer | | |
| 103 | CZ | `mate3_w2_sp_use` | integer | | |
| 104 | DA | `mate3_w3_sp_use` | integer | | |
| 105 | DB | `mate3_rescues` | integer | | |
| 106 | DC | `mate3_rescued` | integer | | |
| 107 | DD | `mate3_golden_eggs` | integer | | |
| 108 | DE | `mate3_power_eggs` | integer | | |
| 109 | DF | `drizzler_appearances` | integer | | Number of Drizzler appearances<br>コウモリの出現数 |
| 110 | DG | `drizzler_player_kills` | integer | | Number of killed by the player |
| 111 | DH | `drizzler_mate1_kills` | integer | | Number of killed by teammate 1 |
| 112 | DI | `drizzler_mate2_kills` | integer | | Number of killed by teammate 2 |
| 113 | DJ | `drizzler_mate3_kills` | integer | | Number of killed by teammate 3 |
| 114 | DK | `flyfish_appearances` | integer | | Number of Flyfish appearances<br>カタパッドの出現数 |
| 115 | DL | `flyfish_player_kills` | integer | | |
| 116 | DM | `flyfish_mate1_kills` | integer | | |
| 117 | DN | `flyfish_mate2_kills` | integer | | |
| 118 | DO | `flyfish_mate3_kills` | integer | | |
| 119 | DP | `goldie_appearances` | integer | | Number of Goldie appearances<br>キンシャケの出現数 |
| 120 | DQ | `goldie_player_kills` | integer | | |
| 121 | DR | `goldie_mate1_kills` | integer | | |
| 122 | DS | `goldie_mate2_kills` | integer | | |
| 123 | DT | `goldie_mate3_kills` | integer | | |
| 124 | DU | `griller_appearances` | integer | | Number of Griller appearances<br>グリルの出現数 |
| 125 | DV | `griller_player_kills` | integer | | |
| 126 | DW | `griller_mate1_kills` | integer | | |
| 127 | DX | `griller_mate2_kills` | integer | | |
| 128 | DY | `griller_mate3_kills` | integer | | |
| 129 | DZ | `maws_appearances` | integer | | Number of Maws appearances<br>モグラの出現数 |
| 130 | EA | `maws_player_kills` | integer | | |
| 131 | EB | `maws_mate1_kills` | integer | | |
| 132 | EC | `maws_mate2_kills` | integer | | |
| 133 | ED | `maws_mate3_kills` | integer | | |
| 134 | EE | `scrapper_appearances` | integer | | Number of Scrapper appearances<br>テッパンの出現数 |
| 135 | EF | `scrapper_player_kills` | integer | | |
| 136 | EG | `scrapper_mate1_kills` | integer | | |
| 137 | EH | `scrapper_mate2_kills` | integer | | |
| 138 | EI | `scrapper_mate3_kills` | integer | | |
| 139 | EJ | `steel_eel_appearances` | integer | | Number of Steel Eel appearances<br>ヘビの出現数 |
| 140 | EK | `steel_eel_player_kills` | integer | | |
| 141 | EL | `steel_eel_mate1_kills` | integer | | |
| 142 | EM | `steel_eel_mate2_kills` | integer | | |
| 143 | EN | `steel_eel_mate3_kills` | integer | | |
| 144 | EO | `steelhead_appearances` | integer | | Number of Steelhead appearances<br>バクダンの出現数 |
| 145 | EP | `steelhead_player_kills` | integer | | |
| 146 | EQ | `steelhead_mate1_kills` | integer | | |
| 147 | ER | `steelhead_mate2_kills` | integer | | |
| 148 | ES | `steelhead_mate3_kills` | integer | | |
| 149 | ET | `stinger_appearances` | integer | | Number of Stinger appearances<br>タワーの出現数 |
| 150 | EU | `stinger_player_kills` | integer | | |
| 151 | EV | `stinger_mate1_kills` | integer | | |
| 152 | EW | `stinger_mate2_kills` | integer | | |
| 153 | EX | `stinger_mate3_kills` | integer | | |

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
