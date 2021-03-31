<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddPersonAddressModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 3:04
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\PersonsAddressSettings;
use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;


use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressAddPersonAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressAddPersonAddressModal extends Control
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
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var Person2AddressForm $person2AddressForm
     */
    private $person2AddressForm;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * AddressAddPersonAddressModal constructor.
     *
     * @param AddressFacade         $addressFacade
     * @param Person2AddressFacade  $person2AddressFacade
     * @param Person2AddressForm    $person2AddressForm
     * @param Person2AddressManager $person2AddressManager
     * @param PersonManager         $personManager
     * @param PersonSettingsManager $personSettingsManager
     * @param ITranslator           $translator
     */
    public function __construct(
        AddressFacade $addressFacade,
        Person2AddressFacade $person2AddressFacade,
        Person2AddressForm $person2AddressForm,
        Person2AddressManager $person2AddressManager,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->addressFacade = $addressFacade;
        $this->person2AddressFacade = $person2AddressFacade;
        $this->person2AddressForm = $person2AddressForm;
        $this->person2AddressManager = $person2AddressManager;
        $this->personManager = $personManager;
        $this->personSettingsManager = $personSettingsManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['addressAddPersonAddressForm']->render();
    }

    /**
     * @param int $addressId
     */
    public function handleAddressAddPersonAddress($addressId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $addresses = $this->addressFacade->getPairsCached();
        $persons = $this->personSettingsManager->getAllPairsCached();
        $addressPersons = $this->person2AddressManager->getPairsByRight($addressId);

        $this['addressAddPersonAddressForm-_addressId']->setDefaultValue($addressId);
        $this['addressAddPersonAddressForm-addressId']->setItems($addresses)
            ->setDisabled()
            ->setValue($addressId);

        $this['addressAddPersonAddressForm-personId']->setItems($persons)
            ->setDisabled($addressPersons);

        $presenter->template->modalName = 'addressAddPersonAddress';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddPersonAddressForm()
    {
        $personAddressSettings = new PersonsAddressSettings();

        $form = $this->person2AddressForm->create($personAddressSettings);

        $form->addHidden('_addressId');

        $form->onValidate[] = [$this, 'addressAddPersonAddressFormValidate'];
        $form->onSuccess[] = [$this, 'addressAddPersonAddressFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function addressAddPersonAddressFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairsCached();

        $countryControl = $form->getComponent('personId');
        $countryControl->setItems($persons)
            ->validate();

        $addresses = $this->addressFacade->getPairsCached();

        $addressHiddenControl = $form->getComponent('_addressId');

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->setValue($addressHiddenControl->getValue())
            ->validate();

        $form->removeComponent($addressHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addressAddPersonAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this->person2AddressManager->addGeneral((array) $values);

        $persons = $this->person2AddressFacade->getByRightCached($values->addressId);

        $presenter->template->persons = $persons;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_address_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('address_persons');
    }
}
