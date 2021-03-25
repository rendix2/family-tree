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
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class PersonJobDeletePersonJobFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\PersonJob
 */
class PersonJobDeletePersonJobFromEditModal extends \Nette\Application\UI\Control
{
    /**
     * @param int $personId
     * @param int $jobId
     */
    public function handlePersonJobDeletePersonJobFromEdit($personId, $jobId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
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

            $this->template->modalName = 'personJobDeletePersonJobFromEdit';
            $this->template->jobModalItem = $jobFilter($jobModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $presenter->payload->showModal = true;

            $this->redrawControl('modal');
        }
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

        try {
            $this->person2JobManager->deleteByLeftIdAndRightId($values->personId, $values->jobId);

            $this->flashMessage('person_job_deleted', BasePresenter::FLASH_SUCCESS);

            $this->redirect('PersonJob:default');
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
