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
use Rendix2\FamilyTree\App\Controls\Forms\CountryForm;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class AddressAddCountryModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressAddCountryModal extends Control
{
    /**
     * @var CountryForm $countryForm
     */
    private $countryForm;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * AddressAddCountryModal constructor.
     *
     * @param CountryForm      $countryForm
     * @param CountryManager $countryManager
     */
    public function __construct(
        CountryForm $countryForm,
        CountryManager $countryManager
    ) {
        parent::__construct();

        $this->countryForm = $countryForm;

        $this->countryManager = $countryManager;
    }

    public function render()
    {
        $this['addressAddCountryForm']->render();
    }

    /**
     * @return void
     */
    public function handleAddressAddCountry()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $presenter->template->modalName = 'addressAddCountry';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressAddCountryForm()
    {
        $form = $this->countryForm->create();

        $form->elementPrototype->setAttribute('class', 'ajax');

        $form->onAnchor[] = [$this, 'addressAddCountryFormAnchor'];
        $form->onValidate[] = [$this, 'addressAddCountryFormValidate'];
        $form->onSuccess[] = [$this, 'addressAddCountryFormSuccess'];

        return $form;
    }

    /**
     * @return void
     */
    public function addressAddCountryFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this->countryManager->insert()->insert((array) $values);

        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $presenter['addressForm-countryId']->setItems($countries);

        $presenter->payload->showModal = false;
        $presenter->payload->snippets = [
            $presenter['addressForm-countryId']->getHtmlId() => (string) $presenter['addressForm-countryId']->getControl(),
        ];

        $presenter->flashMessage('country_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('jsFormCallback');
        $presenter->redrawControl('flashes');
    }
}
