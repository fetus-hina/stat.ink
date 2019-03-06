<?php
declare(strict_types=1);

use Symfony\Component\Yaml\Yaml;
use app\models\GameMode;
use app\models\Map;
use app\models\Rule;
use app\models\Special;
use app\models\Subweapon;
use app\models\Weapon;
use app\models\WeaponType;
use yii\helpers\Html;

$_langs = [
  'ja_JP' => Yii::t('app-apidoc1', 'Japanese name'),
  'en_GB' => Yii::t('app-apidoc1', 'English name (EU)'),
  'en_US' => Yii::t('app-apidoc1', 'English name (NA)'),
  'es_ES' => Yii::t('app-apidoc1', 'Spanish name (EU)'),
  'es_MX' => Yii::t('app-apidoc1', 'Spanish name (Latin America)'),
  'fr_CA' => Yii::t('app-apidoc1', 'French name (NA)'),
  'fr_FR' => Yii::t('app-apidoc1', 'French name (EU)'),
];

$_name = function (int $indent, string $category, string $text, array $params = []) use ($_langs): string {
  $_indent = str_repeat(' ', $indent);
  return ltrim(implode("\n", array_map(
    function (string $langCode) use ($_indent, $category, $text, $params): string {
      return $_indent . Yaml::dump($langCode) . ': ' . Yaml::dump(Yii::t($category, $text, $params, $langCode));
    },
    array_keys($_langs)
  )));
};

$_datetime = function (int $indent, int $unixTime): string {
  $_indent = str_repeat(' ', $indent);
  $time = (new DateTimeImmutable())
    ->setTimezone(new DateTimeZone('Etc/UTC'))
    ->setTimestamp($unixTime);
  return implode("\n" . $_indent, [
    'time: ' . Yaml::dump($time->getTimestamp()),
    'iso8601: ' . Yaml::dump($time->format(DateTime::ATOM)),
  ]);
};

$_keyValueTable = function (int $indent, string $name, string $msgCategory, array $list): string {
  $_indent = str_repeat(' ', $indent);
  return implode("\n$_indent", array_merge(
    [
      sprintf('| `key` | %s |', Html::encode($name)),
      '|-|-|',
    ],
    array_map(
      function (array $item) use ($msgCategory): string {
        return vsprintf('| `%s` | %s |', [
          Html::encode($item['key']),
          Html::encode(Yii::t($msgCategory, $item['name'])),
        ]);
      },
      $list
    )
  ));
};

$_modes = GameMode::find()
  ->orderBy(['id' => SORT_ASC])
  ->with([
    'rules' => function ($query) {
      $query->orderBy(['key' => SORT_ASC]);
    },
  ])
  ->asArray()
  ->all();

$_rules = Rule::find()
  ->orderBy(['key' => SORT_ASC])
  ->asArray()
  ->all();

$_maps = Map::find()
  ->orderBy(['key' => SORT_ASC])
  ->asArray()
  ->all();

$_weapons = Weapon::find()
  ->with([
    'type' => function ($query) {
      $query->orderBy(false);
    },
  ])
  ->orderBy(['key' => SORT_ASC])
  ->asArray()
  ->all();

$_weaponTypes = WeaponType::find()
  ->orderBy(['key' => SORT_ASC])
  ->asArray()
  ->all();

$_subWeapons = Subweapon::find()
  ->orderBy(['key' => SORT_ASC])
  ->asArray()
  ->all();

$_specials = Special::find()
  ->orderBy(['key' => SORT_ASC])
  ->asArray()
  ->all();
?>
openapi: 3.0.2

info:
  title: <?= Yaml::dump(Yii::t('app-apidoc1', 'stat.ink API for Splatoon 1')) . "\n" ?>
  version: 1.0.0
  contact:
    name: stat.ink
    url: https://github.com/fetus-hina/stat.ink
  license:
    name: CC-BY 4.0
    url: https://creativecommons.org/licenses/by/4.0/deed.<?= $langCode . "\n" ?>

servers:
  - url: https://stat.ink
    description: production

paths:
  /api/v1/rule:
    get:
      operationId: getRule
      summary: <?= Yaml::dump(Yii::t('app-apidoc1', 'Get game modes')) . "\n" ?>
      description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Returns an array of game modes information')) . "\n" ?>
      tags:
        - general
      responses:
        '200':
          description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Successful')) . "\n" ?>
          content:
            application/json:
              schema:
                type: array
                items:
                  $ref: '#/components/schemas/Rule'
                example:
