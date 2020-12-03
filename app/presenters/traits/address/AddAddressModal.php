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

                $this['addressAddAddressForm-townId']->setPrompt(
                    $this->getTranslator()
                        ->translate('address_select_town')
                )
                    ->setRequired('address_town_required')
                    ->setItems($towns);

                $countries = $this->countryManager->getPairs('name');

                $this['addressAddAddressForm-countryId']->setItems($countries)
                    ->setDefaultValue($countryId);
            } else {
                $this['addressAddAddressForm-townId']->setPrompt(
                    $this->getTranslator()
                        ->translate('address_select_town')
                )->setItems([]);
            }

            $this->redrawControl('addressAddFormWrapper');
            $this->redrawControl('js');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddAddressForm()
    {
        $formFactory = new AddressForm($this->getTranslator());

        $form = $formFactory->create($this);
        $form->addHidden('_townId');
        $form->onAnchor[] = [$this, 'addressAddAddressFormAnchor'];
        $form->onValidate[] = [$this, 'addressAddAddressFormValidate'];
        $form->onSuccess[] = [$this, 'addressAddAddressFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addressAddAddressFormAnchor()
    {
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

        $this->redrawControl();
    }
}
