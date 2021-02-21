<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingAddAddressModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:25
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\AddressForm;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Settings\AddressSettings;

/**
 * Class WeddingAddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Wedding
 */
class WeddingAddAddressModal extends Control
{
    /**
     * @return void
     */
    public function handleWeddingAddAddress()
    {
        if (!$this->isAjax()) {
            $this->redirect('Wedding:edit', $this->getParameter('id'));
        }

        $countries = $this->countryManager->getPairs('name');

        $this['weddingAddAddressForm-countryId']->setItems($countries);

        $this->template->modalName = 'weddingAddAddress';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
        $this->redrawControl('js');
    }

    /**
     * @param int $countryId countryId
     * @param string $formData
     */
    public function handleWeddingAddAddressSelectCountry($countryId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('Wedding:edit', $this->getParameter('id'));
        }

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['townId']);

        $countries = $this->countryManager->getPairs('name');

        if ($countryId) {
            $towns = $this->townSettingsManager->getPairsByCountry($countryId);

            $this['weddingAddAddressForm-townId']->setItems($towns);
            $this['weddingAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue($countryId);
        } else {
            $this['weddingAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue(null);

            $this['weddingAddAddressForm-townId']->setItems([]);
        }

        $this['weddingAddAddressForm']->setDefaults($formDataParsed);

        $this->payload->snippets = [
            $this['weddingAddAddressForm-townId']->getHtmlId() => (string) $this['weddingAddAddressForm-townId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingAddAddressForm()
    {
        $addressSettings = new AddressSettings();
        $addressSettings->selectCountryHandle = $this->link('weddingAddAddressSelectCountry!');

        $formFactory = new AddressForm($this->translator, $addressSettings);

        $form = $formFactory->create();
        $form->addHidden('_townId');
        $form->onValidate[] = [$this, 'weddingAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'weddingAddAddressFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function weddingAddAddressFormValidate(Form $form)
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
    public function weddingAddAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $this->addressManager->add($values);

        $addresses = $this->addressFacade->getPairsCached();

        $this['weddingForm-addressId']->setItems($addresses);

        $this->payload->showModal = false;

        $this->flashMessage('address_added', self::FLASH_SUCCESS);

        $this->payload->snippets = [
            $this['weddingForm-addressId']->getHtmlId() => (string) $this['weddingForm-addressId']->getControl(),
        ];

        $this->redrawControl('flashes');
        $this->redrawControl('jsFormCallback');
    }
}