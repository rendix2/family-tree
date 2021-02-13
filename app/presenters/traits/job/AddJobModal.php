<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddJobModal.php
 * User: Tomáš Babický
 * Date: 25.11.2020
 * Time: 1:38
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Job;


use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\JobForm;
use Rendix2\FamilyTree\App\Forms\Settings\JobSettings;

/**
 * Trait AddJobModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Job
 */
trait AddJobModal
{
    /**
     * @return void
     */
    public function handleJobAddJob()
    {
        $towns = $this->townSettingsManager->getAllPairs();
        $addresses = $this->addressFacade->getPairsCached();

        $this['jobAddJobForm-townId']->setItems($towns);
        $this['jobAddJobForm-addressId']->setItems($addresses);

        $this->template->modalName = 'jobAddJob';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentJobAddJobForm()
    {
        $jobSettings = new JobSettings();

        $formFactory = new JobForm($this->translator, $jobSettings);

        $form = $formFactory->create();
        $form->onAnchor[] = [$this, 'jobAddJobFormAnchor'];
        $form->onValidate[] = [$this, 'jobAddJobFormValidate'];
        $form->onSuccess[] = [$this, 'jobAddJobFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @return void
     */
    public function jobAddJobFormAnchor()
    {
        $this->redrawControl('modal');
    }

    /**
     * @param Form $form
     */
    public function jobAddJobFormValidate(Form $form)
    {
        $towns = $this->townManager->getAllPairs();

        $townControl = $form->getComponent('townId');
        $townControl->setItems($towns)
            ->validate();

        $addresses = $this->addressFacade->getPairsCached();

        $addressControl = $form->getComponent('addressId');
        $addressControl->setItems($addresses)
            ->validate();
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function jobAddJobFormSuccess(Form $form, ArrayHash $values)
    {
        $this->jobManager->add($values);

        $this->flashMessage('job_added', self::FLASH_SUCCESS);

        $this->payload->showModal = false;

        $this->redrawControl('flashes');
    }
}