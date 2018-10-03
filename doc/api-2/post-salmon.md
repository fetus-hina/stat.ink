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


`title`
-------

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
