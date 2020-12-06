<?php
/**
 *
 * Created by PhpStorm.
 * Filename: CountryAddAddressModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:00
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Country;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\AddressForm;
use Rendix2\FamilyTree\App\Forms\Settings\AddressSettings;

/**
 * Trait CountryAddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Country
 */
trait CountryAddAddressModal
{
    /**
     * @param int $countryId
     *
     * @param $formData
     * @return void
     */
    public function handleCountryAddAddress($countryId, $formData)
    {
        $countries = $this->countryManager->getPairs('name');
        $towns = $this->townManager->getPairsByCountry($countryId);

        $this['countryAddAddressForm-_countryId']->setDefaultValue($countryId);
        $this['countryAddAddressForm-countryId']->setItems($countries)
            ->setDisabled()
            ->setDefaultValue($countryId);

        $this['countryAddAddressForm-townId']->setItems($towns);

        $this->template->modalName = 'countryAddAddress';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentCountryAddAddressForm()
    {
        $addressSettings = new AddressSettings();

        $formFactory = new AddressForm($this->getTranslator(), $addressSettings);

        $form = $formFactory->create();
        $form->addHidden('_countryId');
        $form->addHidden('_townId');
        $form->onValidate[] = [$this, 'countryAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'countryAddAddressFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function countryAddAddressFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

        $countryHiddenControl = $form->getComponent('_countryId');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->setValue($countryHiddenControl->getValue())
            ->validate();

        $towns = $this->townManager->getPairsByCountry($countryHiddenControl->getValue());

        $townHiddenControl = $form->getComponent('_townId');

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $form->removeComponent($countryHiddenControl);
        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function countryAddAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $this->addressManager->add($values);

        $addresses = $this->addressFacade->getByCountryId($values->countryId);

        $this->template->addresses = $addresses;

        $this->payload->showModal = false;

        $this->flashMessage('address_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('addresses');
    }
}