<?php foreach ($_modes as $_mode) { ?>
<?php foreach ($_mode['rules'] as $_rule) { ?>
                  - key: <?= Yaml::dump($_rule['key']) . "\n" ?>
                    name:
                      <?= $_name(22, 'app-rule', $_rule['name']) . "\n" ?>
                    mode:
                      key: <?= Yaml::dump($_mode['key']) . "\n" ?>
                      name:
                        <?= $_name(24, 'app-rule', $_mode['name']) . "\n" ?>
<?php } ?>
<?php } ?>

  /api/v1/map:
    get:
      operationId: getMap
      summary: <?= Yaml::dump(Yii::t('app-apidoc1', 'Get stages')) . "\n" ?>
      description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Returns an array of stages information')) . "\n" ?>
      tags:
        - general
      responses:
        '200':
          description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Successful')) . "\n" ?>
          content:
            application/json:
              schema:
                type: array
                items:
                  $ref: '#/components/schemas/Map'
                example:
<?php foreach ($_maps as $_) { ?>
                  - key: <?= Yaml::dump($_['key']) . "\n" ?>
                    name:
                      <?= $_name(22, 'app-map', $_['name']) . "\n" ?>
                    area: <?= Yaml::dump($_['area']) . "\n" ?>
                    release_at:
                      <?= $_datetime(22, strtotime($_['release_at'])) . "\n" ?>
<?php } ?>

  /api/v1/weapon:
    get:
      operationId: getWeapon
      summary: <?= Yaml::dump(Yii::t('app-apidoc1', 'Get weapons')) . "\n" ?>
      description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Returns an array of weapons information')) . "\n" ?>
      tags:
        - general
      parameters:
        - name: weapon
          in: query
          description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Filter by key-string of the weapon')) . "\n" ?>
          schema:
            type: string
        - name: type
          in: query
          description: |
            <?= Html::encode(Yii::t('app-apidoc1', 'Filter by key-string of weapon type')) . "  \n" ?>

            <?= $_keyValueTable(12, Yii::t('app-apidoc1', 'Weapon Type'), 'app-weapon', $_weaponTypes) . "\n" ?>
          schema:
            type: string
            enum:
<?php foreach ($_weaponTypes as $_) { ?>
              - <?= Yaml::dump($_['key']) . "\n" ?>
<?php } ?>
        - name: sub
          in: query
          description: |
            <?= Html::encode(Yii::t('app-apidoc1', 'Filter by key-string of sub weapon')) . "  \n" ?>

            <?= $_keyValueTable(12, Yii::t('app-apidoc1', 'Sub Weapon'), 'app-subweapon', $_subWeapons) . "\n" ?>
          schema:
            type: string
            enum:
<?php foreach ($_subWeapons as $_) { ?>
              - <?= Yaml::dump($_['key']) . "\n" ?>
<?php } ?>
        - name: special
          in: query
          description: |
            <?= Html::encode(Yii::t('app-apidoc1', 'Filter by key-string of special weapon')) . "  \n" ?>

            <?= $_keyValueTable(12, Yii::t('app-apidoc1', 'Special Weapon'), 'app-special', $_specials) . "\n" ?>
          schema:
            type: string
            enum:
<?php foreach ($_specials as $_) { ?>
              - <?= Yaml::dump($_['key']) . "\n" ?>
<?php } ?>
      responses:
        '200':
          description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Successful')) . "\n" ?>
          content:
            application/json:
              schema:
                type: array
                items:
                  $ref: '#/components/schemas/Weapon'
                example:
                  - key: wakaba
                    name:
                      <?= $_name(22, 'app-weapon', 'Splattershot Jr.') . "\n" ?>
                    sub:
                      key: splashbomb
                      name:
                        <?= $_name(24, 'app-subweapon', 'Splat Bomb') . "\n" ?>
                    special:
                      key: barrier
                      name:
                        <?= $_name(24, 'app-special', 'Bubbler') . "\n" ?>

components:
  schemas:
    Name:
      type: object
      description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Internationalized name')) . "\n" ?>
      properties:
<?php foreach ($_langs as $_langCode => $_langName) { ?>
        <?= Yaml::dump($_langCode) ?>:
          type: string
          description: <?= Yaml::dump($_langName) . "\n" ?>
<?php } ?>

    DateTime: &schemasDateTime
      type: object
      description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Date and time')) . "\n" ?>
      properties:
        time:
          type: number
          format: int64
          description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Date and time expressed in Unix Time')) . "\n" ?>
        iso8601:
          type: string
          format: date-time
          description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Date and time expressed in ISO-8601 format')) . "\n" ?>

    Rule:
      type: object
      description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Mode information')) . "\n" ?>
      properties:
        key:
          type: string
          pattern: ^[a-z0-9_]+$
          enum:
<?php foreach ($_rules as $_) { ?>
            - <?= Yaml::dump($_['key']) . "\n" ?>
<?php } ?>
          description: |
            <?= Yaml::dump(Yii::t('app-apidoc1', 'Identification string for use with other API')) . "  \n" ?>

            <?= $_keyValueTable(12, Yii::t('app-apidoc1', 'Mode Name'), 'app-rule', $_rules) . "\n" ?>
        mode:
          $ref: '#/components/schemas/Mode'
        name:
          $ref: '#/components/schemas/Name'

    Mode:
      type: object
      description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Lobby information')) . "\n" ?>
      properties:
        key:
          type: string
          pattern: ^[a-z0-9_]+$
          enum:
