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
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Facades\PersonFacade;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\Person2JobManager;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
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
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobFilter $jobFilter
     */
    private $jobFilter;

    /**
     * @var PersonFacade $personFacade
     */
    private $personFacade;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var Person2JobManager $person2JobManager
     */
    private $person2JobManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * JobDeletePersonJobModal constructor.
     *
     * @param JobFacade $jobFacade
     * @param JobFilter $jobFilter
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param Person2JobManager $person2JobManager
     * @param ITranslator $translator
     */
    public function __construct(
        JobFacade $jobFacade,
        JobFilter $jobFilter,
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        Person2JobManager $person2JobManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->jobFacade = $jobFacade;
        $this->jobFilter = $jobFilter;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->person2JobManager = $person2JobManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['jobDeletePersonJobForm']->render();
    }

    /**
     * @param int $jobId
     * @param int $personId
     */
    public function handleJobDeletePersonJob($personId, $jobId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Job:edit', $presenter->getParameter('id'));
        }

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

        if (!$presenter->isAjax()) {
            $presenter->redirect('Job:edit', $presenter->getParameter('id'));
        }

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
    }
}
