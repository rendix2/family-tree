<?php

/**
 *
 * Created by PhpStorm.
 * Filename: JobDeletePersonModal.php
 * User: Tomáš Babický
 * Date: 28.10.2020
 * Time: 16:42
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class JobDeletePersonJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job
 */
class JobDeletePersonJobModal extends Control
{
    /**
     * @param int $jobId
     * @param int $personId
     */
    public function handleJobDeletePersonJob($personId, $jobId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['jobDeletePersonJobForm']->setDefaults(
                [
                    'personId' => $personId,
                    'jobId' => $jobId
                ]
            );

            $personFilter = $this->personFilter;
            $jobFilter = $this->jobFilter;

            $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);
            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);

            $presenter->template->modalName = 'jobDeletePersonJob';
            $presenter->template->jobModalItem = $jobFilter($jobModalItem);
            $presenter->template->personModalItem = $personFilter($personModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentJobDeletePersonJobForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

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
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            try {
                $this->person2JobManager->deleteByLeftIdAndRightId($values->personId, $values->jobId);

                $persons = $this->person2JobManager->getAllByRightJoined($values->jobId);

                $presenter->template->persons = $persons;

                $presenter->payload->showModal = false;

                $presenter->flashMessage('person_job_deleted', BasePresenter::FLASH_SUCCESS);

                $presenter->redrawControl('persons');
            } catch (ForeignKeyConstraintViolationException $e) {
                if ($e->getCode() === 1451) {
                    $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);
                } else {
                    Debugger::log($e, ILogger::EXCEPTION);
                }
            } finally {
                $presenter->redrawControl('flashes');
            }
        } else {
            $presenter->redirect('Job:edit', $values->jobId);
        }
    }
}