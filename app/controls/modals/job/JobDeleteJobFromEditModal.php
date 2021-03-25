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
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
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
     * @param int $jobId
     */
    public function handleJobDeleteJobFromEdit($jobId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['jobDeleteJobFromEditForm']->setDefaults(['jobId' => $jobId]);

            $jobFilter = $this->jobFilter;
            
            $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

            $this->template->modalName = 'jobDeleteJobFromEdit';
            $this->template->jobModalItem = $jobFilter($jobModalItem);

            $presenter->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentJobDeleteJobFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'jobDeleteJobFromEditFormYesOnClick'], true);
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

        try {
            $this->jobManager->deleteByPrimaryKey($values->jobId);

            $this->flashMessage('job_deleted', BasePresenter::FLASH_SUCCESS);

            $this->redirect('Job:default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);

                $this->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
