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
    public function createComponentPersonAddPersonAddressForm()
    {
        $formFactory = new Person2AddressForm($this->getTranslator());

        $form = $formFactory->create();
        $form->addHidden('_personId');
        $form->onAnchor[] = [$this, 'personAddPersonAddressFormAnchor'];
        $form->onValidate[] = [$this, 'personAddPersonAddressFormValidate'];
        $form->onSuccess[] = [$this, 'personSavePersonAddressForm'];

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

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons);
        $personControl->setValue($form->getComponent('_personId')->getValue());
        $personControl->validate();

        $form->removeComponent($form->getComponent('_personId'));

        $addresses = $this->addressFacade->getPairs();

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses);
        $addressControl->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personSavePersonAddressForm(Form $form, ArrayHash $values)
    {
        $this->person2AddressManager->addGeneral($values);

        $this->flashMessage('person_address_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl();
    }
}
