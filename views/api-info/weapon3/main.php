<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\TableResponsiveForceAsset;
use app\components\widgets\ApiInfoName;
use app\components\widgets\Icon;
use app\models\Language;
use app\models\Season3;
use app\models\Weapon3;
use app\models\Weapon3Alias;
use app\models\XMatchingGroup3;
use statink\yii2\sortableTable\SortableTableAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Language[] $langs
 * @var View $this
 * @var Weapon3[] $weapons
 * @var array<string, XMatchingGroup3> $matchingGroups2
 * @var array<string, XMatchingGroup3> $matchingGroups6
 */

TableResponsiveForceAsset::register($this);
SortableTableAsset::register($this);

/**
 * @var array<int, string> $seasons
 */
$seasons = (function (): array {
  $ret = [];
  foreach (Season3::find()->orderBy(['start_at' => SORT_ASC])->all() as $i => $season) {
    $seasonNumber = $i + 1;
    $ret[$seasonNumber] = Yii::t('app-season3', 'Season {seasonNumber} ({seasonName})', [
        'seasonNumber' => $seasonNumber,
        'seasonName' => Yii::t('app-season3', $season->name),
    ]);
  }
  return $ret;
})();

$salmonIcon = Icon::s3Salmon();

?>
<h2><?= Html::encode(Yii::t('app', 'Main Weapon')) ?></h2>
<?= Html::tag(
  'p',
  implode(' ', [
    Html::a(
      implode(' ', [
        Icon::apiJson(),
        Html::encode(Yii::t('app', 'JSON format')),
      ]),
      ['api-v3/weapon'],
      ['class' => 'label label-default'],
    ),
    Html::a(
      implode(' ', [
        Icon::apiJson(),
        Html::encode(Yii::t('app', 'JSON format (All langs)')),
      ]),
      ['api-v3/weapon', 'full' => 1],
      ['class' => 'label label-default'],
    ),
  ]),
) . "\n" ?>
<div class="table-responsive table-responsive-force">
  <table class="table table-striped table-condensed table-sortable">
    <thead>
      <tr>
        <th data-sort="int" data-sort-onload="yes"></th>
        <th data-sort="int"><?= Html::tag(
          'span',
          Html::encode('X(2)'),
          [
            'class' => 'auto-tooltip',
            'title' => Yii::t('app-xmatch3', 'Matchmaking Group for {fromSeason} through {toSeason}', [
                'fromSeason' => $seasons[2] ?? '',
                'toSeason' => $seasons[5] ?? '',
            ]),
          ],
        ) ?></th>
        <th data-sort="int"><?= Html::tag(
          'span',
          Html::encode('X(6)'),
          [
            'class' => 'auto-tooltip',
            'title' => Yii::t('app-xmatch3', 'Matchmaking Group from {fromSeason}', [
                'fromSeason' => $seasons[6] ?? '',
            ]),
          ],
        ) ?></th>
        <th data-sort="int"><?= Html::encode(Yii::t('app', 'Category')) ?></th>
        <?= Html::tag('th', $salmonIcon, [
          'class' => 'auto-tooltip',
          'data-sort' => 'int',
          'title' => Yii::t('app-salmon2', 'Salmon Run'),
        ]) . "\n" ?>
        <th data-sort="string"><code>key</code></th>
        <th data-sort="string"><?= Html::encode(Yii::t('app', 'Aliases')) ?></th>
<?php foreach ($langs as $i => $lang) { ?>
        <?= Html::tag('th', Html::encode($lang->name), [
          'class' => $lang->htmlClasses,
          'data' => [
            'sort' => 'string',
          ],
          'lang' => $lang->lang,
        ]) . "\n" ?>
<?php if ($i === 0) { ?>
        <th data-sort="string"><?= Html::encode(Yii::t('app', 'Weapon (Short)')) ?></th>
        <th data-sort="string"><?= Html::encode(Yii::t('app', 'Main Weapon')) ?></th>
        <th data-sort="int"></th>
        <th data-sort="int"></th>
<?php } ?>
<?php } ?>
        <th data-sort="int"><?= Html::encode(Yii::t('app', 'Released')) ?></th>
      </tr>
    </thead>
    <tbody>
