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
use Rendix2\FamilyTree\App\Controls\Forms\CountryForm;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class TownAddCountryModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownAddCountryModal extends Control
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
     * TownAddCountryModal constructor.
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
        $this['townAddCountryForm']->render();
    }


    /**
     * @return void
     */
    public function handleTownAddCountry()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $presenter->template->modalName = 'townAddCountry';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownAddCountryForm()
    {
        $form = $this->countryForm->create();

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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this->countryManager->insert()->insert((array) $values);

        $presenter->payload->showModal = false;

        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $presenter['townForm-countryId']->setItems($countries);

        $presenter->flashMessage('country_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('townFormWrapper');
        $presenter->redrawControl('jsFormCallback');
    }
}
