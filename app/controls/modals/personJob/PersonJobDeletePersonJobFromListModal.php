<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJobDeletePersonJobFromListModalFactory.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:56
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\PersonJob;

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
 * Class PersonJobDeletePersonJobFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\PersonJob
 */
class PersonJobDeletePersonJobFromListModal extends Control
{
    /**
     * @param int $personId
     * @param int $jobId
     */
    public function handlePersonJobDeletePersonJobFromList($personId, $jobId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['personJobDeletePersonJobFromListForm']->setDefaults(
                [
                    'personId' => $personId,
                    'jobId' => $jobId
                ]
            );

            $jobFilter = $this->jobFilter;
            $personFilter = $this->personFilter;

            $personModalItem = $this->personFacade->getByPrimaryKeyCached($personId);
            $jobModalItem = $this->jobFacade->getByPrimaryKeyCached($jobId);

            $presenter->template->modalName = 'personJobDeletePersonJobFromList';
            $presenter->template->jobModalItem = $jobFilter($jobModalItem);
            $presenter->template->personModalItem = $personFilter($personModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentPersonJobDeletePersonJobFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'personJobDeletePersonJobFromListFormYesOnClick']);
        $form->addHidden('personId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function personJobDeletePersonJobFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        $presenter = $this->presenter;

        try {
            $this->person2JobManager->deleteByLeftIdAndRightId($values->personId, $values->jobId);

            $presenter->flashMessage('person_job_deleted', BasePresenter::FLASH_SUCCESS);

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