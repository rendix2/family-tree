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
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\HistoryNoteFilter;
use Rendix2\FamilyTree\App\Forms\DeleteModalForm;
use Rendix2\FamilyTree\App\Managers\NoteHistoryManager;
use Rendix2\FamilyTree\App\Model\Facades\HistoryNoteFacade;
use Rendix2\FamilyTree\App\Presenters\BasePresenter;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class HistoryNoteDeleteHistoryNoteFromListModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\HistoryNote
 */
class HistoryNoteDeleteHistoryNoteFromListModal extends Control
{
    /**
     * @var HistoryNoteFacade $historyNoteFacade
     */
    private $historyNoteFacade;

    /**
     * @var HistoryNoteFilter $historyNoteFilter
     */
    private $historyNoteFilter;

    /**
     * @var NoteHistoryManager $historyNoteManager
     */
    private $historyNoteManager;

    /**
     * @var ITranslator $translator
     */
    private $translator;

    /**
     * HistoryNoteDeleteHistoryNoteFromListModal constructor.
     *
     * @param HistoryNoteFacade $historyNoteFacade
     * @param HistoryNoteFilter $historyNoteFilter
     * @param NoteHistoryManager $historyNoteManager
     * @param ITranslator $translator
     */
    public function __construct(
        HistoryNoteFacade $historyNoteFacade,
        HistoryNoteFilter $historyNoteFilter,
        NoteHistoryManager $historyNoteManager,
        ITranslator $translator
    ) {
        parent::__construct();

        $this->historyNoteFacade = $historyNoteFacade;
        $this->historyNoteFilter = $historyNoteFilter;
        $this->historyNoteManager = $historyNoteManager;
        $this->translator = $translator;
    }

    public function render()
    {
        $this['historyNoteDeleteHistoryNoteFromListForm']->render();
    }

    /**
     * @param int $historyNoteId
     */
    public function handleHistoryNoteDeleteHistoryNoteFromList($historyNoteId)
    {
        $presenter = $this->presenter;

        if ($presenter->isAjax()) {
            $this['historyNoteDeleteHistoryNoteFromListForm']->setDefaults(['historyNoteId' => $historyNoteId]);

            $historyNoteModalItem = $this->historyNoteFacade->getByPrimaryKey($historyNoteId);

            $historyNoteFilter = $this->historyNoteFilter;

            $presenter->template->modalName = 'historyNoteDeleteHistoryNoteFromList';
            $presenter->template->historyNoteModalItem = $historyNoteFilter($historyNoteModalItem);

            $presenter->payload->showModal = true;

            $presenter->redrawControl('modal');
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
        $presenter = $this->presenter;

        try {
            $this->historyNoteManager->deleteByPrimaryKey($values->historyNoteId);

            $presenter->flashMessage('history_note_deleted', BasePresenter::FLASH_SUCCESS);

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
