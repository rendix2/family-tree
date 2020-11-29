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
use Rendix2\FamilyTree\App\Filters\PersonFilter;
use Rendix2\FamilyTree\App\Forms\HistoryNoteForm;
use Rendix2\FamilyTree\App\Managers\NoteHistoryManager;
use Rendix2\FamilyTree\App\Managers\PersonManager;
use Rendix2\FamilyTree\App\Model\Facades\HistoryNoteFacade;
use Rendix2\FamilyTree\App\Presenters\Traits\HistoryNote\HistoryNoteEditDeleteModal;
use Rendix2\FamilyTree\App\Presenters\Traits\HistoryNote\HistoryNoteListDeleteModal;

/**
 * Class HistoryNotePresenter
 *
 * @package Rendix2\FamilyTree\App\Presenters
 */
class HistoryNotePresenter extends BasePresenter
{
    use HistoryNoteEditDeleteModal;
    use HistoryNoteListDeleteModal;

    /**
     * @var HistoryNoteFacade
     */
    private $historyNoteFacade;

    /**
     * @var NoteHistoryManager $historyNoteManager
     */
    private $historyNoteManager;

    /**
     * @var PersonManager $personManager
     */
    private $personManager;

    /**
     * HistoryNotePresenter constructor.
     *
     * @param HistoryNoteFacade $historyNoteFacade
     * @param NoteHistoryManager $historyNoteManager
     * @param PersonManager $personManager
     */
    public function __construct(
        HistoryNoteFacade $historyNoteFacade,
        NoteHistoryManager $historyNoteManager,
        PersonManager $personManager
    ) {
        parent::__construct();

        $this->historyNoteFacade = $historyNoteFacade;
        $this->historyNoteManager = $historyNoteManager;
        $this->personManager = $personManager;
    }

    /**
     * @return void
     */
    public function renderDefault()
    {
        $notesHistory = $this->historyNoteFacade->getAllCached();

        $this->template->notesHistory = $notesHistory;

        $this->template->addFilter('person', new PersonFilter($this->getTranslator(), $this->getHttpRequest()));
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

        $this->flashMessage('item_updated', self::FLASH_SUCCESS);

        $this->redirect('Person:edit', $id);
    }

    /**
     * @param int|null $id
     */
    public function actionEdit($id = null)
    {
        $persons = $this->personManager->getAllPairsCached($this->getTranslator());

        $this['form-personId']->setItems($persons);

        if ($id !== null) {
            $historyNote = $this->historyNoteFacade->getByPrimaryKeyCached($id);

            if (!$historyNote) {
                $this->error('Item not found.');
            }

            $this['form']->setDefaults((array)$historyNote);
        }
    }

    /**
     * @return Form
     */
    public function createComponentForm()
    {
        $formFactory = new HistoryNoteForm($this->getTranslator());

        $form = $formFactory->create();
        $form->onSuccess[] = [$this, 'saveForm'];

        return $form;
    }

    /**
     * @param Form $form
     * @param ArrayHash $values
     */
    public function saveForm(Form $form, ArrayHash $values)
    {
        $id = $this->getParameter('id');

        if ($id) {
            $this->historyNoteManager->updateByPrimaryKey($id, $values);
            $this->flashMessage('item_updated', self::FLASH_SUCCESS);
        } else {
            $id = $this->historyNoteManager->add($values);
            $this->flashMessage('item_added', self::FLASH_SUCCESS);
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

        $this->flashMessage('item_updated', self::FLASH_SUCCESS);

        $this->redirect('Person:edit', $id);
    }
}