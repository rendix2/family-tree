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

use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;

/**
 * Class MapPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class MapPresenter extends BasePresenter
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * MapPresenter constructor.
     *
     * @param AddressFacade $addressFacade
     * @param JobManager $jobManager
     * @param PersonManager $personManager
     * @param Person2AddressFacade $person2AddressFacade
     * @param TownFacade $townFacade
     * @param WeddingFacade $weddingFacade
     */
    public function __construct(
        AddressFacade $addressFacade,
        JobManager $jobManager,
        PersonManager $personManager,
        Person2AddressFacade $person2AddressFacade,
        TownFacade $townFacade,
        WeddingFacade $weddingFacade
    ) {
        parent::__construct();

        $this->personManager = $personManager;
        $this->person2AddressFacade = $person2AddressFacade;
        $this->addressFacade = $addressFacade;
        $this->jobManager = $jobManager;
        $this->townFacade = $townFacade;
        $this->weddingFacade = $weddingFacade;
    }

    /**
     * @return void
     */
    public function handleGetData()
    {
        if (!$this->isAjax()) {
            $this->redirect('Map:default');
        }

        set_time_limit(0);

        $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
        $jobFilter = new JobFilter();
        $durationFilter = new DurationFilter($this->getTranslator());
        $addressFilter = new AddressFilter();
        $townFilter = new TownFilter();
        $weddingFilter = new WeddingFilter($personFilter);

        $addresses = $this->addressFacade->getToMap();
        $towns = $this->townFacade->getToMap();

        $addressesResult = [];

        foreach ($addresses as $address) {
            $personsTemp = $this->person2AddressFacade->getByRightManager($address->id);
            $persons = [];

            foreach ($personsTemp as $person) {
                $persons[] = $personFilter($person->person);
            }

            $jobsTemp = $this->jobManager->getByAddressId($address->id);
            $jobs = [];

            foreach ($jobsTemp as $job) {
                $jobs[] = $jobFilter($job);
            }

            $birthPersonsTemp = $this->personManager->getByBirthAddressId($address->id);
            $birthPersons = [];

            foreach ($birthPersonsTemp as $birthPerson) {
                $birthPersons[] = $personFilter($birthPerson);
            }

            $deadPersonsTemp = $this->personManager->getByDeathAddressId($address->id);
            $deadPersons = [];

            foreach ($deadPersonsTemp as $deadPerson) {
                $deadPersons[] = $personFilter($deadPerson);
            }

            $gravedPersonsTemp = $this->personManager->getByGravedAddressId($address->id);
            $gravedPersons = [];

            foreach ($gravedPersonsTemp as $gravedPerson) {
                $gravedPersons[] = $personFilter($gravedPerson);
            }

            $addressWeddingsTemp = $this->weddingFacade->getByAddressId($address->id);
            $addressWeddings = [];

            foreach ($addressWeddingsTemp as $addressWedding) {
                $addressWeddings[] = $weddingFilter($addressWedding);
            }

            $addressesResult[$address->id]['address'] = $addressFilter($address);
            $addressesResult[$address->id]['gps'] = $address->gps;
            $addressesResult[$address->id]['persons'] = $persons;
            $addressesResult[$address->id]['jobs'] = $jobs;
            $addressesResult[$address->id]['birthPersons'] = $birthPersons;
            $addressesResult[$address->id]['deadPersons'] = $deadPersons;
            $addressesResult[$address->id]['gravedPersons'] = $gravedPersons;
            $addressesResult[$address->id]['weddings'] = $addressWeddings;
        }

        $townsResult = [];

        foreach ($towns as $townId => $town) {
            $birthPersonsTemp = $this->personManager->getByBirthTownId($town->id);
            $birthPersons = [];

            foreach ($birthPersonsTemp as $birthPerson) {
                $birthPersons[] = $personFilter($birthPerson);
            }

            $deadPersonsTemp = $this->personManager->getByDeathTownId($town->id);
            $deadPersons = [];

            foreach ($deadPersonsTemp as $deadPerson) {
                $deadPersons[] = $personFilter($deadPerson);
            }

            $gravedPersonsTemp = $this->personManager->getByGravedTownId($town->id);
            $gravedPersons = [];

            foreach ($gravedPersonsTemp as $gravedPerson) {
                $gravedPersons[] = $personFilter($gravedPerson);
            }

            $townJobsTemp = $this->jobManager->getByTownId($town->id);
            $townJobs = [];

            foreach ($townJobsTemp as $townJob) {
                $townJobs[] = $jobFilter($townJob);
            }

            $townWeddingsTemp = $this->weddingFacade->getByTownId($town->id);
            $townWeddings = [];

            foreach ($townWeddingsTemp as $townWedding) {
                $townWeddings[] = $weddingFilter($townWedding);
            }

            $townsResult[$town->id]['town'] = $townFilter($town);
            $townsResult[$town->id]['gps'] = $town->gps;
            $townsResult[$town->id]['birthPersons'] = $birthPersons;
            $townsResult[$town->id]['deadPersons'] = $deadPersons;
            $townsResult[$town->id]['gravedPersons'] = $gravedPersons;
            $townsResult[$town->id]['jobs'] = $townJobs;
            $townsResult[$town->id]['weddings'] = $townWeddings;
        }

        $this->payload->addresses = $addressesResult;
        $this->payload->towns = $townsResult;

        $this->sendPayload();
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
    }
}
