<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobAddTownModal.php
 * User: Tomáš Babický
 * Date: 03.12.2020
 * Time: 0:49
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Job;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\TownForm;

/**
 * Trait JobAddTownModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Job
 */
trait JobAddTownModal
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
        $formFactory = new TownForm($this->getTranslator());

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

        $towns = $this->townManager->getAllPairsCached();

        $this['jobForm-townId']->setItems($towns);

        $this->payload->showModal = false;

        $this->flashMessage('town_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('jobFormWrapper');
        $this->redrawControl('js');
    }
}