<?php foreach ($_modes as $_) { ?>
            - <?= Yaml::dump($_['key']) . "\n" ?>
<?php } ?>
          description: |
            <?= Yaml::dump(Yii::t('app-apidoc1', 'Identification string for use with other API')) . "  \n" ?>

            <?= $_keyValueTable(12, Yii::t('app-apidoc1', 'Lobby Name'), 'app-rule', $_modes) . "\n" ?>
        name:
          $ref: '#/components/schemas/Name'
      example:
        - key: regular
          name:
            <?= $_name(12, 'app-rule', 'Regular Battle') . "\n" ?>
        - key: gachi
          name:
            <?= $_name(12, 'app-rule', 'Ranked Battle') . "\n" ?>

    Map:
      type: object
      description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Stage information')) . "\n" ?>
      properties:
        key:
          type: string
          pattern: ^[a-z0-9_]+$
          enum:
<?php foreach ($_maps as $_) { ?>
            - <?= Yaml::dump($_['key']) . "\n" ?>
<?php } ?>
          description: |
            <?= Yaml::dump(Yii::t('app-apidoc1', 'Identification string for use with other API')) . "  \n" ?>

            <?= $_keyValueTable(12, Yii::t('app-apidoc1', 'Stage Name'), 'app-map', $_maps) . "\n" ?>
        name:
          $ref: '#/components/schemas/Name'
        area:
          type: number
          format: int32
          nullable: true
          description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Total area')) . "\n" ?>
        release_at:
          <<: *schemasDateTime
          description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Date and time when ready to play')) . "\n" ?>
          nullable: true

    Weapon:
      type: object
      description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Weapon information')) . "\n" ?>
      properties:
        key:
          type: string
          pattern: ^[a-z0-9_]+$
          enum:
<?php foreach ($_weapons as $_) { ?>
            - <?= Yaml::dump($_['key']) . "\n" ?>
<?php } ?>
          description: |
            <?= Yaml::dump(Yii::t('app-apidoc1', 'Identification string for use with other API')) . "  \n" ?>

            <?= $_keyValueTable(12, Yii::t('app-apidoc1', 'Weapon Name'), 'app-weapon', $_weapons) . "\n" ?>
        name:
          $ref: '#/components/schemas/Name'
        type:
          $ref: '#/components/schemas/WeaponType'
        sub:
          $ref: '#/components/schemas/SubWeapon'
        special:
          $ref: '#/components/schemas/Special'

    WeaponType:
      type: object
      description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Weapon type (category) information')) . "\n" ?>
      properties:
        key:
          type: string
          pattern: ^[a-z0-9_]+$
          enum:
<?php foreach ($_weaponTypes as $_) { ?>
            - <?= Yaml::dump($_['key']) . "\n" ?>
<?php } ?>
          description: |
            <?= Yaml::dump(Yii::t('app-apidoc1', 'Identification string for use with other API')) . "  \n" ?>

            <?= $_keyValueTable(12, Yii::t('app-apidoc1', 'Name'), 'app-weapon', $_weaponTypes) . "\n" ?>
        name:
          $ref: '#/components/schemas/Name'

    SubWeapon:
      type: object
      description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Sub weapon information')) . "\n" ?>
      properties:
        key:
          type: string
          pattern: ^[a-z0-9_]+$
          enum:
<?php foreach ($_subWeapons as $_) { ?>
            - <?= Yaml::dump($_['key']) . "\n" ?>
<?php } ?>
          description: |
            <?= Yaml::dump(Yii::t('app-apidoc1', 'Identification string for use with other API')) . "  \n" ?>

            <?= $_keyValueTable(12, Yii::t('app-apidoc1', 'Sub Weapon Name'), 'app-subweapon', $_subWeapons) . "\n" ?>
        name:
          $ref: '#/components/schemas/Name'

    Special:
      type: object
      description: <?= Yaml::dump(Yii::t('app-apidoc1', 'Special weapon information')) . "\n" ?>
      properties:
        key:
          type: string
          pattern: ^[a-z0-9_]+$
          enum:
<?php foreach ($_specials as $_) { ?>
            - <?= Yaml::dump($_['key']) . "\n" ?>
<?php } ?>
          description: |
            <?= Yaml::dump(Yii::t('app-apidoc1', 'Identification string for use with other API')) . "  \n" ?>

            <?= $_keyValueTable(12, Yii::t('app-apidoc1', 'Special Weapon Name'), 'app-special', $_specials) . "\n" ?>
        name:
          $ref: '#/components/schemas/Name'

tags:
  - name: general
