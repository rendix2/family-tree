<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNoteDeleteHistoryNoteFromEditModal.php
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
 * Class HistoryNoteDeleteHistoryNoteFromEditModal
 *
 * @package Rendix2\FamilyTree\App\Controls\Modals\HistoryNote
 */
class HistoryNoteDeleteHistoryNoteFromEditModal extends Control
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
     * HistoryNoteDeleteHistoryNoteFromEditModal constructor.
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
        $this['historyNoteDeleteHistoryNoteFromEditForm']->render();
    }

    /**
     * @param int $historyNoteId
     */
    public function handleHistoryNoteDeleteHistoryNoteFromEdit($historyNoteId)
    {
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('HistoryNote:edit', $presenter->getParameter('id'));
        }

        $this['historyNoteDeleteHistoryNoteFromEditForm']->setDefaults(['historyNoteId' => $historyNoteId]);

        $historyNoteModalItem = $this->historyNoteFacade->getByPrimaryKey($historyNoteId);

        $historyNoteFilter = $this->historyNoteFilter;

        $presenter->template->modalName = 'historyNoteDeleteHistoryNoteFromEdit';
        $presenter->template->historyNoteModalItem = $historyNoteFilter($historyNoteModalItem);

        $presenter->payload->showModal = true;

        $presenter->redrawControl('modal');
    }

    /**
     * @return Form
     */
    protected function createComponentHistoryNoteDeleteHistoryNoteFromEditForm()
    {
        $formFactory = new DeleteModalForm($this->translator);
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
        $presenter = $this->presenter;

        if (!$presenter->isAjax()) {
            $presenter->redirect('HistoryNote:edit', $presenter->getParameter('id'));
        }

        try {
            $this->historyNoteManager->deleteByPrimaryKey($values->historyNoteId);

            $presenter->flashMessage('history_note_deleted', BasePresenter::FLASH_SUCCESS);

            $presenter->redirect('HistoryNote:default');
        } catch (ForeignKeyConstraintViolationException $e) {
            if ($e->getCode() === 1451) {
                $presenter->flashMessage('Item has some unset relations', BasePresenter::FLASH_DANGER);

                $presenter->redrawControl('flashes');
            } else {
                Debugger::log($e, ILogger::EXCEPTION);
            }
        }
    }
}