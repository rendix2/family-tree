<?php

/**
 *
 * Created by PhpStorm.
 * Filename: JobDeletePersonModal.php
 * User: Tomáš Babický
 * Date: 28.10.2020
 * Time: 16:42
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Job;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait JobDeletePersonModal
 */
trait JobDeletePersonJobModal
{
    /**
     * @param int $jobId
     * @param int $personId
     */
    public function handleJobDeletePersonJob($personId, $jobId)
    {
        if ($this->isAjax()) {
            $this['jobDeletePersonJobForm']->setDefaults(
                [
                    'personId' => $personId,
                    'jobId' => $jobId
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $jobFilter = new JobFilter($this->getHttpRequest());

            $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $this->template->modalName = 'jobDeletePersonJob';
            $this->template->jobModalItem = $jobFilter($jobModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentJobDeletePersonJobForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'jobDeleteJobPersonFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function jobDeleteJobPersonFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            try {
                $this->person2JobManager->deleteByLeftIdAndRightId($values->personId, $values->jobId);

                $persons = $this->person2JobManager->getAllByRightJoined($values->jobId);

                $this->template->persons = $persons;

                $this->payload->showModal = false;

                $this->flashMessage('person_job_deleted', self::FLASH_SUCCESS);

                $this->redrawControl('persons');
            } catch (ForeignKeyConstraintViolationException $e) {
                if ($e->getCode() === 1451) {
                    $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);
                } else {
                    Debugger::log($e, ILogger::EXCEPTION);
                }
            } finally {
                $this->redrawControl('flashes');
            }
        } else {
            $this->redirect('Job:edit', $values->jobId);
        }
    }
}
