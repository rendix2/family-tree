<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddAddressMlda.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 22:39
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\AddressForm;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Settings\AddressSettings;

/**
 * Trait PersonAddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonAddAddressModal
{
    /**
     * @return void
     */
    public function handlePersonAddAddress()
    {
        $countries = $this->countryManager->getPairs('name');

        $this['personAddAddressForm-countryId']->setItems($countries);

        $this->template->modalName = 'personAddAddress';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
        $this->redrawControl('js');
    }

    /**
     * @param int $countryId countryId
     * @param string $formData
     */
    public function handlePersonAddAddressSelectCountry($countryId, $formData)
    {
        if (!$this->isAjax()) {
            $this->redirect('Person:edit', $this->getParameter('id'));
        }

        $countries = $this->countryManager->getPairs('name');

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['townId']);

        if ($countryId) {
            $this['personAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue($countryId);

            $towns = $this->townManager->getPairsByCountry($countryId);

            $this['personAddAddressForm-townId']->setItems($towns);
        } else {
            $this['personAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue(null);

            $this['personAddAddressForm-townId']->setItems([]);
        }

        $this['personAddAddressForm']->setDefaults($formDataParsed);

        $this->redrawControl('personAddAddressFormWrapper');
        $this->redrawControl('js');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddAddressForm()
    {
        $addressSettings = new AddressSettings();
        $addressSettings->selectCountryHandle = $this->link('personAddAddressSelectCountry!');

        $formFactory = new AddressForm($this->getTranslator(), $addressSettings);

        $form = $formFactory->create();
        $form->addHidden('_townId');
        $form->onValidate[] = [$this, 'personAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'personAddAddressFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }


    /**
     * @param Form $form
     */
    public function personAddAddressFormValidate(Form $form)
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
    public function personAddAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $this->addressManager->add($values);

        $addresses = $this->addressFacade->getAllPairs();

        $this['personForm-birthAddressId']->setItems($addresses);
        $this['personForm-deathAddressId']->setItems($addresses);
        $this['personForm-gravedAddressId']->setItems($addresses);

        $this->payload->showModal = false;

        $this->flashMessage('address_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('personFormWrapper');
        $this->redrawControl('js');
    }
}
