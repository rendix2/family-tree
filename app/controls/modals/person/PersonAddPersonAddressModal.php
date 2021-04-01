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
use Rendix2\FamilyTree\App\Controls\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\PersonsAddressSettings;
use Rendix2\FamilyTree\App\Facades\Person2AddressFacade;


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
     * @var Person2AddressForm $person2AddressForm
     */
    private $person2AddressForm;

    /**
     * PersonAddPersonAddressModal constructor.
     *
     * @param ITranslator           $translator
     * @param PersonSettingsManager $personSettingsManager
     * @param AddressFacade         $addressFacade
     * @param Person2AddressManager $person2AddressManager
     * @param PersonManager         $personManager
     * @param Person2AddressFacade  $person2AddressFacade
     * @param Person2AddressForm    $person2AddressForm
     */
    public function __construct(
        ITranslator $translator,
        PersonSettingsManager $personSettingsManager,
        AddressFacade $addressFacade,
        Person2AddressManager $person2AddressManager,
        PersonManager $personManager,
        Person2AddressFacade $person2AddressFacade,
        Person2AddressForm $person2AddressForm
    ) {
        parent::__construct();

        $this->person2AddressForm = $person2AddressForm;

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
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $persons = $this->personSettingsManager->getAllPairs();
        $addresses = $this->addressFacade->getAllPairs();
        $personAddresses = $this->person2AddressManager->getPairsByLeft($personId);

        $this['personAddPersonAddressForm-_personId']->setDefaultValue($personId);
        $this['personAddPersonAddressForm-personId']->setDisabled()
            ->setItems($persons)
            ->setDefaultValue($personId);

        $this['personAddPersonAddressForm-addressId']->setItems($addresses)
            ->setDisabled($personAddresses);

        $presenter->template->modalName = 'personAddPersonAddress';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPersonAddressForm()
    {
        $personAddressSettings = new PersonsAddressSettings();

        $form = $this->person2AddressForm->create($personAddressSettings);

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
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPersonAddressFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs();

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
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->person2AddressManager->addGeneral($values);

        $addresses = $this->person2AddressFacade->getByLeftCached($values->personId);

        $presenter->template->addresses = $addresses;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_address_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('addresses');
        $presenter->redrawControl('flashes');
    }
}
