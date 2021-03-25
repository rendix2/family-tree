<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddCountryModal.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 0:33
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\CountryForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownAddCountryModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownAddCountryModal extends Control
{
    /**
     * @return void
     */
    public function handleTownAddCountry()
    {
        $presenter = $this->presenter;

        $presenter->template->modalName = 'townAddCountry';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownAddCountryForm()
    {
        $formFactory = new CountryForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'townAddCountryFormAnchor'];
        $form->onValidate[] = [$this, 'townAddCountryFormValidate'];
        $form->onSuccess[] = [$this, 'townAddCountryFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function townAddCountryFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function townAddCountryFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function townAddCountryFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->countryManager->add($values);

        $presenter->payload->showModal = false;

        $countries = $this->countryManager->getPairsCached('name');

        $this['townForm-countryId']->setItems($countries);

        $presenter->flashMessage('country_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('townFormWrapper');
        $presenter->redrawControl('jsFormCallback');
    }
}
