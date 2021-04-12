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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\PersonsAddressSettings;
use Rendix2\FamilyTree\App\Model\Facades\Person2AddressFacade;
use Rendix2\FamilyTree\App\Model\Facades\AddressFacade;
use Rendix2\FamilyTree\App\Model\Managers\Person2AddressManager;
use Rendix2\FamilyTree\App\Model\Managers\PersonManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;


/**
 * Class PersonAddPersonAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddPersonAddressModal extends Control
{
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
     * @param AddressFacade         $addressFacade
     * @param Person2AddressManager $person2AddressManager
     * @param PersonManager         $personManager
     * @param Person2AddressFacade  $person2AddressFacadeCached
     * @param Person2AddressForm    $person2AddressForm
     */
    public function __construct(
        AddressFacade $addressFacade,
        Person2AddressManager $person2AddressManager,
        PersonManager $personManager,
        Person2AddressFacade $person2AddressFacadeCached,
        Person2AddressForm $person2AddressForm
    ) {
        parent::__construct();

        $this->person2AddressForm = $person2AddressForm;

        $this->addressFacade = $addressFacade;
        $this->person2AddressManager = $person2AddressManager;
        $this->personManager = $personManager;
        $this->person2AddressFacade = $person2AddressFacadeCached;
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

        $persons = $this->personManager->select()->getSettingsManager()->getAllPairs();
        $addresses = $this->addressFacade->getManager()->select()->getManager()->getAllPairs();
        $personAddresses = $this->person2AddressManager->select()->getManager()->getPairsByLeft($personId);

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
        $persons = $this->personManager->select()->getManager()->getAllPairs();

        $personHiddenControl = $form->getComponent('_personId');

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->setValue($personHiddenControl->getValue())
            ->validate();

        $addresses = $this->addressFacade->select()->getManager()->getAllPairs();

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

        $this->person2AddressManager->insert()->insert((array) $values);

        $addresses = $this->person2AddressFacade->select()->getCachedManager()->getByLeftKey($values->personId);

        $presenter->template->addresses = $addresses;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('person_address_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('addresses');
        $presenter->redrawControl('flashes');
    }
}
