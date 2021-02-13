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
use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\CountryFilter;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\TownFilter;
use Rendix2\FamilyTree\App\Forms\AddressForm;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Settings\AddressSettings;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\JobManager;
use Rendix2\FamilyTree\App\Managers\JobSettingsManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Managers\WeddingManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressAddCountryModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressAddJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressAddPersonAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressAddTownModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteAddressJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteBirthPersonModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteDeathPersonModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteGravedPersonModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteJobModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeletePersonAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteAddressFromEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteAddressFromListModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteWeddingAddressModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Address\AddressDeleteWeddingModal;
use Rendix2\FamilyTree\App\Presenters\Traits\Town\AddressAddWeddingModal;

/**
 * Class AddressPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class AddressPresenter extends BasePresenter
{
    use AddressAddCountryModal;
    use AddressAddTownModal;

    use AddressAddPersonAddressModal;
    use AddressDeletePersonAddressModal;

    use AddressDeleteAddressFromEditModal;
    use AddressDeleteAddressFromListModal;

    use AddressAddJobModal;
    use AddressDeleteJobModal;

    use AddressDeleteAddressJobModal;

    use AddressDeleteBirthPersonModal;
    use AddressDeleteDeathPersonModal;
    use AddressDeleteGravedPersonModal;

    use AddressAddWeddingModal;
    use AddressDeleteWeddingModal;
    use AddressDeleteWeddingAddressModal;

    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var JobSettingsManager $jobSettingsManager
     */
    private $jobSettingsManager;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * @var WeddingManager $weddingManager
     */
    private $weddingManager;

    /**
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * AddressPresenter constructor.
     *
     * @param AddressManager $addressManager
     * @param AddressFacade $addressFacade
     * @param CountryManager $countryManager
     * @param JobFacade $jobFacade
     * @param JobManager $jobManager
     * @param Person2AddressFacade $person2AddressFacade
     * @param Person2AddressManager $person2AddressManager
     * @param PersonFacade $personFacade
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     * @param WeddingFacade $weddingFacade
     * @param WeddingManager $weddingManager
     */
    public function __construct(
        AddressManager $addressManager,
        AddressFacade $addressFacade,
        CountryManager $countryManager,
        JobFacade $jobFacade,
        JobManager $jobManager,
        JobSettingsManager $jobSettingsManager,
        Person2AddressFacade $person2AddressFacade,
        Person2AddressManager $person2AddressManager,
        PersonFacade $personFacade,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager,
        WeddingFacade $weddingFacade,
        WeddingManager $weddingManager
    ) {
        parent::__construct();

        $this->addressManager = $addressManager;
        $this->addressFacade = $addressFacade;

        $this->countryManager = $countryManager;

        $this->jobFacade = $jobFacade;
        $this->jobManager = $jobManager;
        $this->jobSettingsManager = $jobSettingsManager;

        $this->person2AddressFacade = $person2AddressFacade;
        $this->person2AddressManager = $person2AddressManager;

        $this->personFacade = $personFacade;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;

        $this->townManager = $townManager;
        $this->townSettingsManager = $townSettingsManager;

        $this->weddingFacade = $weddingFacade;
        $this->weddingManager = $weddingManager;
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
        $countries = $this->countryManager->getPairsCached('name');

        $this['addressForm-countryId']->setItems($countries);

        if ($id !== null) {
             $address = $this->addressFacade->getByPrimaryKeyCached($id);

            if (!$address) {
                $this->error('Item not found.');
            }

            $towns = $this->townSettingsManager->getPairsByCountryCached($address->town->country->id);

            $this['addressForm-townId']
                ->setItems($towns)
                ->setValue($address->town->id);

            $this['addressForm-countryId']->setDefaultValue($address->town->country->id);
            $this['addressForm']->setDefaults((array) $address);
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

            $weddings = [];
        } else {
            $address = $this->addressFacade->getByPrimaryKeyCached($id);
            $jobs = $this->jobSettingsManager->getByAddressIdCached($id);
            $persons = $this->person2AddressFacade->getByRightCached($id);

            $birthPersons = $this->personSettingsManager->getByBirthAddressIdCached($id);
            $deathPersons = $this->personSettingsManager->getByDeathAddressIdCached($id);
            $gravedPersons = $this->personSettingsManager->getByGravedAddressIdCached($id);

            $weddings = $this->weddingFacade->getByAddressIdCached($id);
        }

        $this->template->persons = $persons;
        $this->template->jobs = $jobs;
        $this->template->address = $address;
        $this->template->weddings = $weddings;

        $this->template->birthPersons = $birthPersons;
        $this->template->deathPersons = $deathPersons;
        $this->template->gravedPersons = $gravedPersons;

        $this->template->addFilter('address', new AddressFilter());
        $this->template->addFilter('duration', new DurationFilter($this->translator));
        $this->template->addFilter('job', new JobFilter($this->getHttpRequest()));
        $this->template->addFilter('person', new PersonFilter($this->translator, $this->getHttpRequest()));
        $this->template->addFilter('town', new TownFilter());
    }

    /**
     * @param int $countryId countryId
     * @param string $formData string json of form data
     */
    public function handleAddressFormSelectCountry($countryId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('Address:edit', $this->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['townId']);

        $countries = $this->countryManager->getPairsCached('name');

        if ($countryId) {
            $this['addressForm-countryId']->setItems($countries)
                ->setDefaultValue($countryId);

            $towns = $this->townSettingsManager->getPairsByCountry($countryId);

            $this['addressForm-townId']->setItems($towns);
        } else {
            $this['addressForm-countryId']->setItems($countries)
                ->setDefaultValue(null);

            $this['addressForm-townId']->setItems([]);
        }

        $this['addressForm']->setDefaults($formDataParsed);

        $this->payload->snippets = [
            $this['addressForm-townId']->getHtmlId() => (string) $this['addressForm-townId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressForm()
    {
        $addressSettings = new AddressSettings();
        $addressSettings->selectCountryHandle = $this->link('addressFormSelectCountry!');

        $formFactory = new AddressForm($this->translator, $addressSettings);

        $form = $formFactory->create();
        $form->onValidate[] = [$this, 'addressFormValidate'];
        $form->onSuccess[] = [$this, 'addressFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addressFormValidate(Form $form, ArrayHash $values)
    {
        $addressFormData = $form->getHttpData();

        $towns = $this->townManager->getPairsByCountry($values->countryId);

        $this['addressForm-townId']->setItems($towns)
            ->setValue($addressFormData['townId'])
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addressFormSuccess(Form $form, ArrayHash $values)
    {
        $values->townId = (int)$form->getHttpData()['townId'];

        $id = $this->getParameter('id');

        if ($id) {
            $this->addressManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('address_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->addressManager->add($values);

            $this->flashMessage('address_added', self::FLASH_SUCCESS);
        }

        $this->redirect('Address:edit', $id);
    }
}
