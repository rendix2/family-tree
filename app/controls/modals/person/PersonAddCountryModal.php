<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddCounntryModal.php
 * User: Tomáš Babický
 * Date: 30.03.2021
 * Time: 10:22
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\CountryForm;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddCountryModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddCountryModal extends Control
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
     * PersonAddCountryModal constructor.
     *
     * @param CountryForm    $countryForm
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
        $this['personAddCountryForm']->render();
    }

    /**
     * @return void
     */
    public function handlePersonAddCountry()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $presenter->template->modalName = 'personAddCountry';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddCountryForm()
    {
        $formFactory = $this->countryForm;

        $form = $formFactory->create();

        $form->onAnchor[] = [$this, 'personAddCountryFormAnchor'];
        $form->onValidate[] = [$this, 'personAddCountryFormValidate'];
        $form->onSuccess[] = [$this, 'personAddCountryFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddCountryFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddCountryFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddCountryFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->countryManager->insert()->insert((array) $values);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('country_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
    }
}