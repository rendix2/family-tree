<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressFromListModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:16
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
 * Class JobDeleteJobFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\Job
 */
class JobDeleteJobFromListModal extends Control
{
    /**
     * @param int $jobId
     */
    public function handleJobDeleteJobFromList($jobId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['jobDeleteJobFromListForm']->setDefaults(['jobId' => $jobId]);

            $jobFilter = $this->jobFilter;

            $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

            $presenter->template->modalName = 'jobDeleteJobFromList';
            $presenter->template->jobModalItem = $jobFilter($jobModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentJobDeleteJobFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'jobDeleteJobFromListFormYesOnClick']);
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function jobDeleteJobFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        try {
            $this->jobManager->deleteByPrimaryKey($values->jobId);

            $presenter->flashMessage('job_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redrawControl('list');
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