<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddTownModal.php
 * User: Tomáš Babický
 * Date: 09.12.2020
 * Time: 0:37
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Address;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;

/**
 * Trait AddressAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Address
 */
trait AddressAddTownModal
{
    /**
     * @return void
     */
    public function handleAddressAddTown()
    {
        $countries = $this->countryManager->getPairs('name');

        $this['addressAddTownForm-countryId']->setItems($countries);

        $this->template->modalName = 'addressAddTown';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddTownForm()
    {
        $formFactory = new TownForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addressAddTownFormAnchor'];
        $form->onValidate[] = [$this, 'addressAddTownFormValidate'];
        $form->onSuccess[] = [$this, 'addressAddTownFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addressAddTownFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addressAddTownFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addressAddTownFormSuccess(Form $form, ArrayHash $values)
    {
        $this->townManager->add($values);

        $towns = $this->townSettingsManager->getPairsCached('name');

        $this['addressForm-townId']->setItems($towns);

        $this->payload->showModal = false;
        $this->payload->snippets = [
            $this['addressForm-townId']->getHtmlId() => (string) $this['addressForm-townId']->getControl(),
        ];

        $this->flashMessage('town_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
    }
}
