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

use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\CountryForm;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddCountryModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddCountryModal extends \Nette\Application\UI\Control
{
    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * PersonAddCountryModal constructor.
     *
     * @param CountryManager $countryManager
     * @param ITranslator $translator
     */
    public function __construct(
        CountryManager $countryManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->countryManager = $countryManager;
        $this->translator = $translator;
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
        $formFactory = new CountryForm($this->translator);

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

        $this->countryManager->add($values);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('country_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
    }
}