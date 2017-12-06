`GET /api/v2/gear`
==================

| | |
|-|-|
|URL|`https://stat.ink/api/v2/gear`|
|Return-Type|`application/json`|
|認証|なし|

[CSV version](get-gear-csv.md)

ギアの一覧をJSON形式、[`gear`構造体](struct/gear.md)の配列で返します。
各ブキの`key`が他のAPIで利用するときの値です。

出現順に規定はありません。（利用者側で適切に並び替えてください）

このAPIが返すのと同等のページがあります。

  - [アタマ](https://stat.ink/api-info/gear2-headgear)
  - [フク](https://stat.ink/api-info/gear2-clothing)
  - [クツ](https://stat.ink/api-info/gear2-shoes)


クエリパラメータ (Query Parameters)
-----------------------------------

|パラメータ名<br>Param. Name|型<br>Type|例<br>Example|内容|
|---------------------------|----------|-------------|----|
|`type`|string (`headgear`, `clothing` or `shoes`)|`headgear`|ギアの種類を指定します|
|`brand`|string|`krak_on`|ギアのブランドを指定します|
|`ability`|string|`special_power_up`|ギア標準のメインギアパワーを指定します|


出力例 (Output Example)
-----------------------

```js
[  
  {  
    "key":"18k_aviators",
    "type":{  
      "key":"headgear",
      "name":{  
        "ja_JP":"アタマ",
        "en_US":"Headgear",
        "en_GB":"Headgear",
        "es_ES":"Accesorios",
        "es_MX":"Accesorios",
        "fr_FR":"Headgear",
        "fr_CA":"Headgear",
        "de_DE":"Headgear",
        "it_IT":"Headgear",
        "nl_NL":"Headgear",
        "ru_RU":"Headgear"
      }
    },
    "brand":{  
      "key":"rockenberg",
      "name":{  
        "ja_JP":"ロッケンベルグ",
        "en_US":"Rockenberg",
        "en_GB":"Rockenberg",
        "es_ES":"Rockenberg",
        "es_MX":"Rockenberg",
        "fr_FR":"Iormungand",
        "fr_CA":"Iormungand",
        "de_DE":"Rockberg",
        "it_IT":"Rockenburg",
        "nl_NL":"Rockenberg",
        "ru_RU":"Rockenberg"
      },
      "strength":{  
        "key":"run_speed_up",
        "name":{  
          "ja_JP":"ヒト移動速度アップ",
          "en_US":"Run Speed Up",
          "en_GB":"Run Speed Up",
          "es_ES":"Supercarrera",
          "es_MX":"Carrera acelerada",
          "fr_FR":"Course à pied",
          "fr_CA":"Course à pied",
          "de_DE":"Lauftempo +",
          "it_IT":"Velocità +",
          "nl_NL":"Run Speed Up",
          "ru_RU":"Спринтер"
        }
      },
      "weakness":{  
        "key":"swim_speed_up",
        "name":{  
          "ja_JP":"イカダッシュ速度アップ",
          "en_US":"Swim Speed Up",
          "en_GB":"Swim Speed Up",
          "es_ES":"Superbuceo",
          "es_MX":"Nado acelerado",
          "fr_FR":"Turbo-calamar",
          "fr_CA":"Turbo-calmar",
          "de_DE":"Schwimmtempo +",
          "it_IT":"Velocità nuoto +",
          "nl_NL":"Swim Speed Up",
          "ru_RU":"Плавунец"
        }
      }
    },
    "name":{  
      "ja_JP":"タレサン18K",
      "en_US":"18K Aviators",
      "en_GB":"18K Aviators",
      "es_ES":"18K Aviators",
      "es_MX":"18K Aviators",
      "fr_FR":"18K Aviators",
      "fr_CA":"18K Aviators",
      "de_DE":"18K Aviators",
      "it_IT":"18K Aviators",
      "nl_NL":"18K Aviators",
      "ru_RU":"18K Aviators"
    },
    "primary_ability":{  
      "key":"last_ditch_effort",
      "name":{  
        "ja_JP":"ラストスパート",
        "en_US":"Last-Ditch Effort",
        "en_GB":"Last-Ditch Effort",
        "es_ES":"Sprint Final",
        "es_MX":"Último recurso",
        "fr_FR":"Ultime sursaut",
        "fr_CA":"Ultime sursaut",
        "de_DE":"Endspurt",
        "it_IT":"Slash finale",
        "nl_NL":"Last-Ditch Effort",
        "ru_RU":"Финишный спурт"
      }
    }
  },
  // ...
]
```

----

[![CC-BY 4.0](https://stat.ink/static-assets/cc/cc-by.svg)](http://creativecommons.org/licenses/by/4.0/deed.ja)

この文章は[Creative Commons - 表示 4.0 国際](http://creativecommons.org/licenses/by/4.0/deed.ja)の下にライセンスされています。
