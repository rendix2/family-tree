<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPersonAddressModal.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:02
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Forms\Settings\PersonsAddressSettings;
use Rendix2\FamilyTree\App\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddPersonAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddPersonAddressModal extends Control
{

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var PersonSettingsManager
     */
    private $personSettingsManager;

    /**
     * @var AddressFacade $addressFacade
     */
    private $addressFacade;

    /**
     * @var Person2AddressManager $person2AddressManager
     */
    private $person2AddressManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var Person2AddressFacade $person2AddressFacade
     */
    private $person2AddressFacade;

    /**
     * PersonAddPersonAddressModal constructor.
     *
     * @param ITranslator $translator
     * @param PersonSettingsManager $personSettingsManager
     * @param AddressFacade $addressFacade
     * @param Person2AddressManager $person2AddressManager
     * @param PersonManager $personManager
     * @param Person2AddressFacade $person2AddressFacade
     */
    public function __construct(
        ITranslator $translator,
        PersonSettingsManager $personSettingsManager,
        AddressFacade $addressFacade,
        Person2AddressManager $person2AddressManager,
        PersonManager $personManager,
        Person2AddressFacade $person2AddressFacade
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->personSettingsManager = $personSettingsManager;
        $this->addressFacade = $addressFacade;
        $this->person2AddressManager = $person2AddressManager;
        $this->personManager = $personManager;
        $this->person2AddressFacade = $person2AddressFacade;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddPersonAddressForm']->render();
    }

    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddPersonAddress($personId)
    {
        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairs($this->translator);
        $addresses = $this->addressFacade->getAllPairs();
        $personAddresses = $this->person2AddressManager->getPairsByLeft($personId);

        $this['personAddPersonAddressForm-_personId']->setDefaultValue($personId);
        $this['personAddPersonAddressForm-personId']->setDisabled()
            ->setItems($persons)
            ->setDefaultValue($personId);

        $this['personAddPersonAddressForm-addressId']->setItems($addresses)
            ->setDisabled($personAddresses);

        $this->presenter->template->modalName = 'personAddPersonAddress';

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPersonAddressForm()
    {
        $personAddressSettings = new PersonsAddressSettings();

        $formFactory = new Person2AddressForm($this->translator, $personAddressSettings);

        $form = $formFactory->create();
        $form->addHidden('_personId');
        $form->onAnchor[] = [$this, 'personAddPersonAddressFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPersonAddressFormValidate'];
        $form->onSuccess[] = [$this, 'personAddPersonAddressFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddPersonAddressFormAnchor()
    {
        $this->presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPersonAddressFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->translator);

        $personHiddenControl = $form->getComponent('_personId');

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->setValue($personHiddenControl->getValue())
            ->validate();

        $addresses = $this->addressFacade->getAllPairs();

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->validate();

        $form->removeComponent($personHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddPersonAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $this->person2AddressManager->addGeneral($values);

        $addresses = $this->person2AddressFacade->getByLeftCached($values->personId);

        $this->presenter->template->addresses = $addresses;

        $this->presenter->payload->showModal = false;

        $this->presenter->flashMessage('person_address_added', BasePresenter::FLASH_SUCCESS);

        $this->presenter->redrawControl('addresses');
        $this->presenter->redrawControl('flashes');
    }
}