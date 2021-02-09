<?php
/**
 *
 * Created by PhpStorm.
 * Filename: StatisticManager.php
 * User: Tomáš Babický
 * Date: 09.02.2021
 * Time: 0:11
 */

namespace Rendix2\FamilyTree\App\Managers;

use dibi;
use Dibi\Connection;
use Dibi\Row;

/**
 * Class StatisticManager
 *
 * @package Rendix2\FamilyTree\App\Managers
 */
class StatisticManager extends ConnectionManager
{
    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * StatisticManager constructor.
     *
     * @param Connection $dibi
     * @param PersonManager $personManager
     */
    public function __construct(
        Connection $dibi,
        PersonManager $personManager
    ) {
        parent::__construct($dibi);

        $this->personManager = $personManager;
    }

    /**
     * @return Row[]
     */
    public function getPersonNameCount()
    {
        return $this->getDibi()
            ->select('name')
            ->select('COUNT(name)')
            ->as('count')
            ->from(Tables::PERSON_TABLE)
            ->groupBy('name')
            ->orderBy('count', dibi::DESC)
            ->orderBy('name', dibi::ASC)
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getPersonSurnameCount()
    {
        return $this->getDibi()
            ->select('surname')
            ->select('COUNT(surname)')
            ->as('count')
            ->from(Tables::PERSON_TABLE)
            ->groupBy('surname')
            ->orderBy('count', dibi::DESC)
            ->orderBy('surname', dibi::ASC)
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getPersonBirthTownCount()
    {
        return $this->getDibi()
            ->select('t.name')
            ->select('COUNT(p.birthTownId)')
            ->as('count')
            ->from(Tables::PERSON_TABLE)
            ->as('p')
            ->innerJoin(Tables::TOWN_TABLE)
            ->as('t')
            ->on('p.birthTownId = t.id')
            ->where('p.birthTownId IS NOT NULL')
            ->groupBy('birthTownId')
            ->orderBy('count', dibi::DESC)
            ->fetchAll();
    }

    /**
     * @return Row[]
     */
    public function getPersonDeathTownCount()
    {
        return $this->getDibi()
            ->select('t.name')
            ->select('COUNT(p.deathTownId)')
            ->as('count')
            ->from(Tables::PERSON_TABLE)
            ->as('p')
            ->innerJoin(Tables::TOWN_TABLE)
            ->as('t')
            ->on('p.deathTownId = t.id')
            ->where('p.deathTownId IS NOT NULL')
            ->groupBy('deathTownId')
            ->orderBy('count', dibi::DESC)
            ->fetchAll();
    }

    /**
     * @return float
     */
    public function getAveragePersonAge()
    {
        $persons = $this->personManager->getAllCached();
        $personAges = [];

        $averageAge = 0.0;
        $personsWithAge = 0;

        foreach ($persons as $person) {
            $personAges[$person->id] = $this->personManager->calculateAgeByPerson($person);

            if ($personAges[$person->id]['age'] !== null) {
                $personsWithAge++;
                $averageAge += $personAges[$person->id]['age'];
            }
        }

        return $averageAge / $personsWithAge;
    }

    /**
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    public function cmp($a, $b)
    {
        if ($a === $b) {
            return 0;
        }

        return $a > $b ? -1 : 1;
    }

    /**
     * @return array
     */
    public function getPersonBirthYearCount()
    {
        $persons = $this->personManager->getAllCached();
        $personsYears = [];

        foreach ($persons as $person) {
            if ($person->hasBirthYear) {
                if (isset($personsYears[$person->birthYear])) {
                    $personsYears[$person->birthYear]++;
                } else {
                    $personsYears[$person->birthYear] = 1;
                }
            }

            if ($person->hasBirthDate) {
                $personYear = (int) $person->birthDate->format('Y');

                if (isset($personsYears[$personYear])) {
                    $personsYears[$personYear]++;
                } else {
                    $personsYears[$personYear] = 1;
                }
            }
        }

        uasort($personsYears, [self::class, 'cmp']);

        $personsYearsResult = [];

        foreach ($personsYears as $year => $count) {
            $personsYearsResult[] = [$year, $count];
        }

        return $personsYearsResult;
    }

    /**
     * @return array
     */
    public function getPersonDeathYearCount()
    {
        $persons = $this->personManager->getAllCached();
        $personsYears = [];

        foreach ($persons as $person) {
            if ($person->hasDeathYear) {
                if (isset($personsYears[$person->deathYear])) {
                    $personsYears[$person->deathYear]++;
                } else {
                    $personsYears[$person->deathYear] = 1;
                }
            }

            if ($person->hasDeathDate) {
                $personYear = (int) $person->deathDate->format('Y');

                if (isset($personsYears[$personYear])) {
                    $personsYears[$personYear]++;
                } else {
                    $personsYears[$personYear] = 1;
                }
            }
        }

        uasort($personsYears, [self::class, 'cmp']);

        $personsYearsResult = [];

        foreach ($personsYears as $year => $count) {
            $personsYearsResult[] = [$year, $count];
        }

        return $personsYearsResult;
    }
}
