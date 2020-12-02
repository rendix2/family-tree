<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddAddressModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:42
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Town;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\AddressForm;

trait TownAddAddressModal
{
    /**
     * @param int $countryId
     * @param int $townId
     *
     * @return void
     */
    public function handleTownAddAddress($countryId, $townId)
    {
        $countries = $this->countryManager->getPairs('name');
        $towns = $this->townManager->getPairsByCountry($countryId);

        $this['townAddAddressForm-_countryId']->setDefaultValue($countryId);

        $this['townAddAddressForm-countryId']->setITems($countries)
            ->setDisabled()
            ->setDefaultValue($countryId);

        $this['townAddAddressForm-_townId']->setDefaultValue($townId);

        $this['townAddAddressForm-townId']->setITems($towns)
            ->setDisabled()
            ->setDefaultValue($townId);

        $this->template->modalName = 'townAddAddress';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @param int $countryId countryId
     */
    public function handleSelectCountry($countryId)
    {
    }

    /**
     * @return Form
     */
    public function createComponentTownAddAddressForm()
    {
        $formFactory = new AddressForm($this->getTranslator());

        $form = $formFactory->create($this);
        $form->addHidden('_countryId');
        $form->addHidden('_townId');
        $form->onAnchor[] = [$this, 'townAddAddressFormAnchor'];
        $form->onValidate[] = [$this, 'townAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'townAddAddressFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function townAddAddressFormAnchor()
    {
    }

    /**
     * @param Form $form
     */
    public function townAddAddressFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

        $countryHiddenControl = $form->getComponent('_countryId');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->setValue($countryHiddenControl->getValue())
            ->validate();

        $towns = $this->townManager->getPairsByCountry($countryControl->getValue());

        $townHiddenControl = $form->getComponent('_townId');

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->setValue($townHiddenControl->getValue())
            ->validate();

        $form->removeComponent($countryHiddenControl);
        $form->removeComponent($townHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function townAddAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $this->addressManager->add($values);

        $addresses = $this->addressFacade->getByTownIdCached($values->townId);

        $this->template->addresses = $addresses;

        $this->payload->showModal = false;

        $this->flashMessage('address_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('addresses');
    }
}
