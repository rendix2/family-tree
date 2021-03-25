<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddTownModal.php
 * User: Tomáš Babický
 * Date: 09.12.2020
 * Time: 0:37
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressAddTownModal extends Control
{
    /**
     * @return void
     */
    public function handleAddressAddTown()
    {
        $presenter = $this->presenter;

        $countries = $this->countryManager->getPairs('name');

        $this['addressAddTownForm-countryId']->setItems($countries);

        $presenter->template->modalName = 'addressAddTown';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
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
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
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
        $presenter = $this->presenter;

        $this->townManager->add($values);

        $towns = $this->townSettingsManager->getPairsCached('name');

        $this['addressForm-townId']->setItems($towns);

        $presenter->payload->showModal = false;
        $presenter->payload->snippets = [
            $this['addressForm-townId']->getHtmlId() => (string) $this['addressForm-townId']->getControl(),
        ];

        $this->flashMessage('town_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
    }
}
