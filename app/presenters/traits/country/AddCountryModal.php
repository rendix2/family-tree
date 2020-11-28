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
    public function handleAddCountry()
    {
        $this->template->modalName = 'addCountry';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    public function createComponentAddCountryForm()
    {
        $formFactory = new CountryForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addCountryFormAnchor'];
        $form->onValidate[] = [$this, 'addCountryFormValidate'];
        $form->onSuccess[] = [$this, 'saveCountryForm'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addCountryFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addCountryFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveCountryForm(Form $form, ArrayHash $values)
    {
        $this->countryManager->add($values);

        $this->flashMessage('country_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        // $this->redrawControl();
    }
}
