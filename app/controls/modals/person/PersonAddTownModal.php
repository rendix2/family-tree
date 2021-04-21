<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonAddTownModa.php
 * User: Tomáš Babický
 * Date: 20.02.2021
 * Time: 13:06
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Person;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\TownForm;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddTownModal extends Control
{
    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var TownForm $townForm
     */
    private $townForm;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * PersonAddTownModal constructor.
     *
     * @param CountryManager $countryManager
     * @param TownForm       $townForm
     * @param TownManager    $townManager
     */
    public function __construct(
        CountryManager $countryManager,
        TownForm $townForm,
        TownManager $townManager
    ) {
        parent::__construct();

        $this->townForm = $townForm;
        $this->countryManager = $countryManager;
        $this->townManager = $townManager;
    }

    public function __destruct()
    {
        $this->townForm = null;
        $this->countryManager = null;
        $this->townManager = null;
    }

    /**
     * @return void
     */
    public function render()
    {
        $this['personAddTownForm']->render();
    }

    /**
     * @return void
     */
    public function handlePersonAddTown()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $this['personAddTownForm-countryId']->setItems($countries);

        $presenter->template->modalName = 'personAddTown';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddTownForm()
    {
        $form = $this->townForm->create();

        $form->onAnchor[] = [$this, 'personAddTownFormAnchor'];
        $form->onValidate[] = [$this, 'personAddTownFormValidate'];
        $form->onSuccess[] = [$this, 'personAddTownFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function personAddTownFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddTownFormValidate(Form $form)
    {
        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $countryControl = $form->getComponent('countryId');
        $countryControl->setItems($countries)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function personAddTownFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Person:edit', $presenter->getParameter('id'));
        }

        $this->townManager->insert()->insert((array) $values);

        $towns = $this->townManager->select()->getCachedManager()->getAllPairs();

        $presenter['personForm-birthTownId']->setItems($towns);
        $presenter['personForm-deathTownId']->setItems($towns);
        $presenter['personForm-gravedTownId']->setItems($towns);

        $presenter->flashMessage('town_added', BasePresenter::FLASH_SUCCESS);

        $presenter->payload->showModal = false;

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('personFormWrapper');
        $presenter->redrawControl('jsFormCallback');
    }
}
