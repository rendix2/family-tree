<?php

/**
 *
 * Created by PhpStorm.
 * Filename: JobPersonDeleteModal.php
 * User: Tomáš Babický
 * Date: 28.10.2020
 * Time: 16:42
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Job;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait JobPersonDeleteModal
 */
trait JobPersonDeleteModal
{
    /**
     * @param int $jobId
     * @param int $personId
     */
    public function handleDeletePersonItem($personId, $jobId)
    {
        $this->template->modalName = 'deletePersonItem';

        $this['deleteJobPersonForm']->setDefaults(
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
    protected function createComponentDeleteJobPersonForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deleteJobPersonFormOk');

        $form->addHidden('personId');
        $form->addHidden('jobId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deleteJobPersonFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->person2JobManager->deleteByLeftIdAndRightId($values->personId, $values->jobId);

            $persons = $this->person2JobManager->getAllByRightJoined($values->jobId);

            $this->template->persons = $persons;
            $this->template->modalName = 'deletePersonItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('persons');
        } else {
            $this->redirect(':edit', $values->jobId);
        }
    }
}
