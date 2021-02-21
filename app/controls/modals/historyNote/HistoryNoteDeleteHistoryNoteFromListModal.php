<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteDeleteHistoryNoteFromListModal.php
 * User: Tomáš Babický
 * Date: 21.02.2021
 * Time: 1:48
 */

namespace Rendix2\FamilyTree\App\Controls\Modals\HistoryNote;

use Dibi\ForeignKeyConstraintViolationException;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

class HistoryNoteDeleteHistoryNoteFromListModal extends \Nette\Application\UI\Control
{
    /**
     * @param int $historyNoteId
     */
    public function handleHistoryNoteDeleteHistoryNoteFromList($historyNoteId)
    {
        if ($this->isAjax()) {
            $this['historyNoteDeleteHistoryNoteFromListForm']->setDefaults(['historyNoteId' => $historyNoteId]);

            $historyNoteModalItem = $this->historyNoteFacade->getByPrimaryKey($historyNoteId);

            $historyNoteFilter = $this->historyNoteFilter;

            $this->template->modalName = 'historyNoteDeleteHistoryNoteFromList';
            $this->template->historyNoteModalItem = $historyNoteFilter($historyNoteModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentHistoryNoteDeleteHistoryNoteFromListForm()
    {
        $formFactory = new DeleteModalForm($this->translator);

        $form = $formFactory->create([$this, 'historyNoteDeleteHistoryNoteFromListFormYesOnClick']);
        $form->addHidden('historyNoteId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function historyNoteDeleteHistoryNoteFromListFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
    {
        try {
            $this->historyNoteManager->deleteByPrimaryKey($values->historyNoteId);

            $this->flashMessage('history_note_deleted', self::FLASH_SUCCESS);

            $this->redrawControl('list');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $this->flashMessage('Item has some unset relations', self::FLASH_DANGER);
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        } finally {
            $this->redrawControl('flashes');
        }
    }
}