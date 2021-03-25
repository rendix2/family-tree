<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobAddPersonJobModal.php
 * User: Tomáš Babický
 * Date: 03.12.2020
 * Time: 1:21
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;
use Rendix2\FamilyTree\App\Forms\Settings\PersonJobSettings;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;

/**
 * Class JobAddPersonJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job
 */
class JobAddPersonJobModal extends Control
{
    /**
     * @param int $jobId
     *
     * @return void
     */
    public function handleJobAddPersonJob($jobId)
    {
        $presenter = $this->presenter;

        $persons = $this->personSettingsManager->getAllPairs($this->translator);
        $jobs = $this->jobSettingsManager->getAllPairs($this->translator);
        $jobsPersons = $this->person2JobManager->getPairsByRight($jobId);

        $this['jobAddPersonJobForm-personId']->setItems($persons)
            ->setDisabled($jobsPersons);

        $this['jobAddPersonJobForm-_jobId']->setDefaultValue($jobId);
        $this['jobAddPersonJobForm-jobId']->setItems($jobs)
            ->setDisabled()
            ->setDefaultValue($jobId);

        $presenter->template->modalName = 'jobAddPersonJob';

        $presenter->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentJobAddPersonJobForm()
    {
        $personJobSettings = new PersonJobSettings();

        $formFactory = new Person2JobForm($this->translator, $personJobSettings);

        $form = $formFactory->create();
        $form->addHidden('_jobId');
        $form->onValidate[] = [$this, 'jobAddPersonJobFormValidate'];
        $form->onSuccess[] = [$this, 'jobAddPersonJobFormSuccess'];
        $form->elementPrototype->setAttribute('class', 'ajax');

        return $form;
    }

    /**
     * @param Form $form
     */
    public function jobAddPersonJobFormValidate(Form $form)
    {
        $persons = $this->personManager->getAllPairs($this->translator);

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->validate();

        $jobs = $this->jobManager->getAllPairs($this->translator);

        $jobHiddenControl = $form->getComponent('_jobId');

        $jobControl = $form->getComponent('jobId');
        $jobControl->setItems($jobs)
            ->setValue($jobHiddenControl->getValue())
            ->validate();

        $form->removeComponent($jobHiddenControl);
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function jobAddPersonJobFormSuccess(Form $form, ArrayHash $values)
    {
        $presenter = $this->presenter;

        $this->person2JobManager->addGeneral($values);

        $persons = $this->person2JobFacade->getByRightCached($values->jobId);

        $presenter->template->persons = $persons;

        $presenter->payload->showModal = false;

        $this->flashMessage('person_job_added', BasePresenter::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('persons');
    }
}
