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
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\DateFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
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
     * @var CountryManager $countryManager
     */
    private $countryManager;

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
     * @param CountryManager $countryManager
     * @param JobManager $jobManager
     * @param Person2AddressManager $person2AddressManager
     * @param PersonManager $personManager
     * @param TownManager $townManager
     */
    public function __construct(
        AddressManager $addressManager,
        CountryManager $countryManager,
        JobManager $jobManager,
        Person2AddressManager $person2AddressManager,
        PersonManager $personManager,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->manager = $addressManager;
        $this->countryManager = $countryManager;
        $this->jobManager = $jobManager;
        $this->person2AddressManager = $person2AddressManager;
        $this->personManager = $personManager;
        $this->townManager = $townManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $addresses = $this->manager->getAllJoinedCountryJoinedTown();

        $this->template->addresses = $addresses;
    }

    /**
     * @param int|null $id addressId
     */
    public function actionEdit($id = null)
    {
        $countries = $this->countryManager->getPairs('name');

        $this['form-countryId']->setItems($countries);

        if ($id !== null) {
            $this->item = $item = $this->manager->getByPrimaryKey($id);

            if (!$item) {
                $this->error('Item not found.');
            }

            $towns = $this->townManager->getPairsByCountry($this->item->countryId);

            $this['form-townId']
                ->setPrompt($this->getTranslator()->translate('address_select_town'))
                ->setItems($towns)
                ->setRequired('address_town_required');

            $this['form-countryId']->setDisabled(true);
            $this['form']->setDefaults($item);
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
            $address = $this->manager->getAllByPrimaryKeyJoinedCountryJoinedTown($id);
            $jobs = $this->jobManager->getByAddressId($id);
            $persons = $this->person2AddressManager->getFluentByRightJoined($id)->fetchAll();

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
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
        $this->template->addFilter('dateFT', new DateFilter($this->getTranslator()));
    }

    /**
     * @param int $value countryId
     */
    public function handleSelectCountry($value)
    {
        if ($value) {
            $towns = $this->townManager->getPairsByCountry($value);

            $this['form-townId']->setPrompt($this->getTranslator()->translate('address_select_town'))
                ->setRequired('address_town_required')
                ->setItems($towns);

            $this['form']->setDefaults(['countryId' => $value]);
        } else {
            $this['form-townId']->setPrompt($this->getTranslator()->translate('address_select_town'))->setItems([]);
        }

        $this->redrawControl('formWrapper');
        $this->redrawControl('country');
        $this->redrawControl('town');
        $this->redrawControl('js');
    }

    /**
     * @param int $id addressId
     */
    public function actionPerson($id)
    {
        $address = $this->manager->getAllByPrimaryKeyJoinedCountryJoinedTown($id);

        if (!$address) {
            $this->error('Item not found.');
        }

        $this->address = $address;

        $addressFilter = new AddressFilter();

        $persons = $this->personManager->getAllPairs($this->getTranslator());

        $this['personForm-addressId']->setItems([$id => $addressFilter($address)])
            ->setDisabled()
            ->setValue($id);

        $this['personForm-personId']->setItems($persons);
    }

    /**
     * @param int $id addressId
     */
    public function renderPerson($id)
    {
        $this->template->address = $this->address;

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @return Form
     */
    protected function createComponentForm()
    {
        $form = new Form();

        $form->setTranslator($this->getTranslator());

        $form->addProtection();

        $form->addSelect('countryId', $this->getTranslator()->translate('address_country'))
            ->setTranslator(null)
            ->setRequired('address_country_required')
            ->setPrompt($this->getTranslator()->translate('address_select_country'));

        $form->addSelect('townId', $this->getTranslator()->translate('address_town'))
            ->setTranslator(null)
            ->setPrompt($this->getTranslator()->translate('address_select_town'));

        $form->addText('street', 'address_street');
        $form->addInteger('streetNumber', 'address_street_number')
            ->setNullable();
        $form->addInteger('houseNumber', 'address_house_number')
            ->setNullable();

        $form->addSubmit('send', 'save');

        $form->onSuccess[] = [$this, 'saveForm'];
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

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
        $form->onRender[] = [BootstrapRenderer::class, 'makeBootstrap4'];

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
