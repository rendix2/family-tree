<?php
/**
 *
 * Created by PhpStorm.
 * Filename: WeddingAddCountryModal.php
 * User: Tomáš Babický
 * Date: 30.03.2021
 * Time: 10:07
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Wedding;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\CountryForm;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class WeddingAddCountryModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Wedding
 */
class WeddingAddCountryModal extends Control
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
     * WeddingAddCountryModal constructor.
     *
     * @param CountryForm $countryForm
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
        $this['weddingAddCountryForm']->render();
    }

    /**
     * @return void
     */
    public function handleWeddingAddCountry()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Wedding:edit', $presenter->getParameter('id'));
        }

        $presenter->template->modalName = 'weddingAddCountry';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentWeddingAddCountryForm()
    {
        $form = $this->countryForm->create();

        $form->onAnchor[] = [$this, 'weddingAddCountryFormAnchor'];
        $form->onValidate[] = [$this, 'weddingAddCountryFormValidate'];
        $form->onSuccess[] = [$this, 'weddingAddCountryFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function weddingAddCountryFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function weddingAddCountryFormValidate(Form $form)
    {
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function weddingAddCountryFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Wedding:edit', $presenter->getParameter('id'));
        }

        $this->countryManager->add($values);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('country_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
    }
}
