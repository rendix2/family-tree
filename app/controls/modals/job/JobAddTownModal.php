<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobAddTownModal.php
 * User: Tomáš Babický
 * Date: 03.12.2020
 * Time: 0:49
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\TownForm;
use Rendix2\FamilyTree\App\Model\Managers\CountryManager;
use Rendix2\FamilyTree\App\Model\Managers\TownManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class JobAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job
 */
class JobAddTownModal extends Control
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
     * JobAddTownModal constructor.
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

    public function render()
    {
        $this['jobAddTownForm']->render();
    }

    /**
     * @return void
     */
    public function handleJobAddTown()
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Job:edit', $presenter->getParameter('id'));
        }

        $countries = $this->countryManager->select()->getCachedManager()->getPairs('name');

        $this['jobAddTownForm-countryId']->setItems($countries);

        $presenter->template->modalName = 'jobAddTown';

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentJobAddTownForm()
    {
        $form = $this->townForm->create();

        $form->onAnchor[] = [$this, 'jobAddTownFormAnchor'];
        $form->onValidate[] = [$this, 'jobAddTownFormValidate'];
        $form->onSuccess[] = [$this, 'jobAddTownFormSuccess'];

        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function jobAddTownFormAnchor()
    {
        $presenter = $this->presenter;

        $presenter->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function jobAddTownFormValidate(Form $form)
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
    public function jobAddTownFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Job:edit', $presenter->getParameter('id'));
        }

        $this->townManager->insert()->insert((array) $values);

        $towns = $this->townManager->select()->getSettingsCachedManager()->getAllPairs();

        $presenter['jobForm-townId']->setItems($towns);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('town_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('jobFormWrapper');
        $presenter->redrawControl('jsFormCallback');
    }
}