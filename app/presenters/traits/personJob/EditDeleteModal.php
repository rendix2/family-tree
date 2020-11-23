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


use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

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
        $this['editDeleteForm']->setDefaults(
            [
                'personId' => $personId,
                'jobId' => $jobId
            ]
        );

        $this->template->modalName = 'editDeleteItem';

        if ($this->isAjax()) {
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
        $this->manager->deleteByLeftIdAndRightId($values->personId, $values->jobId);

        $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

        $this->redirect(':default');
    }
}
