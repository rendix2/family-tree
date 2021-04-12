<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteJobModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 1:30
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Town;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Controls\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Controls\Forms\Settings\DeleteModalFormSettings;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Model\Managers\JobManager;
use Rendix2\FamilyTree\App\Model\Facades\JobFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class TownDeleteJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Town
 */
class TownDeleteJobModal extends Control
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
     * @var JobFilter $jobFilter
     */
    private $jobFilter;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * TownDeleteJobModal constructor.
     *
     * @param JobFacade       $jobFacade
     * @param JobFilter       $jobFilter
     * @param DeleteModalForm $deleteModalForm
     * @param JobManager      $jobManager
     */
    public function __construct(
        JobFacade $jobFacade,
        JobFilter $jobFilter,

        DeleteModalForm $deleteModalForm,

        JobManager $jobManager
    ) {
        parent::__construct();

        $this->deleteModalForm = $deleteModalForm;

        $this->jobFacade = $jobFacade;
        $this->jobFilter = $jobFilter;
        $this->jobManager = $jobManager;
    }

    public function render()
    {
        $this['townDeleteJobForm']->render();
    }

    /**
     * @param int $townId
     * @param int $jobId
     */
    public function handleTownDeleteJob($townId, $jobId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        $this['townDeleteJobForm']->setDefaults(
            [
                'townId' => $townId,
                'jobId' => $jobId
            ]
        );

        $jobFilter = $this->jobFilter;

        $jobModalItem = $this->jobFacade->select()->getCachedManager()->getByPrimaryKey($jobId);

        $presenter->template->modalName = 'townDeleteJob';
        $presenter->template->jobModalItem = $jobFilter($jobModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentTownDeleteJobForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'townDeleteJobFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('townId');
        $form->addHidden('jobId');


        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function townDeleteJobFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Town:edit', $presenter->getParameter('id'));
        }

        try {
            $this->jobManager->delete()->deleteByPrimaryKey($values->jobId);

            $jobs = $this->jobManager->select()->getSettingsCachedManager()->getByTownId($values->townId);

            $presenter->template->jobs = $jobs;

            $presenter->payload->showModal = false;

            $presenter->flashMessage('job_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('jobs');
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
