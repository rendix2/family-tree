<?php
/**
 *
 * Created by PhpStorm.
 * Filename: HistoryNote.php
 * User: Tomáš Babický
 * Date: 16.09.2020
 * Time: 2:01
 */

namespace Rendix2\FamilyTree\App\Presenters;

use Dibi\DateTime;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Rendix2\FamilyTree\App\Filters\HistoryNoteFilter;
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\HistoryNoteForm;
use Rendix2\FamilyTree\App\Managers\NoteHistoryManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\HistoryNoteFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\HistoryNote\HistoryNoteDeleteHistoryNoteFromEditModal;
use Rendix2\FamilyTree\App\Presenters\Traits\HistoryNote\HistoryNoteDeleteHistoryNoteFromListModal;

/**
 * Class HistoryNotePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class HistoryNotePresenter extends BasePresenter
{
    use HistoryNoteDeleteHistoryNoteFromEditModal;
    use HistoryNoteDeleteHistoryNoteFromListModal;

    /**
     * @var HistoryNoteFacade
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
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonFilter $personFilter
     */
    private $personFilter;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * HistoryNotePresenter constructor.
     *
     * @param HistoryNoteFacade $historyNoteFacade
     * @param NoteHistoryManager $historyNoteManager
     * @param PersonManager $personManager
     * @param PersonSettingsManager $personSettingsManager
     */
    public function __construct(
        HistoryNoteFacade $historyNoteFacade,
        HistoryNoteFilter $historyNoteFilter,
        NoteHistoryManager $historyNoteManager,
        PersonFilter $personFilter,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager
    ) {
        parent::__construct();

        $this->historyNoteFacade = $historyNoteFacade;

        $this->historyNoteFilter = $historyNoteFilter;
        $this->personFilter = $personFilter;

        $this->historyNoteManager = $historyNoteManager;
        $this->personManager = $personManager;

        $this->personSettingsManager = $personSettingsManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $notesHistory = $this->historyNoteFacade->getAllCached();

        $this->template->notesHistory = $notesHistory;

        $this->template->addFilter('person', $this->personFilter);
    }

    /**
     * @param int $id personId
     */
    public function actionApplyNote($id)
    {
        $note = $this->historyNoteManager->getByPrimaryKeyCached($id);

        if (!$note) {
            $this->error('Item not found.');
        }

        $this->personManager->updateByPrimaryKey($id, ['note' => $note->text]);

        $this->flashMessage('history_note_saved', self::FLASH_SUCCESS);

        $this->redirect('Person:edit', $id);
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $persons = $this->personSettingsManager->getAllPairsCached($this->translator);

        $this['historyNoteForm-personId']->setItems($persons);

        if ($id !== null) {
            $historyNote = $this->historyNoteFacade->getByPrimaryKeyCached($id);

            if (!$historyNote) {
                $this->error('Item not found.');
            }

            $this['historyNoteForm']->setDefaults((array) $historyNote);
        }
    }

    /**
     * @return Form
     */
    public function createComponentHistoryNoteForm()
    {
        $formFactory = new HistoryNoteForm($this->translator);

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'historyNoteFormSuccess'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function historyNoteFormSuccess(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->historyNoteManager->updateByPrimaryKey($id, $values);

            $this->flashMessage('history_note_saved', self::FLASH_SUCCESS);
        } else {
            $id = $this->historyNoteManager->add($values);

            $this->flashMessage('history_note_added', self::FLASH_SUCCESS);
        }

        $this->redirect('HistoryNote:edit', $id);
    }

    /**
     * @param SubmitButton $submitButton
     * @param ArrayHash $values
     */
    public function useNote(SubmitButton $submitButton, ArrayHash $values)
    {
        $id = $this->presenter->getParameter('id');

        $note = $this->historyNoteManager->getByPrimaryKey($id);

        if ($note->text !== $values->text) {
            $historyNoteData = [
                'personId' => $id,
                'text'     => $values->text,
                'date'     => new DateTime()
            ];

            $this->historyNoteManager->add($historyNoteData);
        }

        $this->personManager->updateByPrimaryKey($id, ['note' => $values->text]);

        $this->flashMessage('history_note_saved', self::FLASH_SUCCESS);

        $this->redirect('Person:edit', $id);
    }
}
