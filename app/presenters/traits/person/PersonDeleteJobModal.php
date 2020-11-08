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
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonJobDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteJobModal
{
    /**
     * @param int $personId
     * @param int $jobId
     */
    public function handleDeleteJobItem($personId, $jobId)
    {
        $this->template->modalName = 'deleteJobItem';

        $this['deletePersonJobForm']->setDefaults(
            [
                'personId' => $personId,
                'jobId' => $jobId
            ]
        );

        if ($this->isAjax()) {
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

            $this->payload->showModal = false;

            $jobs = $this->person2JobManager->getAllByLeftJoined($values->personId);

            $this->template->jobs = $jobs;
            $this->template->modalName = 'deleteJobItem';

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('jobs');
        } else {
            $this->redirect(':edit', $values->personId);
        }
    }
}
