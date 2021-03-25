<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobAddAddressModal.php
 * User: Tomáš Babický
 * Date: 03.12.2020
 * Time: 0:46
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\AddressForm;
use Rendix2\FamilyTree\App\Forms\FormJsonDataParser;
use Rendix2\FamilyTree\App\Forms\Settings\AddressSettings;

/**
 * Class JobAddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job
 */
class JobAddAddressModal extends Control
{
    /**
     * @return void
     */
    public function handleJobAddAddress()
    {
        $presenter = $this->presenter;

        $countries = $this->countryManager->getPairs('name');

        $this['jobAddAddressForm-countryId']->setItems($countries);

        $this->template->modalName = 'jobAddAddress';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
        $this->redrawControl('js');
    }

    /**
     * @param int $countryId countryId
     * @param string $formData
     */
    public function handleJobAddAddressSelectCountry($countryId, $formData)
    {
        $presenter = $this->presenter;

        if (!$this->isAjax()) {
            $this->redirect('Job:edit', $this->getParameter('id'));
        }

        $countries = $this->countryManager->getPairs('name');

        $formDataParsed = FormJsonDataParser::parse($formData);
        unset($formDataParsed['townId']);

        if ($countryId) {
            $this['jobAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue($countryId);

            $towns = $this->townSettingsManager->getPairsByCountry($countryId);

            $this['jobAddAddressForm-townId']
                ->setItems($towns);
        } else {
            $this['jobAddAddressForm-countryId']->setItems($countries)
                ->setDefaultValue(null);

            $this['jobAddAddressForm-townId']->setItems([]);
        }

        $this['jobAddAddressForm']->setDefaults($formDataParsed);

        $this->payload->snippets = [
            $this['jobAddAddressForm-townId']->getHtmlId() => (string) $this['jobAddAddressForm-townId']->getControl(),
        ];

        $this->redrawControl('jsFormCallback');
    }

    /**
     * @return Form
     */
    protected function createComponentJobAddAddressForm()
    {
        $addressSettings = new AddressSettings();
        $addressSettings->selectCountryHandle = $this->link('jobAddAddressSelectCountry!');

        $formFactory = new AddressForm($this->translator, $addressSettings);

        $form = $formFactory->create();
        $form->addHidden('_townId');
        $form->onValidate[] = [$this, 'jobAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'jobAddAddressFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function jobAddAddressFormValidate(Form $form)
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
    public function jobAddAddressFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->addressManager->add($values);

        $addresses = $this->addressFacade->getPairsCached();

        $this['jobForm-addressId']->setItems($addresses);

        $this->payload->showModal = false;

        $this->flashMessage('address_added', self::FLASH_SUCCESS);

        $this->payload->snippets = [
            $this['jobForm-addressId']->getHtmlId() => (string) $this['jobForm-addressId']->getControl(),
        ];

        $this->redrawControl('flashes');
        $this->redrawControl('jsFormCallback');
    }
}
