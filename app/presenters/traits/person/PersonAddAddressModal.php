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
     */
    public function handleSelectCountry($countryId)
    {
        if ($this->isAjax()) {
            if ($countryId) {
                $towns = $this->townManager->getPairsByCountry($countryId);

                $this['personAddAddressForm-townId']->setPrompt(
                    $this->getTranslator()
                        ->translate('address_select_town')
                )
                    ->setRequired('address_town_required')
                    ->setItems($towns);

                $countries = $this->countryManager->getPairs('name');

                $this['personAddAddressForm-countryId']->setItems($countries)
                    ->setDefaultValue($countryId);
            } else {
                $this['personAddAddressForm-townId']->setPrompt(
                    $this->getTranslator()
                        ->translate('address_select_town')
                )
                    ->setItems([]);
            }

            $this->redrawControl('addAddressFormWrapper');
            $this->redrawControl('js');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddAddressForm()
    {
        $formFactory = new AddressForm($this->getTranslator());

        $form = $formFactory->create($this);
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

        $addresses = $this->addressFacade->getPairs();

        $this['form-birthAddressId']->setItems($addresses);
        $this['form-deathAddressId']->setItems($addresses);
        $this['form-gravedAddressId']->setItems($addresses);

        $this->payload->showModal = false;

        $this->flashMessage('address_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('formWrapper');
    }
}
