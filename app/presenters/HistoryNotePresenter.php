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
use Rendix2\FamilyTree\App\Controls\Forms\HistoryNoteForm;
use Rendix2\FamilyTree\App\Controls\Modals\HistoryNote\Container\HistoryNoteModalContainer;
use Rendix2\FamilyTree\App\Controls\Modals\HistoryNote\HistoryNoteDeleteHistoryNoteFromEditModal;
use Rendix2\FamilyTree\App\Controls\Modals\HistoryNote\HistoryNoteDeleteHistoryNoteFromListModal;

use Rendix2\FamilyTree\App\Managers\NoteHistoryManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Managers\PersonSettingsManager;
use Rendix2\FamilyTree\App\Model\Facades\HistoryNoteFacade;

/**
 * Class HistoryNotePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class HistoryNotePresenter extends BasePresenter
{
    /**
     * @var HistoryNoteFacade
     */
    private $historyNoteFacade;

    /**
     * @var HistoryNoteForm $historyNoteForm
     */
    private $historyNoteForm;

    /**
     * @var HistoryNoteModalContainer $historyNoteModalContainer
     */
    private $historyNoteModalContainer;

    /**
     * @var NoteHistoryManager $historyNoteManager
     */
    private $historyNoteManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * @var PersonSettingsManager $personSettingsManager
     */
    private $personSettingsManager;

    /**
     * HistoryNotePresenter constructor.
     *
     * @param HistoryNoteFacade         $historyNoteFacade
     * @param HistoryNoteForm           $historyNoteForm
     * @param HistoryNoteModalContainer $historyNoteModalContainer
     * @param NoteHistoryManager        $historyNoteManager
     * @param PersonManager             $personManager
     * @param PersonSettingsManager     $personSettingsManager
     */
    public function __construct(
        HistoryNoteFacade $historyNoteFacade,
        HistoryNoteForm $historyNoteForm,
        HistoryNoteModalContainer $historyNoteModalContainer,
        NoteHistoryManager $historyNoteManager,
        PersonManager $personManager,
        PersonSettingsManager $personSettingsManager
    ) {
        parent::__construct();

        $this->historyNoteFacade = $historyNoteFacade;
        $this->historyNoteForm = $historyNoteForm;

        $this->historyNoteModalContainer = $historyNoteModalContainer;

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
        $form = $this->historyNoteForm->create();

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

        $note = $this->historyNoteManager->getByPrimaryKeyCached($id);

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

    /**
     * @return HistoryNoteDeleteHistoryNoteFromEditModal
     */
    protected function createComponentHistoryNoteDeleteHistoryNoteFromEditModal()
    {
        return $this->historyNoteModalContainer->getHistoryNoteDeleteHistoryNoteFromEditModalFactory()->create();
    }

    /**
     * @return HistoryNoteDeleteHistoryNoteFromListModal
     */
    protected function createComponentHistoryNoteDeleteHistoryNoteFromListModal()
    {
        return $this->historyNoteModalContainer->getHistoryNoteDeleteHistoryNoteFromListModalFactory()->create();
    }
}
