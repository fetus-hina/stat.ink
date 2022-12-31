<?php //phpcs:ignore PSR1.Files.SideEffects

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m210124_210928_splatfest2_data extends Migration
{
    public function safeUp()
    {
        $anHour = new DateInterval('PT1H');

        foreach ($this->createDataset() as $row) {
            $this->insert('splatfest2', [
                'name_a' => $row->alpha,
                'name_b' => $row->bravo,
                'term' => vsprintf('[%s, %s)', [
                    $row->start->format(DateTime::ATOM),
                    $row->end->format(DateTime::ATOM),
                ]),
                'query_term' => vsprintf('[%s, %s)', [
                    $row->start->sub($anHour)->format(DateTime::ATOM),
                    $row->end->add($anHour)->format(DateTime::ATOM),
                ]),
            ]);
            $id = (int)$this->db->lastInsertID;
            $this->batchInsert('splatfest2_region', ['fest_id', 'region_id'], array_map(
                function (int $regionID) use ($id): array {
                    return [$id, $regionID];
                },
                $row->regions,
            ));
        }
    }

    public function safeDown()
    {
        foreach (['splatfest2_region', 'splatfest2'] as $tableName) {
            $this->delete($tableName);
        }
    }

    public function afterDown()
    {
        $this->doVacuumTables();
    }

    protected function vacuumTables(): array
    {
        return [
            'splatfest2',
            'splatfest2_region',
        ];
    }

    private function createDataset(): array
    {
        $regions = ArrayHelper::map(
            (new Query())->select('*')->from('region2')->orderBy(['key' => SORT_ASC])->all(),
            'key',
            'id',
        );

        $results = [];
        foreach ($this->readData() as $row) {
            if (!isset($results[$row->rowid])) {
                $results[$row->rowid] = (object)[
                    'start' => $row->start,
                    'end' => $row->end,
                    'alpha' => $row->alpha,
                    'bravo' => $row->bravo,
                    'regions' => [],
                ];
            }

            $results[$row->rowid]->regions[] = $regions[$row->region];
            sort($results[$row->rowid]->regions, SORT_NUMERIC);
        }

        usort($results, function (stdClass $a, stdClass $b): int {
            return $a->start->getTimestamp() <=> $b->start->getTimestamp()
                ?: strnatcasecmp(implode(',', $a->regions), implode(',', $b->regions))
                ?: strcasecmp($a->alpha, $b->alpha)
                ?: strcasecmp($a->bravo, $b->bravo);
        });

        return $results;
    }

    private function readData()
    {
        $locale = setlocale(LC_CTYPE, '0');
        setlocale(LC_CTYPE, ['en_US.utf8', 'ja_JP.utf8', 'C']);
        try {
            $fh = fopen(__FILE__, 'rt');
            fseek($fh, __COMPILER_HALT_OFFSET__, SEEK_SET);
            try {
                while (!feof($fh)) {
                    $line = fgets($fh);
                    if ($line === false) {
                        return;
                    }
                    $line = trim((string)$line);
                    if ($line !== '') {
                        $data = str_getcsv($line, ',', '"', '');
                        if (count($data) === 5) {
                            $start = new DateTimeImmutable($data[1], new DateTimeZone('Etc/UTC'));
                            yield (object)[
                                'rowid' => implode('--', [
                                    str_replace(' ', '-', $data[3]),
                                    str_replace(' ', '-', $data[4]),
                                    $start->getTimestamp(),
                                    $data[2],
                                ]),
                                'region' => $data[0],
                                'start' => $start,
                                'end' => $start->add(new DateInterval(sprintf('P%dD', $data[2]))),
                                'alpha' => $data[3],
                                'bravo' => $data[4],
                            ];
                        } else {
                            fwrite(STDERR, 'WARN: Maybe invalid data: ' . $line . "\n");
                        }
                    }
                }
            } finally {
                fclose($fh);
            }
        } finally {
            setlocale(LC_CTYPE, $locale);
        }
    }
}
__halt_compiler();
jp,2017-08-04T06:00Z,1,Mayo,Ketchup
jp,2017-09-09T06:00Z,1,Fries,McNuggets
jp,2017-10-14T04:00Z,1,Dexterity,Endurance
jp,2017-11-11T06:00Z,1,Lemon,No Lemon
jp,2017-12-09T06:00Z,1,Inner Wear,Outer Wear
jp,2018-01-13T06:00Z,1,Action,Comedy
jp,2018-02-03T06:00Z,1,Champion,Challenger
jp,2018-03-03T06:00Z,1,Hana,Dango
jp,2018-03-24T06:00Z,1,Newest,Most Popular
jp,2018-04-21T06:00Z,1,New Lifeform,Future Tech
jp,2018-05-19T06:00Z,1,Hello Kitty,Cinnamoroll
jp,2018-05-26T06:00Z,1,My Melody,Pompompurin
jp,2018-06-09T06:00Z,1,Hello Kitty,My Melody
jp,2018-07-21T06:00Z,1,Squid,Octopus
jp,2018-08-18T06:00Z,1,Mushroom Mountain,Bamboo Shoot Village
jp,2018-09-23T06:00Z,1,Tsubuan,Koshian
jp,2018-10-19T08:00Z,2,Trick,Treat
jp,2018-11-10T06:00Z,1,Pocky Chocolate,Pocky: Gokuboso
jp,2018-12-15T06:00Z,1,Hero,Villain
jp,2019-01-04T08:00Z,2,Fam,Friend
jp,2019-02-02T06:00Z,1,Boke,Tsukkomi
jp,2019-03-16T06:00Z,1,Knight,Wizard
jp,2019-04-19T08:00Z,2,Hare,Tortoise
jp,2019-05-11T06:00Z,1,Ce League,Pa League
jp,2019-06-15T06:00Z,1,No Pineapple,Pineapple
jp,2019-07-18T12:00Z,3,Chaos,Order
jp,2020-05-22T22:00Z,2,Mayo,Ketchup
jp,2020-08-21T22:00Z,2,Chicken,Egg
jp,2020-10-30T22:00Z,2,Trick,Treat
jp,2021-01-15T22:00Z,2,Super Mushroom,Super Star
na,2017-08-05T04:00Z,1,Mayo,Ketchup
na,2017-09-02T04:00Z,1,Flight,Invisibility
na,2017-10-14T04:00Z,1,Vampire,Werewolf
na,2017-11-18T05:00Z,1,Sci-Fi,Fantasy
na,2017-12-16T05:00Z,1,Sweater,Sock
na,2018-01-13T04:00Z,1,Action,Comedy
na,2018-02-17T05:00Z,1,Money,Love
na,2018-03-10T04:00Z,1,Chicken,Egg
na,2018-04-07T04:00Z,1,Baseball,Soccer
na,2018-05-05T04:00Z,1,Raph,Leo
na,2018-05-12T04:00Z,1,Mikey,Donnie
na,2018-05-19T04:00Z,1,Raph,Donnie
na,2018-06-23T04:00Z,1,Pulp,No-Pulp
na,2018-07-21T04:00Z,1,Squid,Octopus
na,2018-08-25T04:00Z,1,Fork,Spoon
na,2018-09-22T04:00Z,1,Retro,Modern
na,2018-10-19T22:00Z,2,Trick,Treat
na,2018-11-17T04:00Z,1,Salsa,Guacamole
na,2018-12-15T04:00Z,1,Hero,Villain
na,2019-01-04T22:00Z,2,Fam,Friend
na,2019-02-09T04:00Z,1,Pancake,Waffle
na,2019-03-16T04:00Z,1,Knight,Wizard
na,2019-04-19T21:00Z,2,Hare,Tortoise
na,2019-05-18T04:00Z,1,Time Travel,Teleportation
na,2019-06-15T04:00Z,1,Unicorn,Narwhal
na,2019-07-18T12:00Z,3,Chaos,Order
na,2020-05-22T22:00Z,2,Mayo,Ketchup
na,2020-08-21T22:00Z,2,Chicken,Egg
na,2020-10-30T22:00Z,2,Trick,Treat
na,2021-01-15T22:00Z,2,Super Mushroom,Super Star
eu,2017-08-05T14:00Z,1,Mayo,Ketchup
eu,2017-09-02T14:00Z,1,Flight,Invisibility
eu,2017-10-07T14:00Z,1,Front Roll,Back Roll
eu,2017-11-04T15:00Z,1,Warm,Cold
eu,2017-12-09T15:00Z,1,Film,Book
eu,2018-01-13T12:00Z,1,Action,Comedy
eu,2018-02-10T14:00Z,1,Gherk-OUT,Gherk-IN
eu,2018-03-10T14:00Z,1,Chicken,Egg
eu,2018-04-07T13:00Z,1,Salty,Sweet
eu,2018-05-05T14:00Z,1,Raph,Leo
eu,2018-05-12T14:00Z,1,Mikey,Donnie
eu,2018-05-19T14:00Z,1,Raph,Donnie
eu,2018-06-23T14:00Z,1,Pulp,No-Pulp
eu,2018-07-21T14:00Z,1,Squid,Octopus
eu,2018-08-18T14:00Z,1,Adventure,Relax
eu,2018-09-22T14:00Z,1,Retro,Modern
eu,2018-10-19T14:00Z,2,Trick,Treat
eu,2018-11-24T14:00Z,1,Eat It,Save It
eu,2018-12-15T14:00Z,1,Hero,Villain
eu,2018-01-04T14:00Z,2,Fam,Friend
eu,2019-02-09T14:00Z,1,Pancake,Waffle
eu,2019-03-16T12:00Z,1,Knight,Wizard
eu,2019-04-19T14:00Z,2,Hare,Tortoise
eu,2019-05-18T12:00Z,1,Time Travel,Teleportation
eu,2019-06-15T16:00Z,1,Kid,Grown-Up
eu,2019-07-18T12:00Z,3,Chaos,Order
eu,2020-05-22T22:00Z,2,Mayo,Ketchup
eu,2020-08-21T22:00Z,2,Chicken,Egg
eu,2020-10-30T22:00Z,2,Trick,Treat
eu,2021-01-15T22:00Z,2,Super Mushroom,Super Star
