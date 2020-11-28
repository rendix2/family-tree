<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonJobDeleteModal.php
 * User: Tomáš Babický
 * Date: 26.10.2020
 * Time: 1:25
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\JobFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonJobDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeletePersonJobModal
{
    /**
     * @param int $personId
     * @param int $jobId
     */
    public function handleDeletePersonJobItem($personId, $jobId)
    {
        if ($this->isAjax()) {
            $this['deletePersonJobForm']->setDefaults(
                [
                    'personId' => $personId,
                    'jobId' => $jobId
                ]
            );

            $personFilter = new PersonFilter($this->getTranslator(), $this->getHttpRequest());
            $jobFilter = new JobFilter();

            $personModalItem = $this->personFacade->getByPrimaryKey($personId);
            $jobModalItem = $this->jobManager->getByPrimaryKey($jobId);

            $this->template->modalName = 'deletePersonJobItem';
            $this->template->jobModalItem = $jobFilter($jobModalItem);
            $this->template->personModalItem = $personFilter($personModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentDeletePersonJobForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());

        $form = $formFactory->create($this, 'deletePersonJobFormOk');
        $form->addHidden('personId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonJobFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->person2JobManager->deleteByLeftIdAndRightId($values->personId, $values->jobId);

            $jobs = $this->person2JobFacade->getByLeft($values->personId);

            $this->template->jobs = $jobs;

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('flashes');
            $this->redrawControl('jobs');
        } else {
            $this->redirect('Person:edit', $values->personId);
        }
    }
}
