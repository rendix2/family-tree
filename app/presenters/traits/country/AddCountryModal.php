<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddCountryModal.php
 * User: Tomáš Babický
 * Date: 26.11.2020
 * Time: 0:33
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Country;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\CountryForm;

/**
 * Trait AddCountryModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Country
 */
trait AddCountryModal
{
    /**
     * @return void
     */
    public function handleCountryAddCountry()
    {
        $this->template->modalName = 'countryAddCountry';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentCountryAddCountryForm()
    {
        $formFactory = new CountryForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'countryAddCountryFormAnchor'];
        $form->onValidate[] = [$this, 'countryAddCountryFormValidate'];
        $form->onSuccess[] = [$this, 'countryAddCountryFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function countryAddCountryFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function countryAddCountryFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function countryAddCountryFormSuccess(Form $form, ArrayHash $values)
    {
        $this->countryManager->add($values);

        $this->payload->showModal = false;

        $this->flashMessage('country_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
    }
}
