<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressAddCountryModal.php
 * User: Tomáš Babický
 * Date: 09.12.2020
 * Time: 0:37
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\CountryForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressAddCountryModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressAddCountryModal extends Control
{
    /**
     * @return void
     */
    public function handleAddressAddCountry()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $this->redirect('Address:edit', $this->getParameter('id'));
        }

        $this->template->modalName = 'addressAddCountry';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddCountryForm()
    {
        $formFactory = new CountryForm($this->translator);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'addressAddCountryFormAnchor'];
        $form->onValidate[] = [$this, 'addressAddCountryFormValidate'];
        $form->onSuccess[] = [$this, 'addressAddCountryFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function addressAddCountryFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function addressAddCountryFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function addressAddCountryFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->countryManager->add($values);

        $countries = $this->countryManager->getPairsCached('name');

        $this['addressForm-countryId']->setItems($countries);

        $this->payload->showModal = false;
        $this->payload->snippets = [
            $this['addressForm-countryId']->getHtmlId() => (string) $this['addressForm-countryId']->getControl(),
        ];

        $this->flashMessage('country_added', BasePresenter::FLASH_SUCCESS);

        $this->redrawControl('jsFormCallback');
        $this->redrawControl('flashes');
    }
}
