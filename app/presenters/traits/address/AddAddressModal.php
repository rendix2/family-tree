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
use Rendix2\FamilyTree\App\Forms\Settings\AddressSettings;

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
    public function handleAddressAddAddress()
    {
        $countries = $this->countryManager->getPairs('name');

        $this['addressAddAddressForm-countryId']->setItems($countries);

        $this->template->modalName = 'addressAddAddress';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
        $this->redrawControl('jsFormCallback');
    }

    /**
     * @param int $countryId countryId
     * @param string $formData
     */
    public function handleAddAddressSelectCountry($countryId, $formData)
    {
        if ($this->isAjax()) {
            if ($countryId) {
                $towns = $this->townSettingsManager->getPairsByCountry($countryId);

                $this['addressAddAddressForm-townId']->setItems($towns);

                $countries = $this->countryManager->getPairs('name');

                $this['addressAddAddressForm-countryId']->setItems($countries)
                    ->setDefaultValue($countryId);
            } else {
                $this['addressAddAddressForm-townId']->setItems([]);
            }

            $this->redrawControl('addressAddFormWrapper');
            $this->redrawControl('jsFormCallback');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddAddressForm()
    {
        $addressSettings = new AddressSettings();
        $addressSettings->selectCountryHandle = $this->link('addAddressSelectCountry!');

        $formFactory = new AddressForm($this->translator, $addressSettings);

        $form = $formFactory->create();
        $form->addHidden('_townId');
        $form->onValidate[] = [$this, 'addressAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'addressAddAddressFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function addressAddAddressFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->validate();

        $towns = $this->townManager->getPairsByCountry($countryControl->getValue());

        $townHiddenControl = $form->getComponent('_townId');

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addressAddAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $this->addressManager->add($values);

        $this->flashMessage('address_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl('flashes');
        $this->redrawControl('jsFormCallback');
    }
}
