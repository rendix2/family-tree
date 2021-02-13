<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Job;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait JobDeleteJobFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Job
 */
trait JobDeleteJobFromEditModal
{
    /**
     * @param int $jobId
     */
    public function handleJobDeleteJobFromEdit($jobId)
    {
        if ($this->isAjax()) {
            $this['jobDeleteJobFromEditForm']->setDefaults(['jobId' => $jobId]);

            $jobFilter = new JobFilter($this->getHttpRequest());
            
            $jobModalItem = $this->jobFacade->getByPrimaryKey($jobId);

            $this->template->modalName = 'jobDeleteJobFromEdit';
            $this->template->jobModalItem = $jobFilter($jobModalItem);

            $this->payload->showModal = true;

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
        try {
            $this->jobManager->deleteByPrimaryKey($values->jobId);

            $this->flashMessage('job_deleted', self::FLASH_SUCCESS);

            $this->redirect('Job:default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);

                $this->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}
