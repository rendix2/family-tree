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

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\BootstrapRenderer;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\TownManager;

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
     * @param int|null $id
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
     * @param int $value
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
     * @param int $id address
     */
    public function actionPerson($id)
    {
        $address = $this->manager->getByPrimaryKeyJoinedCountryJoinedTown($id);

        if (!$address) {
            $this->error('Item not found');
        }

        $addressFilter = new AddressFilter();

        $persons = $this->personManager->getAllPairs($this->getTranslator());

        $this['personForm-addressId']->setItems([$id => $addressFilter($address)])
            ->setDisabled()
            ->setValue($id);

        $this['personForm-personId']->setItems($persons);
    }

    /**
     * @param int $id address
     */
    public function renderPerson($id)
    {
        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
    }

    /**
     * @param int|null $id
     *
     * @return void
     */
    public function renderEdit($id = null)
    {
        $persons = $this->person2AddressManager->getFluentByRightJoined($id)->fetchAll();
        $jobs = $this->jobManager->getByAddressId($id);

        $this->template->persons = $persons;
        $this->template->jobs = $jobs;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator()));
        $this->template->addFilter('job', new JobFilter());
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
