<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddCountryModal.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 0:33
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Town;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\CountryForm;

/**
 * Trait AddCountryModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Town
 */
trait TownAddCountryModal
{
    /**
     * @return void
     */
    public function handleTownAddCountry()
    {
        $this->template->modalName = 'townAddCountry';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownAddCountryForm()
    {
        $formFactory = new CountryForm($this->getTranslator());

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
        $this->redrawControl('modal');
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
        $this->countryManager->add($values);

        $this->payload->showModal = false;

        $countries = $this->countryManager->getPairsCached('name');

        $this['townForm-countryId']->setItems($countries);

        $this->flashMessage('country_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('townFormWrapper');
    }
}
