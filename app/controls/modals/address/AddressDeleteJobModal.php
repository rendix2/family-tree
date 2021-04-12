<?php
/**
 *
 * Created by PhpStorm.
 * Filename: TownDeleteJobModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 1:30
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\Address;

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
 * Class AddressDeleteJobModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Address
 */
class AddressDeleteJobModal extends Control
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
     * @var DeleteModalForm $deleteModalForm
     */
    private $deleteModalForm;

    /**
     * @var JobManager $jobManager
     */
    private $jobManager;

    /**
     * AddressDeleteJobModal constructor.
     *
     * @param DeleteModalForm $deleteModalForm
     * @param JobFacade       $jobFacade
     * @param JobFilter       $jobFilter
     * @param JobManager      $jobManager
     */
    public function __construct(
        DeleteModalForm $deleteModalForm,
        JobFacade $jobFacade,
        JobFilter $jobFilter,
        JobManager $jobManager
    ) {
        parent::__construct();

        $this->jobFacade = $jobFacade;

        $this->jobFilter = $jobFilter;

        $this->deleteModalForm = $deleteModalForm;

        $this->jobManager = $jobManager;
    }

    public function render()
    {
        $this['addressDeleteJobForm']->render();
    }

    /**
     * @param int $addressId
     * @param int $jobId
     */
    public function handleAddressDeleteJob($addressId, $jobId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        $this['addressDeleteJobForm']->setDefaults(
            [
                'addressId' => $addressId,
                'jobId' => $jobId
            ]
        );

        $jobFilter = $this->jobFilter;

        $jobModalItem = $this->jobFacade->select()->getCachedManager()->getByPrimaryKey($jobId);

        $presenter->template->modalName = 'addressDeleteJob';
        $presenter->template->jobModalItem = $jobFilter($jobModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentAddressDeleteJobForm()
    {
        $deleteModalFormSettings = new DeleteModalFormSettings();
        $deleteModalFormSettings->callBack = [$this, 'addressDeleteJobFormYesOnClick'];

        $form = $this->deleteModalForm->create($deleteModalFormSettings);

        $form->addHidden('addressId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function addressDeleteJobFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('Address:edit', $presenter->getParameter('id'));
        }

        try {
            $this->jobManager->delete()->deleteByPrimaryKey($values->jobId);

            $jobs = $this->jobManager->select()->getManager()->getByAddressId($values->addressId);

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
