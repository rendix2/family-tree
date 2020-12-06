<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddAddressModal.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 0:41
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Wedding;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\AddressForm;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Settings\AddressSettings;

/**
 * Trait AddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Wedding
 */
trait WeddingAddAddressModal
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
            $towns = $this->townManager->getPairsByCountry($countryId);

            $this['weddingAddAddressForm-townId']->setItems($towns);
            $this['weddingAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue($countryId);
        } else {
            $this['weddingAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue(null);

            $this['weddingAddAddressForm-townId']->setItems([]);
        }

        $this['weddingAddAddressForm']->setDefaults($formDataParsed);

        $this->redrawControl('weddingAddAddressFormWrapper');
        $this->redrawControl('js');
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingAddAddressForm()
    {
        $addressSettings = new AddressSettings();
        $addressSettings->selectCountryHandle = $this->link('weddingAddAddressSelectCountry!');

        $formFactory = new AddressForm($this->getTranslator(), $addressSettings);

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

        $this->redrawControl('flashes');
        $this->redrawControl('js');
        $this->redrawControl('weddingFormWrapper');
    }
}
