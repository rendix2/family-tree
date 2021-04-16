<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Job;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Model\Managers\JobManager;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class JobDeleteJobFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job
 */
class JobDeleteJobFromEditModal extends Control
{
    /**
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var JobFacade $jobFacade
     */
    private $jobFacade;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * @var JobFilter $jobFilter
     */
    private $jobFilter;

    /**
     * JobDeleteJobFromEditModal constructor.
     *
     * @param DeleteModalForm $deleteModalForm
     * @param JobFacade $jobFacade
     * @param JobFilter $jobFilter
     * @param JobManager $jobManager
     */
    public function __construct(
        DeleteModalForm $deleteModalForm,
        JobFacade $jobFacade,
        JobFilter $jobFilter,
        JobManager $jobManager
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;
        $this->jobFacade = $jobFacade;
        $this->jobManager = $jobManager;
        $this->jobFilter = $jobFilter;
    }

    public function render()
    {
        $this['jobDeleteJobFromEditForm']->render();
    }

    /**
     * @param int $jobId
     */
    public function handleJobDeleteJobFromEdit($jobId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Job:edit', $presenter->getParameter('id'));
        }

        $this['jobDeleteJobFromEditForm']->setDefaults(['jobId' => $jobId]);

        $jobFilter = $this->jobFilter;

        $jobModalItem = $this->jobFacade->select()->getCachedManager()->getByPrimaryKey($jobId);

        $presenter->template->modalName = 'jobDeleteJobFromEdit';
        $presenter->template->jobModalItem = $jobFilter($jobModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentJobDeleteJobFromEditForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'jobDeleteJobFromEditFormYesOnClick'];
        $deleteModalFormSettings->httpRedirect = true;

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function jobDeleteJobFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Job:edit', $presenter->getParameter('id'));
        }

        try {
            $this->jobManager->delete()->deleteByPrimaryKey($values->jobId);

            $presenter->flashMessage('job_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redirect('Job:default');
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
