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
use Rendix2\FamilyTree\App\Controls\Forms\Helpers\FormJsonDataParser;
use Rendix2\FamilyTree\App\Controls\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\PersonsAddressSettings;
use Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\Container\PersonAddressModalContainer;
use Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\PersonAddressDeletePersonAddressFromEditModal;
use Rendix2\FamilyTree\App\Controls\Modals\PersonAddress\PersonAddressDeletePersonAddressFromListModal;
use Rendix2\FamilyTree\App\Model\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Managers\AddressManager;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Managers\Person2AddressManager;

/**
 * Class PersonAddressPresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class PersonAddressPresenter extends BasePresenter
{
    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var AddressManager
     */
    private $addressManager;

    /**
     * @var PersonAddressModalContainer $personAddressModalContainer
     */
    private $personAddressModalContainer;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

    /**
     * @var Person2AddressForm $person2AddressForm
     */
    private $person2AddressForm;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var PersonManager $personManager
     */
    protected $personManager;

    /**
     * PersonAddressPresenter constructor.
     *
     * @param AddressFacade               $addressFacade
     * @param AddressManager              $addressManager
     * @param Person2AddressFacade        $person2AddressFacade
     * @param Person2AddressForm          $person2AddressForm
     * @param PersonAddressModalContainer $personAddressModalContainerCached
     * @param PersonManager               $personManager
     * @param Person2AddressManager       $person2AddressManager
     */
    public function __construct(
        AddressFacade $addressFacade,
        AddressManager $addressManager,
        Person2AddressFacade $person2AddressFacade,
        Person2AddressForm $person2AddressForm,
        PersonAddressModalContainer $personAddressModalContainerCached,
        PersonManager $personManager,
        Person2AddressManager $person2AddressManager
    ) {
        parent::__construct();

        $this->personAddressModalContainer = $personAddressModalContainerCached;

        $this->person2AddressForm = $person2AddressForm;

        $this->addressFacade = $addressFacade;
        $this->person2AddressFacade = $person2AddressFacade;

        $this->addressManager = $addressManager;
        $this->person2AddressManager = $person2AddressManager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $relations = $this->person2AddressFacade->select()->getCachedManager()->getAll();

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
            $selectedPersons = $this->person2AddressManager->select()->getManager()->getPairsByRight($_addressId);

            foreach ($selectedPersons as $key => $selectedPerson) {
                if ($selectedPerson === $this['personAddressForm-personId']->getValue()) {
                    unset($selectedPersons[$key]);

                    break;
                }
            }

            $this['personAddressForm-addressId']->setDefaultValue($_addressId);
            $this['personAddressForm-personId']->setDisabled($selectedPersons);
        } else {
            $persons = $this->personManager->select()->getSettingsCachedManager()->getAllPairs();
            $addresses = $this->addressFacade->select()->getCachedManager()->getAllPairs();

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
            $selectedAddresses = $this->person2AddressManager->select()->getManager()->getPairsByLeft($_personId);

            foreach ($selectedAddresses as $key => $selectedAddress) {
                if ($selectedAddress === $this['personAddressForm-addressId']->getValue()) {
                    unset($selectedAddresses[$key]);

                    break;
                }
            }

            $this['personAddressForm-personId']->setDefaultValue($_personId);
            $this['personAddressForm-addressId']->setDisabled($selectedAddresses);
        } else {
            $persons = $this->personManager->select()->getSettingsCachedManager()->getAllPairs();
            $addresses = $this->addressFacade->getManager()->select()->getCachedManager()->getPairs();

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
        $persons = $this->personManager->select()->getSettingsCachedManager()->getAllPairs();
        $addresses = $this->addressFacade->select()->getCachedManager()->getAllPairs();

        $this['personAddressForm-personId']->setItems($persons);
        $this['personAddressForm-addressId']->setItems($addresses);

        if ($personId && $addressId) {
            $relation = $this->person2AddressFacade->select()->getCachedManager()->getByLeftAndRightKey($personId, $addressId);

            if (!$relation) {
                $this->error('Item not found.');
            }

            $selectedPersons = $this->person2AddressManager->select()->getManager()->getPairsByRight($addressId);
            $selectedAddresses = $this->person2AddressManager->select()->getManager()->getPairsByLeft($personId);

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
            $person = $this->personManager->select()->getSettingsCachedManager()->getByPrimaryKey($personId);

            if (!$person) {
                $this->error('Item not found.');
            }

            $selectedAddresses = $this->person2AddressManager->select()->getManager()->getPairsByLeft($personId);

            foreach ($selectedAddresses as $key => $selectedAddress) {
                if ($selectedAddress === $this['personAddressForm-addressId']->getValue()) {
                    unset($selectedAddresses[$key]);

                    break;
                }
            }

            $this['personAddressForm-personId']->setDefaultValue($personId);
            $this['personAddressForm-addressId']->setDisabled($selectedAddresses);
        } elseif (!$personId && $addressId) {
            $address = $this->addressManager->select()->getCachedManager()->getByPrimaryKey($addressId);

            if (!$address) {
                $this->error('Item not found.');
            }

            $selectedPersons = $this->person2AddressManager->select()->getManager()->getPairsByRight($addressId);

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

        $form = $this->person2AddressForm->create($personAddressSettings);
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
            $this->person2AddressManager->update()->updateByLeftAndRight($personId, $addressId, (array) $values);

            $this->flashMessage('person_address_saved', self::FLASH_SUCCESS);

            $this->redirect('PersonAddress:edit', $values->personId, $values->addressId);
        } else {
            $this->person2AddressManager->insert()->insert((array) $values);

            $this->flashMessage('person_address_added', self::FLASH_SUCCESS);

            $this->redirect('PersonAddress:edit', $values->personId, $values->addressId);
        }
    }

    /**
     * @return PersonAddressDeletePersonAddressFromEditModal
     */
    protected function createComponentPersonAddressDeletePersonAddressFromEditModal()
    {
        return $this->personAddressModalContainer->getPersonAddressDeletePersonAddressFromEditModalFactory()->create();
    }

    /**
     * @return PersonAddressDeletePersonAddressFromListModal
     */
    protected function createComponentPersonAddressDeletePersonAddressFromListModal()
    {
        return $this->personAddressModalContainer->getPersonAddressDeletePersonAddressFromListModalFactory()->create();
    }
}
