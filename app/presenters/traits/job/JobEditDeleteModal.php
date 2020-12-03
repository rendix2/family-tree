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
 * Trait JobEditDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Job
 */
trait JobEditDeleteModal
{
    /**
     * @param int $jobId
     */
    public function handleEditDelete($jobId)
    {
        if ($this->isAjax()) {
            $this['editDeleteForm']->setDefaults(['jobId' => $jobId]);

            $jobFilter = new JobFilter();
            
            $jobModalItem = $this->jobFacade->getByPrimaryKey($jobId);

            $this->template->modalName = 'editDelete';
            $this->template->jobModalItem = $jobFilter($jobModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentEditDeleteForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create([$this, 'editDeleteFormYesOnClick'], true);
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function editDeleteFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->jobManager->deleteByPrimaryKey($values->jobId);

            $this->flashMessage('job_was_deleted', self::FLASH_SUCCESS);

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
