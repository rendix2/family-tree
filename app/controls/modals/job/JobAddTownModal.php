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

/**
 * Trait JobAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Job
 */
class JobAddTownModal extends Control
{
    /**
     * @return void
     */
    public function handleJobAddTown()
    {
        $countries = $this->countryManager->getPairs('name');

        $this['jobAddTownForm-countryId']->setItems($countries);

        $this->template->modalName = 'jobAddTown';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
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
        $this->redrawControl('modal');
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
        $this->townManager->add($values);

        $towns = $this->townSettingsManager->getAllPairsCached();

        $this['jobForm-townId']->setItems($towns);

        $this->payload->showModal = false;

        $this->flashMessage('town_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('jobFormWrapper');
        $this->redrawControl('jsFormCallback');
    }
}