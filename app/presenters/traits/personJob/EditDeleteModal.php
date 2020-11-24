<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonDeleteEditModal.php
 * User: Tomáš Babický
 * Date: 06.11.2020
 * Time: 1:13
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\PersonJob;


use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\AddressFilter;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait PersonDeleteEditModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\PersonJob
 */
trait EditDeleteModal
{
    /**
     * @param int $personId
     * @param int $jobId
     */
    public function handleEditDeleteItem($personId, $jobId)
    {
        if ($this->isAjax()) {
            $this['editDeleteForm']->setDefaults(
                [
                    'personId' => $personId,
                    'jobId' => $jobId
                ]
            );

            $jobFilter = new JobFilter();
            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

            $this->template->modalName = 'editDeleteItem';
            $this->template->jobModalItem = $jobFilter($jobModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

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

        $form = $formFactory->create($this, 'editDeleteFormOk', true);
        $form->addHidden('personId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function editDeleteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->person2JobManager->deleteByLeftIdAndRightId($values->personId, $values->jobId);

            $this->flashMessage('person_job_was_deleted', self::FLASH_SUCCESS);

            $this->redirect('PersonJob:default');
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
