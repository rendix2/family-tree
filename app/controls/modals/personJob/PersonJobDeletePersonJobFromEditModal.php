<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJobDeletePersonJobFromEditModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:54
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\PersonJob;

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
 * Class PersonJobDeletePersonJobFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\PersonJob
 */
class PersonJobDeletePersonJobFromEditModal extends Control
{
    /**
     * @var JobFilter $jobFilter
     */
    private $jobFilter;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

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
     * PersonJobDeletePersonJobFromEditModal constructor.
     *
     * @param JobFilter $jobFilter
     * @param JobFacade $jobFacade
     * @param PersonFacade $personFacade
     * @param PersonFilter $personFilter
     * @param Person2JobManager $person2JobManager
     * @param ITranslator $translator
     */
    public function __construct(
        JobFilter $jobFilter,
        JobFacade $jobFacade,
        PersonFacade $personFacade,
        PersonFilter $personFilter,
        Person2JobManager $person2JobManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->jobFilter = $jobFilter;
        $this->jobFacade = $jobFacade;
        $this->personFacade = $personFacade;
        $this->personFilter = $personFilter;
        $this->person2JobManager = $person2JobManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['personJobDeletePersonJobFromEditForm']->render();
    }

    /**
     * @param int $personId
     * @param int $jobId
     */
    public function handlePersonJobDeletePersonJobFromEdit($personId, $jobId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('PersonJob:edit', $presenter->getParameter('personId'), $presenter->getParameter('jobId'));
        }

        $this['personJobDeletePersonJobFromEditForm']->setDefaults(
            [
                'personId' => $personId,
                'jobId' => $jobId
            ]
        );

        $jobFilter = $this->jobFilter;
        $personFilter = $this->personFilter;

        $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
        $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

        $presenter->template->modalName = 'personJobDeletePersonJobFromEdit';
        $presenter->template->jobModalItem = $jobFilter($jobModalItem);
        $presenter->template->personModalItem = $personFilter($personModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentPersonJobDeletePersonJobFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personJobDeletePersonJobFromEditFormYesOnClick'], true);
        $form->addHidden('personId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personJobDeletePersonJobFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('PersonJob:edit', $presenter->getParameter('personId'), $presenter->getParameter('jobId'));
        }

        try {
            $this->person2JobManager->deleteByLeftIdAndRightId($values->personId, $values->jobId);

            $presenter->flashMessage('person_job_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redirect('PersonJob:default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);

                $presenter->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
