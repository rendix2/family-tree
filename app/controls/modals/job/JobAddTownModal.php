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
use Rendix2\FamilyTree\App\Forms\TownForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class JobAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job
 */
class JobAddTownModal extends Control
{
    /**
     * @return void
     */
    public function handleJobAddTown()
    {
        $presenter = $this->presenter;

        $countries = $this->countryManager->getPairs('name');

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
        $formFactory = new TownForm($this->translator);

        $form = $formFactory->create();
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
        $countries = $this->countryManager->getPairs('name');

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

        $this->townManager->add($values);

        $towns = $this->townSettingsManager->getAllPairsCached();

        $this['jobForm-townId']->setItems($towns);

        $presenter->payload->showModal = false;

        $presenter->flashMessage('town_added', BasePresenter::FLASH_SUCCESS);

        $presenter->redrawControl('flashes');
        $presenter->redrawControl('jobFormWrapper');
        $presenter->redrawControl('jsFormCallback');
    }
}