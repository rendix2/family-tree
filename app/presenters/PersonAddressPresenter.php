<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddressPresenter.php
 * User: Tomáš Babický
 * Date: 28.10.2020
 * Time: 19:14
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\DurationFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Forms\Settings\PersonsAddressSettings;
use Rendix2\FamilyTree\App\Managers\AddressManager;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\PersonAddress\PersonAddressDeletePersonAddressFromEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\PersonAddress\PersonAddressDeletePersonAddressFromListModal;

/**
 * Class PersonAddressPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class PersonAddressPresenter extends BasePresenter
{
    use PersonAddressDeletePersonAddressFromListModal;
    use PersonAddressDeletePersonAddressFromEditModal;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var AddressFilter $addressFilter
     */
    private $addressFilter;

    /**
     * @var AddressManager
     */
    private $addressManager;

    /**
     * @var DurationFilter $durationFilter
     */
    private $durationFilter;

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
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * PersonAddressPresenter constructor.
     *
     * @param AddressFacade $addressFacade
     * @param AddressFilter $addressFilter
     * @param AddressManager $addressManager
     * @param DurationFilter $durationFilter
     * @param Person2AddressFacade $person2AddressFacade
     * @param Person2AddressManager $person2AddressManager
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressFilter $addressFilter,
        AddressManager $addressManager,
        DurationFilter $durationFilter,
        Person2AddressFacade $person2AddressFacade,
        Person2AddressManager $person2AddressManager,
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->personFacade = $personFacade;
        $this->person2AddressFacade = $person2AddressFacade;

        $this->addressFilter = $addressFilter;
        $this->durationFilter = $durationFilter;
        $this->personFilter = $personFilter;

        $this->addressManager = $addressManager;
        $this->person2AddressManager = $person2AddressManager;
        $this->personManager = $personManager;

        $this->personSettingsManager = $personSettingsManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $relations = $this->person2AddressFacade->getAllCached();

        $this->template->relations = $relations;
    }

    /**
     * @param int $_addressId
     * @param string $formData
     */
    public function handlePersonAddressFormSelectAddress($_addressId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('PersonAddress:edit', null, $_addressId);
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['addressId']);

        if ($_addressId) {
            $selectedPersons = $this->person2AddressManager->getPairsByRight($_addressId);

            foreach ($selectedPersons as $key => $selectedPerson) {
                if ($selectedPerson === $this['personAddressForm-personId']->getValue()) {
                    unset($selectedPersons[$key]);

                    break;
                }
            }

            $this['personAddressForm-addressId']->setDefaultValue($_addressId);
            $this['personAddressForm-personId']->setDisabled($selectedPersons);
        } else {
            $persons = $this->personSettingsManager->getAllPairsCached($this->translator);
            $addresses = $this->addressFacade->getPairsCached();

            $this['personAddressForm-personId']->setItems($persons);
            $this['personAddressForm-addressId']->setItems($addresses);
        }

        $this['personAddressForm']->setDefaults((array) $formDataParsed);

        $this->payload->snippets = [
            $this['personAddressForm-personId']->getHtmlId() => (string) $this['personAddressForm-personId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }

    /**
     * @param int $_personId
     * @param string $formData
     */
    public function handlePersonAddressFormSelectPerson($_personId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('PersonAddress:edit', $_personId);
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['personId']);

        if ($_personId) {
            $selectedAddresses = $this->person2AddressManager->getPairsByLeft($_personId);

            foreach ($selectedAddresses as $key => $selectedAddress) {
                if ($selectedAddress === $this['personAddressForm-addressId']->getValue()) {
                    unset($selectedAddresses[$key]);

                    break;
                }
            }

            $this['personAddressForm-personId']->setDefaultValue($_personId);
            $this['personAddressForm-addressId']->setDisabled($selectedAddresses);
        } else {
            $persons = $this->personSettingsManager->getAllPairsCached($this->translator);
            $addresses = $this->addressFacade->getPairsCached();

            $this['personAddressForm-personId']->setItems($persons);
            $this['personAddressForm-addressId']->setItems($addresses);
        }

        $this['personAddressForm']->setDefaults((array) $formDataParsed);

        $this->payload->snippets = [
            $this['personAddressForm-addressId']->getHtmlId() => (string) $this['personAddressForm-addressId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }

    /**
     * @param int $personId
     * @param int $addressId
     */
    public function actionEdit($personId, $addressId)
    {
        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);
        $addresses = $this->addressFacade->getPairsCached();

        $this['personAddressForm-personId']->setItems($persons);
        $this['personAddressForm-addressId']->setItems($addresses);

        if ($personId && $addressId) {
            $relation = $this->person2AddressFacade->getByLeftAndRightCached($personId, $addressId);

            if (!$relation) {
                $this->error('Item not found.');
            }

            $selectedPersons = $this->person2AddressManager->getPairsByRight($addressId);
            $selectedAddresses = $this->person2AddressManager->getPairsByLeft($personId);

            foreach ($selectedAddresses as $key => $selectedAddress) {
                if ($selectedAddress === $relation->address->id) {
                    unset($selectedAddresses[$key]);

                    break;
                }
            }

            foreach ($selectedPersons as $key => $selectedPerson) {
                if ($selectedPerson === $relation->person->id) {
                    unset($selectedPersons[$key]);

                    break;
                }
            }

            $this['personAddressForm-personId']->setDisabled($selectedPersons)
                ->setDefaultValue($relation->person->id);

            $this['personAddressForm-addressId']->setDisabled($selectedAddresses)
                ->setDefaultValue($relation->address->id);

            $this['personAddressForm-dateSince']->setDefaultValue($relation->duration->dateSince);
            $this['personAddressForm-dateTo']->setDefaultValue($relation->duration->dateTo);
            $this['personAddressForm-untilNow']->setDefaultValue($relation->duration->untilNow);

            $this['personAddressForm']->setDefaults((array) $relation);
        } elseif ($personId && !$addressId) {
            $person = $this->personSettingsManager->getByPrimaryKeyCached($personId);

            if (!$person) {
                $this->error('Item not found.');
            }

            $selectedAddresses = $this->person2AddressManager->getPairsByLeft($personId);

            foreach ($selectedAddresses as $key => $selectedAddress) {
                if ($selectedAddress === $this['personAddressForm-addressId']->getValue()) {
                    unset($selectedAddresses[$key]);

                    break;
                }
            }

            $this['personAddressForm-personId']->setDefaultValue($personId);
            $this['personAddressForm-addressId']->setDisabled($selectedAddresses);
        } elseif (!$personId && $addressId) {
            $address = $this->addressManager->getByPrimaryKeyCached($addressId);

            if (!$address) {
                $this->error('Item not found.');
            }

            $selectedPersons = $this->person2AddressManager->getPairsByRight($addressId);

            foreach ($selectedPersons as $key => $selectedPerson) {
                if ($selectedPerson === $this['personAddressForm-personId']->getValue()) {
                    unset($selectedPersons[$key]);

                    break;
                }
            }

            $this['personAddressForm-addressId']->setDefaultValue($addressId);
            $this['personAddressForm-personId']->setDisabled($selectedPersons);
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddressForm()
    {
        $personAddressSettings = new PersonsAddressSettings();
        $personAddressSettings->selectAddressHandle = $this->link('personAddressFormSelectAddress!');
        $personAddressSettings->selectPersonHandle = $this->link('personAddressFormSelectPerson!');

        $formFactory = new Person2AddressForm($this->translator, $personAddressSettings);

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'personAddressFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $personId = $this->getParameter('personId');
        $addressId = $this->getParameter('addressId');

        if ($personId !== null && $addressId !== null) {
            $this->person2AddressManager->updateGeneral($personId, $addressId, (array) $values);

            $this->flashMessage('person_address_saved', self::FLASH_SUCCESS);

            $this->redirect('PersonAddress:edit', $values->personId, $values->addressId);
        } else {
            $this->person2AddressManager->addGeneral((array) $values);

            $this->flashMessage('person_address_added', self::FLASH_SUCCESS);

            $this->redirect('PersonAddress:edit', $values->personId, $values->addressId);
        }
    }
}
