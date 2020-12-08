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
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Trait HistoryNoteDeleteHistoryNoteFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Presenters\Traits\HistoryNote
 */
trait HistoryNoteDeleteHistoryNoteFromEditModal
{
    /**
     * @param int $historyNoteId
     */
    public function handleHistoryNoteDeleteHistoryNoteFromEdit($historyNoteId)
    {
        if ($this->isAjax()) {
            $this['historyNoteDeleteHistoryNoteFromEditForm']->setDefaults(['historyNoteId' => $historyNoteId]);

            $historyNoteModalItem = $this->historyNoteFacade->getByPrimaryKey($historyNoteId);

            $historyNoteFilter = new HistoryNoteFilter();

            $this->template->modalName = 'historyNoteDeleteHistoryNoteFromEdit';
            $this->template->historyNoteModalItem = $historyNoteFilter($historyNoteModalItem);

            $this->payload->showModal = true;

            $this->redrawControl('modal');
        }
    }

    /**
     * @return Form
     */
    protected function createComponentHistoryNoteDeleteHistoryNoteFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->getTranslator());
        $form = $formFactory->create([$this, 'historyNoteDeleteHistoryNoteFromEditFormYesOnClick'], true);

        $form->addHidden('historyNoteId');

        return $form;
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function historyNoteDeleteHistoryNoteFromEditFormYesOnClick(SubmitButton $submitButton, ArrayHash $values)
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