<?php foreach ($weapons as $weapon) { ?>
      <tr>
        <?= Html::tag(
          'td',
          Icon::s3Weapon($weapon),
          [
            'data' => [
              'sort-value' => (string)min(
                array_filter(
                  ArrayHelper::getColumn($weapon->weapon3Aliases, 'key'),
                  fn (string $key): bool => (bool)preg_match('/^\d+$/', $key),
                ),
              ),
            ],
          ],
        ) . "\n" ?>
        <?= $this->render('main/td-x-matching', [
          'weapon' => $weapon,
          'group' => $matchingGroups2[$weapon->key] ?? null,
        ]) . "\n" ?>
        <?= $this->render('main/td-x-matching', [
          'weapon' => $weapon,
          'group' => $matchingGroups6[$weapon->key] ?? null,
        ]) . "\n" ?>
        <?= Html::tag(
          'td',
          implode(' ', [
            Icon::s3WeaponCategory($weapon->mainweapon->type),
            Html::encode(
              Yii::t('app-weapon3', $weapon->mainweapon->type->name),
            ),
          ]),
          [
            'data' => [
              'sort-value' => $weapon->mainweapon->type->rank,
            ],
          ],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          $weapon->salmonWeapon3 ? $salmonIcon : '',
          [
            'data' => [
              'sort-value' => $weapon->salmonWeapon3 ? 1 : 0,
            ],
          ]
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Html::tag('code', Html::encode($weapon->key)),
          [
            'data' => [
              'sort-value' => $weapon->key,
            ],
          ]
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          implode(', ', array_map(
            fn (Weapon3Alias $alias): string => Html::tag('code', Html::encode($alias->key)),
            ArrayHelper::sort(
              $weapon->weapon3Aliases,
              fn (Weapon3Alias $a, Weapon3Alias $b): int => strcmp($a->key, $b->key),
            ),
          )),
        ) . "\n" ?>
<?php foreach ($langs as $j => $lang) { ?>
        <?= Html::tag(
          'td',
          ApiInfoName::widget([
            'name' => Yii::t('app-weapon3', $weapon->name, [], $lang->lang),
            'enName' => $weapon->name,
            'lang' => $lang->lang,
          ]),
          [
            'class' => $lang->htmlClasses,
            'lang' => $lang->lang,
          ]
        ) . "\n" ?>
<?php if ($j === 0) { ?>
        <?= $this->render('main/short-name', [
          'name' => Yii::t('app-weapon3', $weapon->name, [], $lang->lang),
        ]) . "\n" ?>
        <?= Html::tag(
          'td',
          implode(' ', [
            $weapon->name === $weapon->mainweapon->name
              ? Html::tag(
                'span',
                Html::encode(Yii::t('app-weapon3', $weapon->mainweapon->name, [], $lang->lang)),
                ['class' => 'text-muted']
              )
              : Html::encode(Yii::t('app-weapon3', $weapon->mainweapon->name, [], $lang->lang)),
          ])
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Icon::s3Subweapon($weapon->subweapon),
          [
            'data' => [
              'sort-value' => $weapon?->subweapon?->rank ?? -1,
            ],
          ],
        ) . "\n" ?>
        <?= Html::tag(
          'td',
          Icon::s3Special($weapon->special),
          [
            'data' => [
              'sort-value' => $weapon?->special?->rank ?? -1,
            ],
          ],
        ) . "\n" ?>
<?php } ?>
<?php } ?>
        <?= Html::tag(
          'td',
          ArrayHelper::getValue(
            $weapon,
            function (Weapon3 $weapon): string {
              $dt = (new DateTimeImmutable($weapon->release_at))
                ->setTimezone(new DateTimeZone('Etc/UTC'));
              return $dt->getTimestamp() <= (int)strtotime('2022-09-01T00:00:00+00:00')
                ? Html::encode(Yii::t('app', 'Launch'))
                : Html::encode(Yii::$app->formatter->asDate($dt, 'medium'));
            },
          ),
          [
            'data' => [
              'sort-value' => Yii::$app->formatter->asDate($weapon->release_at, 'yyyyMMdd'),
            ],
          ],
        ) . "\n" ?>
      </tr>
<?php } ?>
    </tbody>
  </table>
</div>
<p class="text-right mt-2">
  [X(2): <?= Html::encode(Yii::t('app-xmatch3', 'X: Match making group')) ?>]
  <?= Yii::t(
    'app',
    'Source: {source}',
    [
      'source' => Html::a(
        'Twitter @antariska_spl',
        str_starts_with(Yii::$app->language, 'ja')
          ? 'https://twitter.com/antariska_spl/status/1610201648378556418'
          : 'https://twitter.com/antariska_spl/status/1610203442114629632',
        [
          'target' => '_blank',
          'rel' => 'noopener noreferrer',
        ],
      ),
    ],
  ) ?><br>
  [X(6): <?= Html::encode(Yii::t('app-xmatch3', 'X: Match making group')) ?>]
  <?= Yii::t(
    'app',
    'Source: {source}',
    [
      'source' => Html::a(
        'Twitter @M_ClashBlaster',
        'https://twitter.com/M_ClashBlaster/status/1730117977759224074',
        [
          'target' => '_blank',
          'rel' => 'noopener noreferrer',
        ],
      ),
    ],
  ) . "\n" ?>
</p>
