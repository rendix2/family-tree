<?php
/**
 *
 * Created by PhpStorm.
 * Filename: AddressDeleteAddressEditModal.php
 * User: Tomáš Babický
 * Date: 16.11.2020
 * Time: 21:12
 */

namespace Rendix2\FamilyTree\App\Presenters\Traits\HistoryNote;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\HistoryNoteFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Filters\WeddingFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait HistoryNoteEditDeleteModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\HistoryNote
 */
trait HistoryNoteEditDeleteModal
{
    /**
     * @param int $historyNoteId
     */
    public function handleEditDelete($historyNoteId)
    {
        if ($this->isAjax()) {
            $this['editDeleteForm']->setDefaults(['historyNoteId' => $historyNoteId]);

            $historyNoteModalItem = $this->historyNoteFacade->getByPrimaryKey($historyNoteId);

            $historyNoteFilter = new HistoryNoteFilter();

            $this->template->modalName = 'editDelete';
            $this->template->historyNoteModalItem = $historyNoteFilter($historyNoteModalItem);

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

        $form->addHidden('historyNoteId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function editDeleteFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->historyNoteManager->deleteByPrimaryKey($values->historyNoteId);

            $this->flashMessage('history_note_deleted', self::FLASH_SUCCESS);

            $this->redirect('HistoryNote:default');
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
