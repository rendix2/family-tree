<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownPresenter.php
 * User: Tomáš Babický
 * Date: 20.09.2020
 * Time: 0:11
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\TownForm;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Entities\TownEntity;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Model\Facades\TownFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownAddressModalDelete;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\TownWeddingModalDelete;

/**
 * Class TownPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class TownPresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
    }

    use TownAddressModalDelete;
    use TownWeddingModalDelete;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownFacade $townFacade
     */
    private $townFacade;

    /**
     * @var TownManager $manager
     */
    private $manager;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * TownPresenter constructor.
     *
     * @param AddressFacade $addressFacade
     * @param CountryManager $countryManager
     * @param JobFacade $jobFacade
     * @param PersonManager $personManager
     * @param TownFacade $townFacade
     * @param TownManager $townManager
     * @param WeddingFacade $weddingFacade
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        CountryManager $countryManager,
        JobFacade $jobFacade,
        PersonManager $personManager,
        TownFacade $townFacade,
        TownManager $townManager,
        WeddingFacade $weddingFacade,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->countryManager = $countryManager;
        $this->jobFacade = $jobFacade;
        $this->personManager = $personManager;
        $this->townFacade = $townFacade;
        $this->manager = $townManager;
        $this->weddingFacade = $weddingFacade;
        $this->weddingManager = $weddingManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $towns = $this->townFacade->getAllCached();

        $this->template->towns = $towns;

        $this->template->addFilter('country', new CountryFilter());
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $countries = $this->countryManager->getPairsCached('name');

        $this['form-countryId']->setItems($countries);

        if ($id !== null) {
            $town = $this->townFacade->getByPrimaryKey($id);

            if (!$town) {
                $this->error('Item not found.');
            }

            $this['form-countryId']->setDefaultValue($town->country->id);
            $this['form']->setDefaults((array)$town);
        }
    }

    /**
     * @param int|null $id
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $town = new TownEntity([]);

            $birthPersons = [];
            $deathPersons = [];
            $weddings = [];
            $gravedPersons = [];
            $jobs = [];
            $addresses = [];
        } else {
            $town = $this->townFacade->getByPrimaryKey($id);

            $birthPersons = $this->personManager->getByBirthTownId($id);
            $deathPersons = $this->personManager->getByDeathTownId($id);
            $gravedPersons = $this->personManager->getByGravedTownId($id);
            $weddings = $this->weddingFacade->getByTownIdCached($id);
            $jobs = $this->jobFacade->getByTownIdCached($id);
            $addresses = $this->addressFacade->getByTownIdCached($id);
        }

        $this->template->birthPersons = $birthPersons;
        $this->template->deathPersons = $deathPersons;
        $this->template->gravedPersons = $gravedPersons;
        $this->template->jobs = $jobs;
        $this->template->town = $town;
        $this->template->weddings = $weddings;
        $this->template->addresses = $addresses;

        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('duration', new DurationFilter($this->getTranslator()));
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $formFactory = new TownForm($this->getTranslator());
        $form = $formFactory->create();

        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }
}
