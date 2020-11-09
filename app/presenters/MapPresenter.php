<?php
/**
 *
 * Created by PhpStorm.
 * Filename: MapPresenter.php
 * User: Tomáš Babický
 * Date: 08.11.2020
 * Time: 23:30
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Rendix2\FamilyTree\App\Filters\DateFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;

/**
 * Class MapPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class MapPresenter extends BasePresenter
{
    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var Person2AddressManager  $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * MapPresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param JobManager $jobManager
     * @param PersonManager $personManager
     * @param Person2AddressManager $person2AddressManager
     * @param TownManager $townManager
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressManager $addressManager,
        JobManager $jobManager,
        PersonManager $personManager,
        Person2AddressManager $person2AddressManager,
        TownManager $townManager,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->personManager = $personManager;
        $this->person2AddressManager = $person2AddressManager;
        $this->addressManager = $addressManager;
        $this->jobManager = $jobManager;
        $this->townManager = $townManager;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @return void
     */
    public function handleGetData()
    {
        $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
        $jobFilter = new JobFilter();
        $dateFilter = new DateFilter($this->getTranslator());

        $addresses = $this->addressManager->getPairsToMap();
        $towns = $this->townManager->getPairsToMap();

        $addressesResult = [];

        foreach ($addresses as $addressId => $address) {
            $personsTemp = $this->person2AddressManager->getAllByRightJoined($addressId);
            $persons = [];

            foreach ($personsTemp as $person) {
                $persons[] = $personFilter($person);
            }

            $jobsTemp = $this->jobManager->getByAddressId($addressId);
            $jobs = [];

            foreach ($jobsTemp as $job) {
                $jobs[] = $jobFilter($job);
            }

            $birthPersonsTemp = $this->personManager->getByBirthAddressId($addressId);
            $birthPersons = [];

            foreach ($birthPersonsTemp as $birthPerson) {
                $birthPersons[] = $personFilter($birthPerson);
            }

            $deadPersonsTemp = $this->personManager->getByDeathAddressId($addressId);
            $deadPersons = [];

            foreach ($deadPersonsTemp as $deadPerson) {
                $deadPersons[] = $personFilter($deadPerson);
            }

            $gravedPersonsTemp = $this->personManager->getByGravedAddressId($addressId);
            $gravedPersons = [];

            foreach ($gravedPersonsTemp as $gravedPerson) {
                $gravedPersons[] = $personFilter($gravedPerson);
            }

            $addressesResult[$addressId]['address'] = $address;
            $addressesResult[$addressId]['persons'] = $persons;
            $addressesResult[$addressId]['jobs'] = $jobs;
            $addressesResult[$addressId]['birthPersons'] = $birthPersons;
            $addressesResult[$addressId]['deadPersons'] = $deadPersons;
            $addressesResult[$addressId]['gravedPersons'] = $gravedPersons;
        }

        $townsResult = [];

        foreach ($towns as $townId => $town) {
            $birthPersonsTemp = $this->personManager->getByBirthTownId($townId);
            $birthPersons = [];

            foreach ($birthPersonsTemp as $birthPerson) {
                $birthPersons[] = $personFilter($birthPerson);
            }

            $deadPersonsTemp = $this->personManager->getByDeathTownId($townId);
            $deadPersons = [];

            foreach ($deadPersonsTemp as $deadPerson) {
                $deadPersons[] = $personFilter($deadPerson);
            }

            $gravedPersonsTemp = $this->personManager->getByGravedTownId($townId);
            $gravedPersons = [];

            foreach ($gravedPersonsTemp as $gravedPerson) {
                $gravedPersons[] = $personFilter($gravedPerson);
            }

            $townJobsTemp = $this->jobManager->getByTownId($townId);
            $townJobs = [];

            foreach ($townJobsTemp as $townJob) {
                $townJobs[] = $jobFilter($townJob);
            }

            $townWeddingsTemp = $this->weddingManager->getByTownId($townId);
            $townWeddings = [];

            foreach ($townWeddingsTemp as $townWedding) {
                $husband = $this->personManager->getByPrimaryKey($townWedding->husbandId);
                $wife = $this->personManager->getByPrimaryKey($townWedding->wifeId);

                $townWeddings[] = [
                    'husband' => $personFilter($husband),
                    'wife' => $personFilter($wife),
                    'duration' => $dateFilter($townWedding)
                ];
            }

            $townsResult[$townId]['town'] = $town;
            $townsResult[$townId]['birthPersons'] = $birthPersons;
            $townsResult[$townId]['deadPersons'] = $deadPersons;
            $townsResult[$townId]['gravedPersons'] = $gravedPersons;
            $townsResult[$townId]['jobs'] = $townJobs;
            $townsResult[$townId]['weddings'] = $townWeddings;
        }

        $result = [
            'addresses' => $addressesResult,
            'towns' => $townsResult
        ];

        $this->sendJson($result);
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
    }
}
