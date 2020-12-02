<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddAddressModal.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 0:41
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\AddressForm;

/**
 * Trait AddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddAddressModal
{
    /**
     * @return void
     */
    public function handleAddAddress()
    {
        $countries = $this->countryManager->getPairs('name');

        $this['addAddressForm-countryId']->setITems($countries);

        $this->template->modalName = 'addAddress';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
        $this->redrawControl('js');
    }

    /**
     * @param int $countryId countryId
     */
    public function handleSelectCountry($countryId)
    {
        if ($this->isAjax()) {
            if ($countryId) {
                $towns = $this->townManager->getPairsByCountry($countryId);

                $this['addAddressForm-townId']->setPrompt($this->getTranslator()->translate('address_select_town'))
                    ->setRequired('address_town_required')
                    ->setItems($towns);

                $countries = $this->countryManager->getPairs('name');

                $this['addAddressForm-countryId']->setItems($countries)->setDefaultValue($countryId);
            } else {
                $this['addAddressForm-townId']->setPrompt($this->getTranslator()->translate('address_select_town'))->setItems([]);
            }

            $this->redrawControl('addAddressFormWrappers');
            $this->redrawControl('js');
        }
    }

    /**
     * @return Form
     */
    public function createComponentAddAddressForm()
    {
        $formFactory = new AddressForm($this->getTranslator());

        $form = $formFactory->create($this);
        $form->addHidden('_townId');
        $form->onAnchor[] = [$this, 'addAddressFormAnchor'];
        $form->onValidate[] = [$this, 'addAddressFormValidate'];
        $form->onSuccess[] = [$this, 'saveAddressForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addAddressFormAnchor()
    {
    }

    /**
     * @param Form $form
     */
    public function addAddressFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->validate();

        $towns = $this->townManager->getPairsByCountry($countryControl->getValue());

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $form->removeComponent($form->getComponent('_townId'));
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveAddressForm(Form $form, ArrayHash $values)
    {
        $this->addressManager->add($values);

        $this->flashMessage('address_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl();
    }
}
