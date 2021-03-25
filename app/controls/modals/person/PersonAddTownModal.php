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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;
use Rendix2\FamilyTree\App\Managers\CountryManager;
use Rendix2\FamilyTree\App\Managers\TownManager;
use Rendix2\FamilyTree\App\Managers\TownSettingsManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class PersonAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Person
 */
class PersonAddTownModal extends Control
{
    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * @var CountryManager $countryManager
     */
    private $countryManager;

    /**
     * @var TownManager $townManager
     */
    private $townManager;

    /**
     * @var TownSettingsManager $townSettingsManager
     */
    private $townSettingsManager;

    /**
     * PersonAddTownModal constructor.
     *
     * @param ITranslator $translator
     * @param CountryManager $countryManager
     * @param TownManager $townManager
     * @param TownSettingsManager $townSettingsManager
     */
    public function __construct(
        ITranslator $translator,
        CountryManager $countryManager,
        TownManager $townManager,
        TownSettingsManager $townSettingsManager
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->countryManager = $countryManager;
        $this->townManager = $townManager;
        $this->townSettingsManager = $townSettingsManager;
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

        if (!$this->presenter->isAjax()) {
            $this->presenter->redirect('Person:edit', $this->getParameter('id'));
        }

        $countries = $this->countryManager->getPairs('name');

        $this['personAddTownForm-countryId']->setItems($countries);

        $this->presenter->template->modalName = 'personAddTown';

        $this->presenter->payload->showModal = true;

        $this->presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonAddTownForm()
    {
        $formFactory = new TownForm($this->translator);

        $form = $formFactory->create();
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
        $this->presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function personAddTownFormValidate(Form $form)
    {
        $countries = $this->countryManager->getPairs('name');

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

        $this->townManager->add($values);

        $towns = $this->townSettingsManager->getAllPairsCached();

        $this['personForm-birthTownId']->setItems($towns);
        $this['personForm-deathTownId']->setItems($towns);
        $this['personForm-gravedTownId']->setItems($towns);

        $this->presenter->flashMessage('town_added', BasePresenter::FLASH_SUCCESS);

        $this->presenter->payload->showModal = false;

        $this->presenter->redrawControl('flashes');
        $this->presenter->redrawControl('personFormWrapper');
        $this->presenter->redrawControl('jsFormCallback');
    }
}
