<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobAddAddressModal.php
 * User: Tomáš Babický
 * Date: 03.12.2020
 * Time: 0:46
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Job;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\AddressForm;

/**
 * Trait JobAddAddressModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Job
 */
trait JobAddAddressModal
{
    /**
     * @return void
     */
    public function handleJobAddAddress()
    {
        $countries = $this->countryManager->getPairs('name');

        $this['jobAddAddressForm-countryId']->setItems($countries);

        $this->template->modalName = 'jobAddAddress';

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

                $this['jobAddAddressForm-townId']->setPrompt(
                    $this->getTranslator()
                        ->translate('address_select_town')
                )
                    ->setRequired('address_town_required')
                    ->setItems($towns);

                $countries = $this->countryManager->getPairs('name');

                $this['jobAddAddressForm-countryId']->setItems($countries)
                    ->setDefaultValue($countryId);
            } else {
                $this['jobAddAddressForm-townId']->setPrompt(
                    $this->getTranslator()->translate('address_select_town')
                )
                    ->setItems([]);
            }

            $this->redrawControl('jobAddAddressFormWrapper');
            $this->redrawControl('js');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentJobAddAddressForm()
    {
        $formFactory = new AddressForm($this->getTranslator());

        $form = $formFactory->create($this);
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
        $this->addressManager->add($values);

        $addresses = $this->addressFacade->getPairsCached();

        $this['jobForm-addressId']->setItems($addresses);

        $this->payload->showModal = false;

        $this->flashMessage('address_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('jobFormWrapper');
    }
}
