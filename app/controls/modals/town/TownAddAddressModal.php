<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownAddAddressModal.php
 * User: Tomáš Babický
 * Date: 02.12.2020
 * Time: 1:42
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\AddressForm;
use Rendix2\FamilyTree\App\Forms\Settings\AddressSettings;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownAddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownAddAddressModal extends Control
{
    /**
     * @param int $countryId
     * @param int $townId
     *
     * @return void
     */
    public function handleTownAddAddress($countryId, $townId)
    {
        $presenter = $this->presenter;

        $countries = $this->countryManager->getPairs('name');
        $towns = $this->townSettingsManager->getPairsByCountry($countryId);

        $this['townAddAddressForm-_countryId']->setDefaultValue($countryId);

        $this['townAddAddressForm-countryId']->setItems($countries)
            ->setDisabled()
            ->setDefaultValue($countryId);

        $this['townAddAddressForm-_townId']->setDefaultValue($townId);

        $this['townAddAddressForm-townId']->setItems($towns)
            ->setDisabled()
            ->setDefaultValue($townId);

        $presenter->template->modalName = 'townAddAddress';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownAddAddressForm()
    {
        $addressSettings = new AddressSettings();

        $formFactory = new AddressForm($this->translator, $addressSettings);

        $form = $formFactory->create();
        $form->addHidden('_countryId');
        $form->addHidden('_townId');
        $form->onValidate[] = [$this, 'townAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'townAddAddressFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
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
        $presenter = $this->presenter;

        $this->addressManager->add($values);

        $addresses = $this->addressFacade->getByTownIdCached($values->townId);

        $presenter->template->addresses = $addresses;

        $presenter->payload->showModal = false;

        $presenter->flashMessage('address_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('addresses');
    }
}
