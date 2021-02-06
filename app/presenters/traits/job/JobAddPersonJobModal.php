<?php
/**
 *
 * Created by PhpStorm.
 * Filename: JobAddPersonJobModal.php
 * User: Tomáš Babický
 * Date: 03.12.2020
 * Time: 1:21
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Job;

use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\Person2JobForm;

/**
 * Trait JobAddPersonJobModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Job
 */
trait JobAddPersonJobModal
{
    /**
     * @param int $jobId
     *
     * @return void
     */
    public function handleJobAddPersonJob($jobId)
    {
        $persons = $this->personManager->getAllPairs($this->getTranslator());
        $jobs = $this->jobManager->getAllPairs($this->getTranslator());
        $jobsPersons = $this->person2JobManager->getPairsByRight($jobId);

        $this['jobAddPersonJobForm-personId']->setItems($persons)
            ->setDisabled($jobsPersons);

        $this['jobAddPersonJobForm-_jobId']->setDefaultValue($jobId);
        $this['jobAddPersonJobForm-jobId']->setItems($jobs)
            ->setDisabled()
            ->setDefaultValue($jobId);

        $this->template->modalName = 'jobAddPersonJob';

        $this->payload->showModal = true;

        $this->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentJobAddPersonJobForm()
    {
        $formFactory = new Person2JobForm($this->getTranslator());

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
        $persons = $this->personManager->getAllPairs($this->getTranslator());

        $personControl = $form->getComponent('personId');
        $personControl->setItems($persons)
            ->validate();

        $jobs = $this->jobManager->getAllPairs($this->getTranslator());

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
        $this->person2JobManager->addGeneral($values);

        $persons = $this->person2JobFacade->getByRightCached($values->jobId);

        $this->template->persons = $persons;

        $this->payload->showModal = false;

        $this->flashMessage('person_job_added', self::FLASH_SUCCESS);

        $this->redrawControl('flashes');
        $this->redrawControl('persons');
    }
}