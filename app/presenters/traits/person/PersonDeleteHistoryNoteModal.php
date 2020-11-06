<?php
/**
 *
 * Created by PhpStorm.
 * Filename: PersonHistoryNoteDeleteModal.php
 * User: Tomáš Babický
 * Date: 31.10.2020
 * Time: 2:43
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\Person;

use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;

/**
 * Trait PersonHistoryNoteDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\Person
 */
trait PersonDeleteHistoryNoteModal
{
    /**
     * @param int $personId
     * @param int $historyNoteId
     */
    public function handleDeleteHistoryNoteItem($personId, $historyNoteId)
    {
        $this->template->modalName = 'deleteHistoryNoteItem';

        $this['deletePersonHistoryNoteForm']->setDefaults(
            [
                'personId' => $personId,
                'historyNoteId' => $historyNoteId
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
    protected function createComponentDeletePersonHistoryNoteForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create($this, 'deletePersonHistoryNoteFormOk');

        $form->addHidden('personId');
        $form->addHidden('historyNoteId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function deletePersonHistoryNoteFormOk(SubmitButton $submitButton, ArrayHash $values)
    {
        if ($this->isAjax()) {
            $this->historyNoteManager->deleteByPrimaryKey($values->historyNoteId);

            $historyNotes = $this->historyNoteManager->getByPerson($values->personId);

            $this->template->historyNotes = $historyNotes;
            $this->template->modalName = 'deleteHistoryNoteItem';

            $this->payload->showModal = false;

            $this->flashMessage('item_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('modal');
            $this->redrawControl('flashes');
            $this->redrawControl('history_notes');
        } else {
            $this->redirect(':edit', $values->personId);
        }
    }
}