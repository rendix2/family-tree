<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddPersonAddressModal.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 1:30
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\Person2AddressForm;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;

/**
 * Trait PersonAddPersonAddressModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddPersonAddressModal
{
    /**
     * @param int $personId
     *
     * @return void
     */
    public function handlePersonAddPersonAddress($personId)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());
        $addresses = $this->addressFacade->getPairs();

        $this['personAddPersonAddressForm-_personId']->setDefaultValue($personId);
        $this['personAddPersonAddressForm-personId']->setDisabled()->setItems($persons)->setDefaultValue($personId);
        $this['personAddPersonAddressForm-addressId']->setItems($addresses);

        $this->template->modalName = 'personAddPersonAddress';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddPersonAddressForm()
    {
        $formFactory = new Person2AddressForm($this->getTranslator());

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
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddPersonAddressFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());

        $personHiddenControl = $form->getComponent('_personId');

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->setValue($personHiddenControl->getValue())
            ->validate();

        $addresses = $this->addressFacade->getPairs();

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

        $this->template->addresses = $addresses;

        $this->payload->showModal = false;

        $this->flashMessage('person_address_added', self::FLASH_SUCCESS);

        $this->redrawControl('addresses');
        $this->redrawControl('flashes');
    }
}
