<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AdressPresenter.php
 * User: Tomáš Babický
 * Date: 29.08.2020
 * Time: 2:12
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Dibi\Row;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\AddressForm;
use Rendix2\FamilyTree\App\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressAddressPersonDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressJobDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressPersonDeleteModal;

/**
 * Class AddressPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class AddressPresenter extends BasePresenter
{
    use CrudPresenter {
        actionEdit as traitActionEdit;
        saveForm as traitSaveForm;
    }

    use AddressAddressPersonDeleteModal;
    use AddressJobDeleteModal;
    use AddressPersonDeleteModal;

    /**
     * @var AddressManager $manager
     */
    private $manager;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var Row|false $address
     */
    private $address;

    /**
     * AddressPresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param AddressFacade $addressFacade
     * @param CountryManager $countryManager
     * @param JobManager $jobManager
     * @param Person2AddressFacade $person2AddressFacade
     * @param Person2AddressManager $person2AddressManager
     * @param PersonManager $personManager
     * @param TownManager $townManager
     */
    public function __construct(
        AddressManager $addressManager,
        AddressFacade $addressFacade,
        CountryManager $countryManager,
        JobManager $jobManager,
        Person2AddressFacade $person2AddressFacade,
        Person2AddressManager $person2AddressManager,
        PersonManager $personManager,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->manager = $addressManager;
        $this->addressFacade = $addressFacade;
        $this->countryManager = $countryManager;
        $this->jobManager = $jobManager;
        $this->person2AddressFacade = $person2AddressFacade;
        $this->person2AddressManager = $person2AddressManager;
        $this->personManager = $personManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $addresses = $this->addressFacade->getAllCached();

        $this->template->addresses = $addresses;

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('country', new CountryFilter());
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @param int|null $id addressId
     */
    public function actionEdit($id = null)
    {
        $countries = $this->countryManager->getPairs('name');

        $this['form-countryId']->setItems($countries);

        if ($id !== null) {
             $address = $this->addressFacade->getByPrimaryKeyCached($id);

            if (!$address) {
                $this->error('Item not found.');
            }

            $towns = $this->townManager->getPairsByCountry($address->town->country->id);

            $this['form-townId']
                ->setPrompt($this->getTranslator()->translate('address_select_town'))
                ->setItems($towns)
                ->setRequired('address_town_required')
                ->setValue($address->town->id);

            $this['form-countryId']->setDefaultValue($address->town->country->id);
            $this['form']->setDefaults((array) $address);
        }
    }

    /**
     * @param int|null $id addressId
     *
     * @return void
     */
    public function renderEdit($id = null)
    {
        if ($id === null) {
            $address = [];
            $jobs = [];
            $persons = [];

            $birthPersons = [];
            $deathPersons = [];
            $gravedPersons = [];

        } else {
            $address = $this->addressFacade->getByPrimaryKeyCached($id);
            $jobs = $this->jobManager->getByAddressId($id);
            $persons = $this->person2AddressFacade->getByRightCached($id);

            $birthPersons = $this->personManager->getByBirthAddressId($id);
            $deathPersons = $this->personManager->getByDeathAddressId($id);
            $gravedPersons = $this->personManager->getByGravedAddressId($id);
        }

        $this->template->persons = $persons;
        $this->template->jobs = $jobs;
        $this->template->address = $address;

        $this->template->birthPersons = $birthPersons;
        $this->template->deathPersons = $deathPersons;
        $this->template->gravedPersons = $gravedPersons;

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('job', new JobFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
        $this->template->addFilter('dateFT', new DurationFilter($this->getTranslator()));
    }

    /**
     * @param int $value countryId
     */
    public function handleSelectCountry($value)
    {
        if ($this->isAjax()) {
            if ($value) {
                $towns = $this->townManager->getPairsByCountry($value);

                $this['form-townId']->setPrompt($this->getTranslator()->translate('address_select_town'))
                ->setRequired('address_town_required')
                ->setItems($towns);

                $this['form-countryId']->setDefaultValue($value);
            } else {
                $this['form-townId']->setPrompt($this->getTranslator()->translate('address_select_town'))->setItems([]);
            }

            $this->redrawControl('formWrapper');
            $this->redrawControl('country');
            $this->redrawControl('town');
            $this->redrawControl('js');
        }
    }

    /**
     * @param int $id addressId
     */
    public function actionPerson($id)
    {
        $address = $this->addressFacade->getByPrimaryKey($id);

        if (!$address) {
            $this->error('Item not found.');
        }

        $this->address = $address;

        $addressFilter = new AddressFilter();

        $persons = $this->personManager->getAllPairs($this->getTranslator());

        $this['personForm-addressId']->setItems([$id => $addressFilter($address, $address->town)])
            ->setDisabled()
            ->setDefaultValue($id);

        $this['personForm-personId']->setItems($persons);
    }

    /**
     * @param int $id addressId
     */
    public function renderPerson($id)
    {
        $this->template->address = $this->address;

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $formFactory = new AddressForm($this->getTranslator());

        $form = $formFactory->create($this);
        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $values->townId = (int)$form->getHttpData()['townId'];

        $this->traitSaveForm($form, $values);
    }

    /**
     * @return Form
     */
    public function createComponentPersonForm()
    {
        $formFactory = new Person2AddressForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'savePersonForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function savePersonForm(Form $form, ArrayHash $values)
    {
        $addressId = $this->getParameter('id');

        $values->addressId = $addressId;
        $id = $this->person2AddressManager->addGeneral((array)$values);
        $this->flashMessage('item_added', self::FLASH_SUCCESS);
        $this->redirect(':edit', $addressId);
    }
}
