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
use Rendix2\FamilyTree\App\Managers\JobSettingsManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
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
     * @var AddressFilter $addressFilter
     */
    private $addressFilter;

    /**
     * @var JobFilter $jobFilter
     */
    private $jobFilter;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var JobSettingsManager $jobSettingsManager
     */
    private $jobSettingsManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var TownFilter $townFilter
     */
    private $townFilter;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var WeddingFilter $weddingFilter
     */
    private $weddingFilter;

    /**
     * MapPresenter constructor.
     *
     * @param AddressFacade $addressFacade
     * @param AddressFilter $addressFilter
     * @param JobFilter $jobFilter
     * @param JobSettingsManager $jobSettingsManager
     * @param PersonFilter $personFilter
     * @param PersonSettingsManager $personSettingsManager
     * @param Person2AddressFacade $person2AddressFacade
     * @param TownFacade $townFacade
     * @param TownFilter $townFilter
     * @param WeddingFacade $weddingFacade
     * @param WeddingFilter $weddingFilter
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressFilter $addressFilter,
        JobFilter $jobFilter,
        JobSettingsManager $jobSettingsManager,
        PersonFilter $personFilter,
        PersonSettingsManager $personSettingsManager,
        Person2AddressFacade $person2AddressFacade,
        TownFacade $townFacade,
        TownFilter $townFilter,
        WeddingFacade $weddingFacade,
        WeddingFilter $weddingFilter
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->person2AddressFacade = $person2AddressFacade;
        $this->townFacade = $townFacade;
        $this->weddingFacade = $weddingFacade;

        $this->addressFilter = $addressFilter;
        $this->jobFilter = $jobFilter;
        $this->personFilter = $personFilter;
        $this->townFilter = $townFilter;
        $this->weddingFilter = $weddingFilter;

        $this->personSettingsManager = $personSettingsManager;
        $this->jobSettingsManager = $jobSettingsManager;
    }

    /**
     * @return void
     */
    public function handleGetData()
    {
        if (!$this->isAjax()) {
            $this->redirect('Map:default');
        }

        $addressFilter = $this->addressFilter;
        // $durationFilter = new DurationFilter($this->translator);
        $jobFilter = $this->jobFilter;
        $personFilter = $this->personFilter;
        $townFilter = $this->townFilter;
        $weddingFilter = $this->weddingFilter;

        $addresses = $this->addressFacade->getToMap();
        $towns = $this->townFacade->getToMap();

        $addressesResult = [];

        foreach ($addresses as $address) {
            $personsTemp = $this->person2AddressFacade->getByRightManagerCached($address->id);
            $persons = [];

            foreach ($personsTemp as $person) {
                $persons[] = $personFilter($person->person);
            }

            $jobsTemp = $this->jobSettingsManager->getByAddressIdCached($address->id);
            $jobs = [];

            foreach ($jobsTemp as $job) {
                $jobs[] = $jobFilter($job);
            }

            $birthPersonsTemp = $this->personSettingsManager->getByBirthAddressIdCached($address->id);
            $birthPersons = [];

            foreach ($birthPersonsTemp as $birthPerson) {
                $birthPersons[] = $personFilter($birthPerson);
            }

            $deadPersonsTemp = $this->personSettingsManager->getByDeathAddressIdCached($address->id);
            $deadPersons = [];

            foreach ($deadPersonsTemp as $deadPerson) {
                $deadPersons[] = $personFilter($deadPerson);
            }

            $gravedPersonsTemp = $this->personSettingsManager->getByGravedAddressIdCached($address->id);
            $gravedPersons = [];

            foreach ($gravedPersonsTemp as $gravedPerson) {
                $gravedPersons[] = $personFilter($gravedPerson);
            }

            $addressWeddingsTemp = $this->weddingFacade->getByAddressIdCached($address->id);
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
            $birthPersonsTemp = $this->personSettingsManager->getByBirthTownId($town->id);
            $birthPersons = [];

            foreach ($birthPersonsTemp as $birthPerson) {
                $birthPersons[] = $personFilter($birthPerson);
            }

            $deadPersonsTemp = $this->personSettingsManager->getByDeathTownId($town->id);
            $deadPersons = [];

            foreach ($deadPersonsTemp as $deadPerson) {
                $deadPersons[] = $personFilter($deadPerson);
            }

            $gravedPersonsTemp = $this->personSettingsManager->getByGravedTownId($town->id);
            $gravedPersons = [];

            foreach ($gravedPersonsTemp as $gravedPerson) {
                $gravedPersons[] = $personFilter($gravedPerson);
            }

            $townJobsTemp = $this->jobSettingsManager->getByTownId($town->id);
            $townJobs = [];

            foreach ($townJobsTemp as $townJob) {
                $townJobs[] = $jobFilter($townJob);
            }

            $townWeddingsTemp = $this->weddingFacade->getByTownIdCached($town->id);
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
