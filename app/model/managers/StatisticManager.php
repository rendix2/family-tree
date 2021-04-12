<?php
/**
 *
 * Created by PhpStorm.
 * Filename: StatisticManager.php
 * User: Tomáš Babický
 * Date: 09.02.2021
 * Time: 0:11
 */

namespace Rendix2\FamilyTree\App\Model\Managers;

use dibi;
use Dibi\Connection;
use Dibi\Row;
use Rendix2\FamilyTree\App\Services\PersonAgeService;

/**
 * Class StatisticManager
 *
 * @package Rendix2\FamilyTree\App\Model\Managers
 */
class StatisticManager
{
    /**
     * @var Connection $connection
     */
    private $connection;

    /**
     * @var PersonAgeService $personAgeService
     */
    private $personAgeService;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * StatisticManager constructor.
     *
     * @param Connection       $connection
     * @param PersonManager    $personManager
     * @param PersonAgeService $personAgeService
     */
    public function __construct(
        Connection $connection,
        PersonManager $personManager,
        PersonAgeService $personAgeService
    ) {
        $this->connection = $connection;
        $this->personAgeService = $personAgeService;
        $this->personManager = $personManager;
    }

    /**
     * @return Row[]
     */
    public function getPersonNameCount()
    {
        return $this->connection
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
        return $this->connection
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
        return $this->connection
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
        return $this->connection
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
        $persons = $this->personManager->select()->getCachedManager()->getAll();

        $averageAge = 0.0;
        $personsWithAge = 0;

        foreach ($persons as $person) {
            $personAge = $this->personAgeService->calculateAgeByPerson($person)['age'];

            if ($personAge !== null && $personAge !== 0 && $personAge > 0) {
                $personsWithAge++;
                $averageAge += $personAge;
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
        $persons = $this->personManager->select()->getCachedManager()->getAll();
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
        $persons = $this->personManager->select()->getCachedManager()->getAll();
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

    /**
     * @return array
     */
    public function getPersonAgeCount()
    {
        $persons = $this->personManager->select()->getCachedManager()->getAll();
        $personAges = [];

        foreach ($persons as $person) {
            $personAge = $this->personAgeService->calculateAgeByPerson($person)['age'];

            if ($personAge === null || $personAge === 0 || $personAge < 0) {
                continue;
            }

            if (isset($personAges[$personAge])) {
                $personAges[$personAge]++;
            } else {
                $personAges[$personAge] = 1;
            }
        }

        uasort($personAges, [self::class, 'cmp']);

        $personsAgesResult = [];

        foreach ($personAges as $age => $count) {
            $personsAgesResult[] = [$age, $count];
        }

        return $personsAgesResult;
    }
}
