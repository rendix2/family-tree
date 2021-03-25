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
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Forms\Settings\PersonsAddressSettings;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressAddPersonAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressAddPersonAddressModal extends Control
{
    /**
     * @param int $addressId
     */
    public function handleAddressAddPersonAddress($addressId)
    {
        $addresses = $this->addressFacade->getPairsCached();
        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);
        $addressPersons = $this->person2AddressManager->getPairsByRight($addressId);

        $this['addressAddPersonAddressForm-_addressId']->setDefaultValue($addressId);
        $this['addressAddPersonAddressForm-addressId']->setItems($addresses)
            ->setDisabled()
            ->setValue($addressId);

        $this['addressAddPersonAddressForm-personId']->setItems($persons)
            ->setDisabled($addressPersons);

        $this->template->modalName = 'addressAddPersonAddress';

        $presenter->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddPersonAddressForm()
    {
        $personAddressSettings = new PersonsAddressSettings();

        $formFactory = new Person2AddressForm($this->translator, $personAddressSettings);

        $form = $formFactory->create();
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
        $persons = $this->personManager->getAllPairsCached($this->translator);

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
        $this->person2AddressManager->addGeneral((array) $values);

        $persons = $this->person2AddressFacade->getByRightCached($values->addressId);

        $this->template->persons = $persons;

        $presenter->payload->showModal = false;

        $this->flashMessage('person_address_added', BasePresenter::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('address_persons');
    }
}
