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
use Rendix2\FamilyTree\App\Controls\Forms\AddressForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\AddressSettings;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressAddCountryModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressAddJobModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressAddPersonAddressModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressAddTownModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressAddWeddingModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteAddressFromEditModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteAddressFromListModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteAddressJobModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteBirthPersonModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteDeathPersonModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteGravedPersonModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteJobModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeletePersonAddressModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteWeddingAddressModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\AddressDeleteWeddingModal;
use Rendix2\FamilyTree\App\Controls\Modals\Address\Container\AddressModalContainer;
use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Facades\WeddingFacade;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\JobSettingsManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;

/**
 * Class AddressPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class AddressPresenter extends BasePresenter
{
    /**
     * @var AddressManager $addressManager
     */
    private $addressManager;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var AddressForm $addressForm
     */
    private $addressForm;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var JobSettingsManager $jobSettingsManager
     */
    private $jobSettingsManager;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

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
     * @var WeddingFacade $weddingFacade
     */
    private $weddingFacade;

    /**
     * @var AddressModalContainer $addressModalContainer
     */
    private $addressModalContainer;

    /**
     * AddressPresenter constructor.
     *
     * @param AddressModalContainer $addressModalContainer
     * @param AddressFacade $addressFacade
     * @param Person2AddressFacade $person2AddressFacade
     * @param WeddingFacade $weddingFacade
     * @param AddressForm $addressForm
     * @param AddressManager $addressManager
     * @param CountryManager $countryManager
     * @param JobSettingsManager $jobSettingsManager
     * @param PersonSettingsManager $personSettingsManager
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     */
    public function __construct(
        AddressModalContainer $addressModalContainer,

        AddressFacade $addressFacade,
        Person2AddressFacade $person2AddressFacade,
        WeddingFacade $weddingFacade,

        AddressForm $addressForm,

        AddressManager $addressManager,
        CountryManager $countryManager,
        TownManager $townManager,

        JobSettingsManager $jobSettingsManager,
        PersonSettingsManager $personSettingsManager,
        TownSettingsManager $townSettingsManager
    ) {
        parent::__construct();

        $this->addressModalContainer = $addressModalContainer;

        $this->addressFacade = $addressFacade;
        $this->person2AddressFacade = $person2AddressFacade;
        $this->weddingFacade = $weddingFacade;

        $this->addressForm = $addressForm;

        $this->addressManager = $addressManager;
        $this->countryManager = $countryManager;
        $this->townManager = $townManager;

        $this->jobSettingsManager = $jobSettingsManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->townSettingsManager = $townSettingsManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $addresses = $this->addressFacade->getAllCached();

        $this->template->addresses = $addresses;
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

        $formFactory = $this->addressForm;

        $form = $formFactory->create($addressSettings);
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

    /**
     * @return AddressAddCountryModal
     */
    public function createComponentAddressAddCountryModal()
    {
        return $this->addressModalContainer->getAddressAddCountryModalFactory()->create();
    }

    /**
     * @return AddressAddJobModal
     */
    public function createComponentAddressAddJobModal()
    {
        return $this->addressModalContainer->getAddressAddJobModalFactory()->create();
    }

    /**
     * @return AddressAddPersonAddressModal
     */
    public function createComponentAddressAddPersonAddressModal()
    {
        return $this->addressModalContainer->getAddressAddPersonAddressModalFactory()->create();
    }

    /**
     * @return AddressAddTownModal
     */
    public function createComponentAddressAddTownModal()
    {
        return $this->addressModalContainer->getAddressAddTownModalFactory()->create();
    }

    /**
     * @return AddressAddWeddingModal
     */
    public function createComponentAddressAddWeddingModal()
    {
        return $this->addressModalContainer->getAddressAddWeddingModalFactory()->create();
    }

    /**
     * @return AddressDeleteAddressFromEditModal
     */
    public function createComponentAddressDeleteAddressFromEditModal()
    {
        return $this->addressModalContainer->getAddressDeleteAddressFromEditModalFactory()->create();
    }

    /**
     * @return AddressDeleteAddressFromListModal
     */
    public function createComponentAddressDeleteAddressFromListModal()
    {
        return $this->addressModalContainer->getAddressDeleteAddressFromListModalFactory()->create();
    }

    /**
     * @return AddressDeleteAddressJobModal
     */
    public function createComponentAddressDeleteAddressJobModal()
    {
        return $this->addressModalContainer->getAddressDeleteAddressJobModalFactory()->create();
    }

    /**
     * @return AddressDeleteBirthPersonModal
     */
    public function createComponentAddressDeleteBirthPersonModal()
    {
        return $this->addressModalContainer->getAddressDeleteBirthPersonModalFactory()->create();
    }

    /**
     * @return AddressDeleteDeathPersonModal
     */
    public function createComponentAddressDeleteDeathPersonModal()
    {
        return $this->addressModalContainer->getAddressDeleteDeathPersonModalFactory()->create();
    }

    /**
     * @return AddressDeleteGravedPersonModal
     */
    public function createComponentAddressDeleteGravedPersonModal()
    {
        return $this->addressModalContainer->getAddressDeleteGravedPersonModalFactory()->create();
    }

    /**
     * @return AddressDeleteJobModal
     */
    public function createComponentAddressDeleteJobModal()
    {
        return $this->addressModalContainer->getAddressDeleteJobModalFactory()->create();
    }

    /**
     * @return AddressDeletePersonAddressModal
     */
    public function createComponentAddressDeletePersonAddressModal()
    {
        return $this->addressModalContainer->getAddressDeletePersonAddressModalFactory()->create();
    }

    /**
     * @return AddressDeleteWeddingAddressModal
     */
    public function createComponentAddressDeleteWeddingAddressModal()
    {
        return $this->addressModalContainer->getAddressDeleteWeddingAddressModalFactory()->create();
    }

    /**
     * @return AddressDeleteWeddingModal
     */
    public function createComponentAddressDeleteWeddingModal()
    {
        return $this->addressModalContainer->getAddressDeleteWeddingModalFactory()->create();
    }
}
